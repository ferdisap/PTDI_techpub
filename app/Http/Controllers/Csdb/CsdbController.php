<?php

namespace App\Http\Controllers\Csdb;

use App\Http\Controllers\Controller;
use App\Http\Requests\Csdb\CsdbCreateByXMLEditor;
use App\Http\Requests\Csdb\CsdbUpdateByXMLEditor;
use App\Models\Csdb;
use App\Rules\Csdb\Path as PathRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Ptdi\Mpub\Fop\Fop;
use Ptdi\Mpub\Main\CSDBError;
use Ptdi\Mpub\Main\CSDBObject;
use Ptdi\Mpub\Main\CSDBStatic;
use Ptdi\Mpub\Main\Helper;
use SimpleXMLElement;

class CsdbController extends Controller
{
  public function app()
  {
    return view('csdb4.app');
  }

  public function create(CsdbCreateByXMLEditor $request)
  {
    $CSDBModel = new Csdb();
    $CSDBModel->CSDBObject = $request->validated()['xmleditor'][0];
    $CSDBModel->filename = $CSDBModel->CSDBObject->filename;
    $CSDBModel->path = $request->validated()['path'];
    $CSDBModel->initiator_id = $request->user()->id;
    $CSDBModel->appendAvailableStorage($request->user()->storage);
    if($CSDBModel->saveDOMandModel($request->user()->storage)){
      $CSDBModel->initiator; // agar ada initiator nya
      return $this->ret2(200, ["New {$CSDBModel->filename} has been created."], ["model" => $CSDBModel], ['infotype' => 'info']);
    }
    return $this->ret2(400, ["{$CSDBModel->filename} failed to create."], CSDBError::getErrors(), ['model' => $CSDBModel]);
  }

  /**
   * jika user mengubah filename, filename akan kembali seperti asalnya karena update akan mengubah seluruhnya selain filename
   * @return Response JSON contain SQL object model with initiator data
   */
  public function update(CsdbUpdateByXMLEditor $request, string $filename)
  {
    $CSDBModel = $request->validated()['oldCSDBModel'][0];
    $CSDBModel->CSDBObject = $request->validated()['xmleditor'][0];
    $CSDBModel->path = $request->validated()['path'];
    if($CSDBModel->saveDOMandModel($request->user()->storage)){
      $CSDBModel->initiator; // agar ada initiator nya
      return $this->ret2(200, ["New {$CSDBModel->filename} has been created."], ["model" => $CSDBModel], ['infotype' => 'info']);
    }
    return $this->ret2(400, ["{$CSDBModel->filename} failed to create."], CSDBError::getErrors(), ['model' => $CSDBModel]);
  }

  public function read_pdf_object(Request $request, Csdb $CSDBModel)
  {
    // $modelIdentCode = Helper::get_attribute_from_filename($CSDBModel->filename, 'modelIdentCode');  
    $modelIdentCode = 'CN235';
    $config = new \DOMDocument();
    $config->validateOnParse = true;
    $config->load(CSDB_VIEW_PATH."/xsl/Config.xml");
    $xpath = new \DOMXPath($config);
    $pathxsl = CSDB_VIEW_PATH."/xsl". "/". $xpath->evaluate("string(//config/output/method[@type='pdf']/path[@product-name='{$modelIdentCode}' or @product-name='*'])");
    
    $storage = $CSDBModel->initiator->storage;
    $CSDBModel->CSDBObject->load(CSDB_STORAGE_PATH. "/" . $storage . "/" . $CSDBModel->filename);
    $CSDBModel->CSDBObject->setConfigXML(CSDB_VIEW_PATH . DIRECTORY_SEPARATOR . "xsl" . DIRECTORY_SEPARATOR . "Config.xml"); // nanti diubah mungkin berbeda antara pdf dan html meskupun harusnya SAMA. Nanti ConfigXML mungkin tidak diperlukan jika fitur BREX sudah siap sepenuhnya.

    CSDBStatic::$footnotePositionStore[$CSDBModel->filename] = [];    

    $transformed = $CSDBModel->CSDBObject->transform_to_xml( $pathxsl, [
      "filename" => $CSDBModel->filename,
      "alertPathBackground" => "file:///".str_replace("\\","/", CSDB_VIEW_PATH."/xsl/pdf/assets"),
    ]);

    $fo = CSDB_VIEW_PATH."/xsl". "/". $xpath->evaluate("string(//method[@type='pdf']/pathCache)")."/".$CSDBModel->filename.".fo";
    if(file_put_contents($fo, $transformed) AND ($pdf = Fop::FO_to_PDF($fo))){
      return Response::make($pdf,200,[
        'Content-Type' => 'application/pdf', 
        'Cache-Control' => 'public',
        'Expires' => now()->add('day', 1),
        'Last-Modified' => $CSDBModel->updated_at
      ]);
    }
    dd($transformed);
    abort(400);
  }

