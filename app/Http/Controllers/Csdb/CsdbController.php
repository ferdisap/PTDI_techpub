<?php

namespace App\Http\Controllers\Csdb;

use Abordage\LastModified\Facades\LastModified;
use App\Http\Controllers\Controller;
use App\Http\Requests\Csdb\CsdbChangePath;
use App\Http\Requests\Csdb\CsdbCreateByXMLEditor;
use App\Http\Requests\Csdb\CsdbDelete;
use App\Http\Requests\Csdb\CsdbUpdateByXMLEditor;
use App\Http\Requests\Csdb\UploadICN;
use App\Models\Csdb;
use App\Models\Csdb\Dmc;
use App\Rules\Csdb\Path as PathRules;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PrettyXml\Formatter;
use Ptdi\Mpub\Fop\Fop;
use Ptdi\Mpub\Main\CSDBError;
use Ptdi\Mpub\Main\CSDBObject;
use Ptdi\Mpub\Main\CSDBStatic;
use Ptdi\Mpub\Main\CSDBValidator;
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
    $CSDBModel->filename = $CSDBModel->CSDBObject->getFilename();
    $CSDBModel->path = $request->validated()['path'];
    $CSDBModel->initiator_id = $request->user()->id;
    $CSDBModel->appendAvailableStorage($request->user()->storage);
    $this->created_at = now()->toString();
    $this->updated_at = now()->toString();
    if ($CSDBModel->saveDOMandModel($request->user()->storage)) {
      $CSDBModel->initiator; // agar ada initiator nya
      return $this->ret2(200, ["New {$CSDBModel->filename} has been created."], ["csdb" => $CSDBModel], ['infotype' => 'info']);
    }
    return $this->ret2(400, ["{$CSDBModel->filename} failed to create."], CSDBError::getErrors(), ['csdb' => $CSDBModel]);
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
    $this->updated_at = now()->toString();
    if ($CSDBModel->saveDOMandModel($request->user()->storage)) {
      $CSDBModel->initiator; // agar ada initiator nya
      return $this->ret2(200, ["New {$CSDBModel->filename} has been update."], ["csdb" => $CSDBModel], ['infotype' => 'info']);
    }
    return $this->ret2(400, ["{$CSDBModel->filename} failed to update."], CSDBError::getErrors(), ['csdb' => $CSDBModel]);
  }

  public function read_pdf_object(Request $request, Csdb $CSDBModel)
  {
    // $modelIdentCode = Helper::get_attribute_from_filename($CSDBModel->filename, 'modelIdentCode');  
    $modelIdentCode = 'CN235';
    $config = new \DOMDocument();
    $config->validateOnParse = true;
    $config->load(CSDB_VIEW_PATH . "/xsl/Config.xml");
    $xpath = new \DOMXPath($config);
    $fo = CSDB_VIEW_PATH . "/xsl" . "/" . $xpath->evaluate("string(//method[@type='pdf']/pathCache)") . "/" . $CSDBModel->filename . ".fo";
    $response = Response::make();
    // $etag = '"'.md5($fo."___".$CSDBModel->updated_at).'"';
    // $response = Response::make()->setEtag($etag);
    // if(in_array($etag, $request->getEtags())){
    //   return Response::make('',304);
    // }
    $pathxsl = CSDB_VIEW_PATH . "/xsl" . "/" . $xpath->evaluate("string(//config/output/method[@type='pdf']/path[@product-name='{$modelIdentCode}' or @product-name='*'])");

    $storage = $CSDBModel->initiator->storage;
    $CSDBModel->CSDBObject->load(CSDB_STORAGE_PATH . "/" . $storage . "/" . $CSDBModel->filename);
    $CSDBModel->CSDBObject->setConfigXML(CSDB_VIEW_PATH . DIRECTORY_SEPARATOR . "xsl" . DIRECTORY_SEPARATOR . "Config.xml"); // nanti diubah mungkin berbeda antara pdf dan html meskupun harusnya SAMA. Nanti ConfigXML mungkin tidak diperlukan jika fitur BREX sudah siap sepenuhnya.

    CSDBStatic::$footnotePositionStore[$CSDBModel->filename] = [];
    $transformed = $CSDBModel->CSDBObject->transform_to_xml($pathxsl, [
      "filename" => $CSDBModel->filename,
      "alertPathBackground" => "file:///" . str_replace("\\", "/", CSDB_VIEW_PATH . "/xsl/pdf/assets"),
    ]);
    if (file_put_contents($fo, $transformed) and ($pdf = Fop::FO_to_PDF($fo))) {
      $response->header('Content-Type', 'application/pdf');
      return $response->setContent($pdf);
      // return Response::make($pdf,200,[
      //   'Content-Type' => 'application/pdf', 
      //   // 'Cache-Control' => 'no-cache, must-revalidate, max-age=0', // kayaknya ga guna di ETag
      //   // 'Expires' => now()->add('seconds', 20)->toString(), // kayaknya ga guna juga di ETag
      //   // 'ETag' => $etag,
      // ]);
    }
    abort(400);
  }
  public function read_pdf_object_berhasil_cacheing(Request $request, Csdb $CSDBModel)
  {
    // $modelIdentCode = Helper::get_attribute_from_filename($CSDBModel->filename, 'modelIdentCode');  
    $modelIdentCode = 'CN235';
    $config = new \DOMDocument();
    $config->validateOnParse = true;
    $config->load(CSDB_VIEW_PATH . "/xsl/Config.xml");
    $xpath = new \DOMXPath($config);
    $fo = CSDB_VIEW_PATH . "/xsl" . "/" . $xpath->evaluate("string(//method[@type='pdf']/pathCache)") . "/" . $CSDBModel->filename . ".fo";
    $etag = '"' . md5($fo . "___" . $CSDBModel->updated_at) . '"';
    if (in_array($etag, $request->getEtags())) {
      return Response::make('', 304);
    }
    $pathxsl = CSDB_VIEW_PATH . "/xsl" . "/" . $xpath->evaluate("string(//config/output/method[@type='pdf']/path[@product-name='{$modelIdentCode}' or @product-name='*'])");

    $storage = $CSDBModel->initiator->storage;
    $CSDBModel->CSDBObject->load(CSDB_STORAGE_PATH . "/" . $storage . "/" . $CSDBModel->filename);
    $CSDBModel->CSDBObject->setConfigXML(CSDB_VIEW_PATH . DIRECTORY_SEPARATOR . "xsl" . DIRECTORY_SEPARATOR . "Config.xml"); // nanti diubah mungkin berbeda antara pdf dan html meskupun harusnya SAMA. Nanti ConfigXML mungkin tidak diperlukan jika fitur BREX sudah siap sepenuhnya.

    CSDBStatic::$footnotePositionStore[$CSDBModel->filename] = [];
    $transformed = $CSDBModel->CSDBObject->transform_to_xml($pathxsl, [
      "filename" => $CSDBModel->filename,
      "alertPathBackground" => "file:///" . str_replace("\\", "/", CSDB_VIEW_PATH . "/xsl/pdf/assets"),
    ]);
    if (file_put_contents($fo, $transformed) and ($pdf = Fop::FO_to_PDF($fo))) {
      return Response::make($pdf, 200, [
        'Content-Type' => 'application/pdf',
        // 'Cache-Control' => 'no-cache, must-revalidate, max-age=0', // kayaknya ga guna di ETag
        // 'Expires' => now()->add('seconds', 20)->toString(), // kayaknya ga guna juga di ETag
        'ETag' => $etag,
      ]);
    }
    abort(400);
  }
  public function read_html_object(Request $request, Csdb $CSDBModel)
  {
  }

  /**
   * nanti dipakai saat orang ingin cetak PDF/HTML atau sekedar ngecek ketersediaan object di folder mereka masing2
   */
  public function checkAvailableObject(Request $request)
  {
    $dir = scandir(CSDB_STORAGE_PATH . "/" . $request->user()->storage);
    $dir = array_filter($dir, fn ($v) => substr($v, -4, 4) === '.xml');
    $analyze = [];
    $unavailable = [];
    foreach ($dir as $filename) {
      $refObjects = [];
      $CSDBObject = new CSDBObject("5.0");
      $CSDBObject->load(CSDB_STORAGE_PATH . "/" . $request->user()->storage . "/" . $filename);
      $domXpath = new \DOMXpath($CSDBObject->document);
      $ref = $domXpath->evaluate("//dmRefIdent|//pmRefIdent|*[@infoEntityIdent]");
      foreach ($ref as $el) {
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
        if (!(in_array($f, $dir))) {
          $refObjects[] = $f;
          $unavailable[] = $f;
        };
      }
      if (!empty($refObjects)) {
        $analyze[] = [
          'calledin' => $filename,
          'unavailable' => $refObjects,
        ];
      }
    }
    $unavailable = array_unique($unavailable);
    array_walk($unavailable, function (&$v) use ($analyze) {
      $cin = [];
      foreach ($analyze as $a) {
        if (in_array($v, $a['unavailable'])) {
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

  public function get_object_raw(Request $request, Csdb $CSDBModel)
  {
    $CSDBModel->CSDBObject->load(CSDB_STORAGE_PATH . "/" . $request->user()->storage . "/" . $CSDBModel->filename);
    if ($CSDBModel->CSDBObject->document) {
      $formatter = new Formatter();
      return Response::make($formatter->format($CSDBModel->CSDBObject->document->saveXML()), 200, ['Content-Type' => 'text/xml']);
    }
    return $this->ret2(200, "There is no such of {$CSDBModel->filename}", ['headers' => ['Content-Type' => 'text/xml']]);
  }

  public function get_icn_raw(Request $request, Csdb $CSDBModel)
  {
    $CSDBModel->CSDBObject->load(CSDB_STORAGE_PATH . "/" . $request->user()->storage . "/" . $CSDBModel->filename);
    return Response::make($CSDBModel->CSDBObject->document->getFile(), 200, [
      'Content-Type' => $CSDBModel->CSDBObject->document->getFileinfo()['mime_type'],
    ]);
  }

  ###### semua fungsi lama taruh dibawah ######

  /**
   * tidak bisa pakai fitur search dan tidak pakai pagination karena digunakan untuk ListTree.vue
   * jika $request->('listtree'), return all with only filename and path column
   * notApplicable: jika $request->get('path'), maka query where path like $request->get('path'); return all column
   */
  public function get_allobjects_list(Request $request)
  {
    if ($request->get('listtree')) {
      return $this->ret2(
        200,
        [
          "data" =>
          Csdb::where('filename', 'like', 'DMC-%')
            ->orWhere('filename', 'like', 'PMC-%')
            ->orWhere('filename', 'like', 'ICN-%')
            ->get(['filename', 'path', 'updated_at'])
            ->toArray()
        ]
      );
    }
    $this->model = Csdb::with('initiator');
    return $this->ret2(200, ['data' => $this->model->get()->toArray()]);
  }

  public function get_object_model(Request $request, string $filename)
  {
    $type = substr($filename, 0, 3);
    $model = Csdb::getModelClass(ucfirst($type));
    $model->setProtected(['with' => 'csdb.initiator']);
    $model = $model->where('filename', $filename)->first();
    return $model ? $this->ret2(200, ["model" => $model->toArray()]) : $this->ret2(400, ["no such {$filename} available."]);
  }

  public function forfolder_get_allobjects_list(Request $request)
  {
    // validasi. Jadi ketika tidak ada path ataupun sc, ataupun filename (KOSONG) maka akan mencari path = "csdb/"
    if ($request->path === "/") $request->merge(['path' => 'csdb']);

    $this->model = Csdb::with('initiator');
    // $res = $this->generateWhereRawQueryString($request->get('sc'));
    $res = $this->generateWhereRawQueryString($request->get('sc'),['path' => "#&value;"]);
    $keywords = $res[1];

    // menyiapkan csdb object
    // $this->model->orderBy('filename');
    // $ret = $this->model->paginate(100);
    $ret = $this->model->whereRaw($res[0])->orderBy('filename')->paginate(100);
    $ret->setPath($request->getUri());
    
    
    $m = '';
    // menyiapkan folder
    if ($ret->isNotEmpty()) {
      if (isset($keywords['path'])) {
        $this->model = new Csdb();
        $res = $this->generateWhereRawQueryString($request->get('sc'));
        $folder = $this->model->whereRaw($res[0])->get(['path'])->toArray();
        $folder = array_unique($folder, SORT_REGULAR);
        foreach($folder as $i => $v){
          $folder[$i] = join("", $v); // saat didapat dari database, bentuknya array berisi satu path saja
          foreach($keywords['path'] as $path){
            if($folder[$i] === $path){
              $folder[$i] = '';
            } else {
              $path = str_replace("/","\/",$path);
              $folder[$i] = preg_replace("/({$path})(\/[a-zA-Z0-9]+)(\/.+)?/","$1$2",$folder[$i]); // menghilangkan subfolder. eg.: query path='csdb', result='csdb/cn235/amm'. Nah 'amm' nya dihilangkan
            }
          }
        }
        $folder = array_unique($folder,SORT_STRING);
        $folder = array_filter($folder, fn ($v) => ($v != null) || ($v != ''));
        $folder = array_values($folder); // supaya tidak assoc atau supaya indexnya teratur
        sort($folder);
      }
    } else $m = "CSDB objects can not be found.";

    if (isset($keywords['path']) and count($keywords['path']) === 1) {
      $current_path = $keywords['path'][0];
    }

    $ret = $ret->toArray();
    $ret['csdb'] = $ret['data'];
    unset($ret['data']);

    return $this->ret2(200, $ret, ['message' => $m, 'infotype' => "caution", 'folder' => $folder ?? [], "current_path" => $current_path ?? '']);
  }

  /**
   * ini bisa update dan create
   * @return Response JSON contain SQL object model with initiator data
   */
  public function uploadICN(UploadICN $request)
  {
    // #1 validation input form
    $validatedData = $request->validated();
    $file = $validatedData['entity'];
    $CSDBModel = Csdb::where('filename', $validatedData['filename'])->first() ?? new Csdb();
    $CSDBModel->CSDBObject->load($file->path());
    $CSDBModel->filename = $validatedData['filename'];
    $CSDBModel->path = $validatedData['path'];
    $CSDBModel->initiator_id = $request->user()->id;
    $CSDBModel->appendAvailableStorage($request->user()->storage);
    if ($CSDBModel->saveDOMandModel($request->user()->storage)) {
      $CSDBModel->initiator; // agar ada initiator nya
      $message = $request->isUpdate ? "{$CSDBModel->filename} has been updated." : "New {$CSDBModel->filename} has been uploaded.";
      return $this->ret2(200, [$message], ["csdb" => $CSDBModel, 'infotype' => 'info']);
    } else {
      return $this->ret2(400, ["{$validatedData['filename']} failed to upload."], CSDBError::getErrors(), ["csdb" => $CSDBModel]);
    }
  }

  /**
   * jika ada filenamSearch, default pencarian adalah column 'filename'
   */
  public function get_deletion_list(Request $request)
  {
    $this->model = new Csdb();
    // $res = $this->generateWhereRawQueryString("deleter_id::" . $request->user()->id);
    $res = $this->generateWhereRawQueryString("deleter_id::" . 0);
    $ret = $this->model->whereRaw($res[0])->latest('deleted_at')->paginate(15);
    $ret->setPath($request->getUri());

    return $this->ret2(200, $ret->toArray());
  }

  /**
   * @return array
   */
  public function change_object_path(CsdbChangePath $request)
  {
    $validatedData = $request->validated();
    foreach($validatedData['CSDBModelArray'] as $model){
      $model->path = (string) $validatedData['path'];
      $model->save();
    }
    return $this->ret2(200,[ join(", ", $validatedData['filename']) . " success move to {$validatedData['path']}." ],['infotype' => 'info']);
  }


  public function delete(CsdbDelete $request)
  {
    dd($request->validated());
  }
}