  /**
   * nanti dipakai saat orang ingin cetak PDF/HTML atau sekedar ngecek ketersediaan object di folder mereka masing2
   */
  public function checkAvailableObject(Request $request)
  {
    $dir = scandir(CSDB_STORAGE_PATH."/".$request->user()->storage);
    $dir = array_filter($dir, fn($v) => substr($v,-4,4) === '.xml');
    $analyze = [];
    $unavailable = [];
    foreach($dir as $filename){
      $refObjects = [];
      $CSDBObject = new CSDBObject("5.0");
      $CSDBObject->load(CSDB_STORAGE_PATH."/".$request->user()->storage."/".$filename);
      $domXpath = new \DOMXpath($CSDBObject->document);
      $ref = $domXpath->evaluate("//dmRefIdent|//pmRefIdent|*[@infoEntityIdent]");
      foreach($ref as $el){
        $f = ''; 
        $tagName = $el->tagName;
        switch ($tagName) {
          case 'dmRefIdent':
            $f = CSDBStatic::resolve_dmIdent($el);
            break;          
          case 'pmRefIdent':
            $f = CSDBStatic::resolve_pmIdent($el);
            break;
          default:
            $f = $el->getAttribute('infoEntityIdent');
            break;
        }
        if(!(in_array($f, $dir))) {
          $refObjects[] = $f;
          $unavailable[] = $f;
        };
      }
      if(!empty($refObjects)){
        $analyze[] = [
          'calledin' => $filename,
          'unavailable' => $refObjects,
        ];
      }
    }
    $unavailable = array_unique($unavailable);
    array_walk($unavailable, function(&$v) use($analyze){
      $cin = [];
      foreach($analyze as $a){
        if(in_array($v, $a['unavailable'])){
          $cin[] = $a['calledin'];
        }
      }
      $v = [
        'filename' => $v,
        'calledin' => $cin,
      ];
    }, $analyze);
    return $this->ret2(200, ['analyze' => $analyze, 'unavailable' => $unavailable]);
  }

  ###### semua fungsi lama taruh dibawah ######

    /**
   * tidak bisa pakai fitur search dan tidak pakai pagination karena digunakan untuk ListTree.vue
   * jika $request->('listtree'), return all with only filename and path column
   * notApplicable: jika $request->get('path'), maka query where path like $request->get('path'); return all column
   */
  public function get_allobjects_list(Request $request)
  {
    if($request->get('listtree')){
      return $this->ret2(200, 
      [
        "data" => 
          Csdb::
          where('filename', 'like', 'DMC-%')
          ->orWhere('filename', 'like', 'PMC-%')
          ->orWhere('filename', 'like', 'ICN-%')
          ->get(['filename', 'path', 'updated_at'])
          ->toArray()
      ]);
    }
    $this->model = Csdb::with('initiator');    
    return $this->ret2(200, ['data' => $this->model->get()->toArray()]);
  }

  public function get_object_model(Request $request, string $filename)
  {
    // $model = Csdb::with('initiator')->where('filename', $filename)->first();
    // return $model ? $this->ret2(200, ["model" => $model->toArray()]) : $this->ret2(400, ["no such {$filename} available."]);
    $type = substr($filename, 0,3);
    $model = Csdb::getModel(ucfirst($type));
    $model->setProtected(['with' => 'csdb.initiator']);
    $model = $model->where('filename', $filename)->first();
    // $model = ModelsCsdb::with('initiator')->where('filename', $filename)->first();
    return $model ? $this->ret2(200, ["model" => $model->toArray()]) : $this->ret2(400, ["no such {$filename} available."]);
  }

  public function forfolder_get_allobjects_list(Request $request)
  {
    // validasi. Jadi ketika tidak ada path ataupun sc, ataupun filename (KOSONG) maka akan mencari path = "csdb/"
    if($request->path === "/") $request->merge(['path' => 'csdb']);

    $this->model = Csdb::with('initiator');    
    $res = $this->generateWhereRawQueryString($request->get('sc'));
    $keywords = $res[1];

    // menyiapkan csdb object
    // $this->model->orderBy('filename');
    // $ret = $this->model->paginate(100);
    $ret = $this->model->whereRaw($res[0])->orderBy('filename')->paginate(100);
    $ret->setPath($request->getUri());

    $m = '';
    // menyiapkan folder
    if($ret->isNotEmpty()){
      $minLengthPath = 999;
      if(isset($keywords['path'])){
        $this->model = new Csdb();
        $this->generateWhereRawQueryString($request->get('sc'));
        $folder = $this->model->whereRaw($res[0])->get(['path'])->toArray();
        // $folder = $this->model->get(['path'])->toArray();
        // if(empty($folder)) $m = "Folder can not be loaded"; // sepertinya tidak perlu message karena kalau tidak ada folder lagi, ya tidak perlu di infokan
        $folder = array_unique($folder,SORT_REGULAR);
        $folder = array_map(function($v) use ($keywords, &$minLengthPath){
          $v = join("",$v); // saat didapat dari database, bentuknya array berisi satu path saja
          $minLength = substr_count($v, "/", 0, strlen($v)) + 1; // plus satu karena saat menghitung '/', 'csdb/male/amm' dihitung 2
          if($minLength < $minLengthPath) $minLengthPath = $minLength;
          // foreach($keywords['path'] as $p){$v = preg_replace("/($p)(.+)/",'${2}',$v,1);} // jika $v = 'male/csdb/cn235/csdb/ke1' maka path 'csdb' yang terhapus hanya yang pertama
          // $v = preg_replace("/\/{2,}/", "/", $v); // mengganti multiple slash menjadi '/'; tapi ini kayaknya tidak perlu biar lebih cepat karena ujung2nya cuma diambil 1 folder (tanpa sub-folder nya)
          // if(substr($v,0,1) === '/') $v = substr_replace($v, '', 0,1); // membuang slash di depan
          return $v;
        }, $folder);
        if($minLengthPath === 1) $folder = []; // jika 1 artinya folder cuma 1 level, eg: 'csdb'. Artinya tidak perlu ditampung karena sama dengan request di search
        $folder = array_filter($folder, fn($v) => substr_count($v, "/", 0, strlen($v))+1 === $minLengthPath); // plus satu karena saat menghitung '/', 'csdb/male/amm' dihitung 2
        $folder = array_values($folder); // supaya tidak assoc atau supaya indexnya teratur
        sort($folder);
      }
    } else $m = "CSDB objects can not be found.";

    if(isset($keywords['path']) AND count($keywords['path']) === 1){
      $current_path = $keywords['path'][0];
    }

    return $this->ret2(200, $ret->toArray(), ['message' => $m, 'infotype' => "caution", 'folder' => $folder ?? [], "current_path" => $current_path ?? '']);
  }
}
