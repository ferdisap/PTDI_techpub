<?php

namespace App\Http\Controllers;

use App\Models\Csdb as ModelsCsdb;
use App\Models\Project;
use App\Models\User;
use App\Rules\Csdb\Path as PathRules;
use BREXValidator;
use Carbon\Carbon;
use Closure;
use DOMElement;
use DOMNode;
use DOMXPath;
use Gumlet\ImageResize;
use Illuminate\Foundation\Vite as FoundationVite;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rules\File;
use PhpParser\Node\Expr\Cast\Object_;
use Ptdi\Mpub\CSDB;
use Ptdi\Mpub\ICNDocument;
use Ptdi\Mpub\Validation;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipStream\ZipStream;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Vite;
use PrettyXml\Formatter;
use Ptdi\Mpub\Helper;
use Ptdi\Mpub\Main\CSDBError;
use Ptdi\Mpub\Main\CSDBObject;
use Ptdi\Mpub\Main\CSDBValidator;
use Ptdi\Mpub\Main\XSIValidator;
use Illuminate\Support\HtmlString;
use PHPUnit\Framework\MockObject\Stub\ReturnCallback;

class CsdbController extends Controller
{
  use Validation;

  /**
   * $model bisa berupa ModelsCsdb atau DB::table()
   * dipindahkan ke parent class
   */
  // private mixed $model;

  ################# NEW for csdb4 #################
  public function app()
  {
    // // output <script type="module" src="https://localhost:443/@vite/client"></script><script type="module" src="https://localhost:443/resources/js/csdb4/tes.js"></script>
    // $vite = Vite::useBuildDirectory(env('VITE_BUILD_DIR', 'build'))
    // ->withEntryPoints(['resources/js/csdb4/tes.js']);
    // dd($vite->toHTML());

    // // output <script type="module" src="https://localhost:443/@vite/client"></script><script type="module" src="https://localhost:443/resources/js/csdb4/tes.js"></script>
    // $vite = new FoundationVite;
    // $htmlString = $vite->__invoke('resources/js/csdb4/tes.js', env('VITE_BUILD_DIR', 'build'));
    // dd($htmlString);

    // // output = <script type="module" src="https://localhost:443/@vite/client"></script>
    // $vite = new FoundationVite;
    // dd($vite, $vite->toHTML());

    // ini bisa
    // $blade = file_get_contents(resource_path('views/csdb4/app.blade.php'));
    // $blade = Blade::render($blade);
    // return Response::make($blade,200,[
    //   'content-type' => 'text/html'
    // ]);

    // ini bisa
    // $view = view('csdb4.app')->render();
    // $view = preg_replace('/<script.+src=("[a-zA-Z0-9:\/]+worker.js").+<\/script>/m','$1',$view);
    // return Response::make($view,200,[
    //   'content-type' => 'text/html'
    // ]);
    
    // ini bisa
    // return view('csdb3.app');
    return view('csdb4.app');
  }

  /**
   * tidak bisa pakai fitur search dan tidak pakai pagination karena digunakan untuk ListTree.vue
   * jika $request->('listtree'), return all with only filename and path column
   * notApplicable: jika $request->get('path'), maka query where path like $request->get('path'); return all column
   */
  public function get_allobjects_list(Request $request)
  {
    // $tes = ModelsCsdb::where('filename', 'DMC-1_foo')->get(['updated_at']);
    // dd(now()->toString(), $tes[0]->updated_at->toString());
    if($request->get('listtree')){
      return $this->ret2(200, 
      [
        "data" => 
          ModelsCsdb::
          where('filename', 'like', 'DMC-%')
          ->orWhere('filename', 'like', 'PMC-%')
          ->orWhere('filename', 'like', 'ICN-%')
          ->get(['filename', 'path', 'updated_at'])
          ->toArray()
      ]);
      // return $this->ret2(200, ModelsCsdb::selectRaw("filename, path")->paginate(200)->toArray()); // hanya untuk dump karena database isinya ribuan rows
    }
    $this->model = ModelsCsdb::with('initiator');    
    return $this->ret2(200, ['data' => $this->model->get()->toArray()]);

    // $this->model = ModelsCsdb::with('initiator');
    // $this->model->orderBy('path');
    // $ret = $this->model->paginate(100);
    // $ret->setPath($request->getUri());
    // return $this->ret2(200, $ret->toArray());

    // $obj1 = [
    //   "filename" => 'cfoo1asasscsascscasas',
    //   'path' => 'csdb/'
    // ];
    // $obj1_1 = [
    //   "filename" => 'cfoo1_1asasscsascscasas',
    //   'path' => 'csdb/'
    // ];
    // $obj11 = [
    //   "filename" => 'n2foo11asasscsascscasas',
    //   'path' => 'csdb/n219/'
    // ];
    // $obj12 = [
    //   "filename" => 'n2foo12asasscsascscasas',
    //   'path' => 'csdb/n219/'
    // ];
    // $obj111 = [
    //   "filename" => 'cfoo111asasscsascscasas',
    //   'path' => 'csdb/n219/amm'
    // ];
    // $allobj = [$obj1, $obj11, $obj1_1, $obj12];
    // return $this->ret2(200, ['data' => $allobj]);

    // $obj21 = ["filename" => 'cfoo21', "path" => 'csdb/male/'];
    // $obj22 = ["filename" => 'cfoo22', "path" => 'csdb/male/'];

    // $obj3 = ["filename" => 'xafoo1', "path" => 'xxx/'];
    // $obj32 = ["filename" => 'xbfooasa', "path" => 'xxx/'];
    // $obj31 = ["filename" => 'xfoo11', "path" => 'xxx/n219/'];
    
    // $obj41 = ["filename" => 'yfoo11', "path" => 'yyy/'];
    // $obj42 = ["filename" => 'yfoo11', "path" => 'yyy/aaa/'];


    // $allobj = [$obj111, $obj1, $obj11, $obj1_1, $obj12, $obj21, $obj22, $obj3, $obj32, $obj31];
    // $allobj = [$obj1, $obj11, $obj1_1, $obj12, $obj21, $obj22, $obj3, $obj32, $obj31];
    // $allobj = [$obj1, $obj11, $obj1_1, $obj12, $obj21, $obj22, $obj3, $obj32, $obj31, $obj41, $obj42];
    // $allobj = [$obj1, $obj11, $obj1_1, $obj12, $obj21, $obj22];
    // return $this->ret2(200, ['data' => $allobj]);
  }

  public function get_object_model(Request $request, string $filename)
  {
    $model = ModelsCsdb::with('initiator')->where('filename', $filename)->first();
    return $model ? $this->ret2(200, ["model" => $model->toArray()]) : $this->ret2(400, ["no such {$filename} available."]);
  }

  public function forfolder_get_allobjects_list(Request $request)
  {
    // validasi. Jadi ketika tidak ada path ataupun sc, ataupun filename (KOSONG) maka akan mencari path = "csdb/"
    if($request->path === "/") $request->merge(['path' => 'csdb']);

    $this->model = ModelsCsdb::with('initiator');    
    $keywords = $this->search($request->get('sc'));

    // menyiapkan csdb object
    $this->model->orderBy('filename');
    $ret = $this->model->paginate(100);
    $ret->setPath($request->getUri());

    $m = '';
    // menyiapkan folder
    if($ret->isNotEmpty()){
      $minLengthPath = 999;
      if(isset($keywords['path'])){
        $this->model = new ModelsCsdb();
        $this->search($request->get('sc'), false);
        $folder = $this->model->get(['path'])->toArray();
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
  public function forfolder_get_allobjects_list_xx(Request $request)
  {
    // dd($request->all());
    // dd($request->ajax());
    // validasi. Jadi ketika tidak ada path ataupun filenameSearch, ataupun filename (KOSONG) maka akan mencari path = "csdb/"
    if(!$request->get('filenameSearch') 
      AND (!$request->path OR $request->path === "/")
      AND (!$request->get('filename'))
    ){
      // di XHR akan ditambah, tapi checknya di dd($request->request->get("path"));. 
      // Kalau di URL, dapetnya bisa langsung di $request->get("path")
      // jika mau dua duanya, checknya pakai $request->path
      // $request->merge(['path' => "csdb/"]); 
      $request->merge(['path' => "csdb"]); 
    }

    $this->model = ModelsCsdb::with('initiator');    
    $keywords = $this->search($request->get('filenameSearch'), $m);
    // dd($request->get('filenameSearch'),$keywords);

    // jika ada filename, maka akan menuju request path
    if($request->get("filename")){
      $path = ModelsCsdb::where('filename', $request->get('filename'))->first('path')->path;
      $request->merge(["path" => $path]);
    }
    
    // jika ada path, maka akan mendapatkan apth saja
    if($request->path AND !isset($keywords['path'])){
      $this->model->where('path', $request->path);
      $current_path = ["current_path" => $request->path];
    }

    $path = $path ?? $request->path;
    $path = "{$path}%";
    $this->model->orderBy('filename');
    $ret = $this->model->paginate(100);
    $ret->setPath($request->getUri());

    if($ret->isNotEmpty()){
      // $folder = ModelsCsdb::selectRaw('path')->whereRaw("path LIKE '{$path}'")->get()->unique('path', true);
      $folder = ModelsCsdb::selectRaw('path')->whereRaw("path LIKE '{$path}' AND initiator_id = ". Auth::user()->id)->get()->unique('path', true);
      $folder = $folder->toArray();
      $folder = array_filter($folder, function($obj) use($request){
        if($obj['path'] === $request->path){
          return false;
        } elseif(count(explode("/", $obj['path'])) - count(explode("/",$request->path)) > 1){
          return false;
        }
        return $obj;
      });
      sort($folder); // supaya tidak ada keys nya (hanya value didalam array)
    } else {
      $m[] = join(" or ", [$request->filenameSearch, $request->filename]) . "can not be found.";
    }

    return $this->ret2(200, $ret->toArray(), ['message' => $m, 'infotype' => "caution"] ,['folder' => $folder ?? []], $current_path ?? ["current_path" => ""]);
  }

  /**
   * sudah dicoba terkait penerapan CSDBObject class, tapi baru hanya sekali dan berhasil
   * Tidak untuk ICN. 
   * Saat ini tidak ada fitur upload file
   * history code = CRBT
   * @return Response JSON contain SQL object model with initiator data
   */
  public function create(Request $request)
  {    
    // #0 sama dengan CsdbServiceController@change_object_path
    $validator = Validator::make($request->all(), [
      'path' => ['required', new PathRules]
    ]);
    if($validator->fails()) return $this->ret2(400, [$validator->getMessageBag()->getMessages()]);
    $validated = $validator->validated();
    
    $new_csdb_model = new ModelsCsdb();
    $new_csdb_model->direct_save = false;

    // #1. create dom
    $proccessid = CSDBError::$processId = self::class . "::create";
    $new_csdb_model->CSDBObject = new CSDBObject("5.0");
    $new_csdb_model->CSDBObject->loadByString(trim($request->get('xmleditor')));
    if (!$new_csdb_model->CSDBObject->isS1000DDoctype()) return $this->ret2(400, ['Failed to create csdb.'], ["xmleditor" => CSDBError::getErrors(true, $proccessid)]);
    CSDBError::$processId = '';

    // #2. validasi filename,rootname dom
    if ($new_csdb_model->CSDBObject->document instanceof \DOMDocument AND $new_csdb_model->CSDBObject->isS1000DDoctype()) {
      $csdb_filename = $new_csdb_model->CSDBObject->filename;
      $initial = $new_csdb_model->CSDBObject->getInitial();
      if ($initial === 'dml') return $this->ret2(400, [['xmleditor' => ['You cannot create DML here.']]]);
    } else {
      return $this->ret2(400, ['Failed to create csdb Object.']);
    }

    // #3. validate Schema Xsd (optional). User boleh uncheck input checkbox xsi_validate
    if (($new_csdb_model->CSDBObject->document instanceof \DOMDocument) AND $request->get('xsi_validate')) {
      $CSDBValidator = new XSIValidator($new_csdb_model->CSDBObject);
      if(!$CSDBValidator->validate()){
        return $this->ret2(400, [['xmleditor' => CSDBError::getErrors(true, 'validateBySchema')]]);
      }
    }

    // #4. assign inWork into '01' and issueNumber to the highest+1
    $domXpath = new \DOMXPath($new_csdb_model->CSDBObject->document);
    $code = preg_replace("/_.+/", '', $csdb_filename);
    $collection = Storage::disk('csdb')->files();
    $collection = array_filter($collection, fn ($file) => str_contains($file, $code));
    if (empty($collection)) {
      $issueInfo = $domXpath->evaluate("//identAndStatusSection/{$initial}Address/{$initial}Ident/issueInfo")[0];
      $issueInfo->setAttribute('issueNumber', '000');
      $issueInfo->setAttribute('inWork', '01');
    } else {
      $collection_issueNumber = [];
      $collection_inWork = [];
      array_walk($collection, function ($file, $i) use (&$collection_issueNumber, &$collection_inWork) {
        $file = explode('_', $file);
        if (isset($file[1])) {
          $issueInfo = explode("-", $file[1]);
          $collection_issueNumber[$i] = $issueInfo[0];
          $collection_inWork[$i] = $issueInfo[1];
        }
      });

      $issueInfo = $domXpath->evaluate("//identAndStatusSection/{$initial}Address/{$initial}Ident/issueInfo")[0];
      $max_in = max($collection_issueNumber);
      $max_in = array_keys(array_filter($collection_issueNumber, fn ($v) => $v == $max_in))[0]; // output key. bukan value array
      $max_in = $collection_issueNumber[$max_in];
      $max_iw = max($collection_inWork);
      $max_iw = array_keys(array_filter($collection_inWork, fn ($v) => $v == $max_iw))[0]; // output key. bukan value array
      $max_iw = $collection_inWork[$max_iw];
      $max_iw++;

      $issueInfo->setAttribute('issueNumber', str_pad($max_in, 3, '0', STR_PAD_LEFT));
      $issueInfo->setAttribute('inWork', str_pad($max_iw, 2, '0', STR_PAD_LEFT));
    }
    $csdb_filename = $new_csdb_model->CSDBObject->getFilename();

    // #5. validate Brex (optional). User boleh uncheck input checkbox brex_validate
    // setiap create DML, tidak divalidasi BREX, validasi Brex harus dilakukan oleh user secara manual setelah di upload
    // sementara ini ICNDocument tidak di validasi oleh brex saat upload
    if (($initial != 'dml') AND $request->get('brex_validate') == 'on') {
      $CSDBValidator = new BREXValidator($new_csdb_model->CSDBObject, $new_csdb_model->CSDBObject->getBrexDm());
      if($CSDBValidator->validate()){
        return $this->ret2(400, [['xmleditor' => CSDBError::getErrors(true, 'validateBySchema')]]);        
      }
    }

    

    // #6. saving dan menambahkan remarks stage dan remarks
    $new_csdb_model->filename = $csdb_filename;
    $new_csdb_model->path = $validated['path'];
    // $new_csdb_model->editable = 1;
    $new_csdb_model->initiator_id = $request->user()->id;
    // $new_csdb_model->setRemarks('stage', 'unstaged'); // kayaknya ini tidak diperlukan lagi jika sudah tidak ada fitur stage/unstaged/staging
    // $new_csdb_model->setRemarks('history', Carbon::now().";CRBT;Object is created with filename {$csdb_filename}.;{$request->user()->first_name} {$request->user()->middle_name} {$request->user()->last_name}");
    // $new_csdb_model->setRemarks('status');

    if($new_csdb_model->saveModelAndDOM()){
      $new_csdb_model->initiator; // agar ada initiator nya
      return $this->ret2(200, ["New {$new_csdb_model->filename} has been created."], ["model" => $new_csdb_model], ['infotype' => 'info']);
    } 
    return $this->ret2(400, ["{$csdb_filename} failed to create."], CSDBError::getErrors(), ['model' => $new_csdb_model]);
  }

  /**
   * sudah dicoba terkait penerapan CSDBObject class, tapi baru hanya sekali dan berhasil
   * jika user mengubah filename, filename akan kembali seperti asalnya karena update akan mengubah seluruhnya selain filename
   * @return Response JSON contain SQL object model with initiator data
   */
  public function update(Request $request, string $filename)
  {
    // #0. validasi schema
    $old_filename = $filename;
    $CSDBModel = ModelsCsdb::where('filename', $old_filename)->first();
    // $old_dom = CSDB::importDocument(storage_path('csdb'), $CSDBModel->filename);
    $oldCSDBObject = new CSDBObject("5.0");
    $oldCSDBObject->load(storage_path("csdb/$CSDBModel->filename"));
    
    // $schema = $old_dom->documentElement->getAttribute('xsi:noNamespaceSchemaLocation');
    $schema = $oldCSDBObject->getSchema();
    if (str_contains($schema, 'dml.xsd')) {
      return $this->ret2(400, ["You cannot update object with schema {$schema} here."]);
    }
    if (!$CSDBModel->editable) {
      return $this->ret2(400, ["You cannot update object with the editable status is false."]);
    }
    if ($request->path){
      $serviceController = new CsdbServiceController();
      if(!($changePath = $serviceController->change_object_path($request, $CSDBModel))[0]){
        return $this->ret2(400, [$changePath[1]]);
      }
    }

    // #1. create dom
    $proccessid = CSDBError::$processId = self::class . "::update";
    // $xmlstring = $request->get('xmleditor');
    // $CSDBModel->DOMDocument = CSDB::importDocument('', '', trim($xmlstring)); // akan false jika tidak bisa jad DOM
    // if (!$CSDBModel->DOMDocument) return $this->ret2(400, ['Failed to update object.'], ["xmleditor" => CSDB::get_errors(true, $proccessid)]);
    // $CSDBModel->CSDBObject = new CSDBObject("5.0");
    $CSDBModel->CSDBObject->loadByString(trim($request->get('xmleditor')));
    if (!$CSDBModel->CSDBObject->isS1000DDoctype()) return $this->ret2(400, ['Failed to create csdb.'], ["xmleditor" => CSDBError::getErrors(true, $proccessid)]);
    CSDBError::$processId = '';
    
    // #2. validasi filename,rootname dom
    if ($CSDBModel->CSDBObject->document instanceof \DOMDocument AND $CSDBModel->CSDBObject->isS1000DDoctype()) {
      $new_filename = $CSDBModel->CSDBObject->filename;
      $initial = $CSDBModel->CSDBObject->getInitial();
      if ($initial === 'dml') return $this->ret2(400, [['xmleditor' => ['You cannot create DML here.']]]);
    } else {
      return $this->ret2(400, ['Failed to create csdb Object.']);
    }

    // #3. validate Schema Xsd (optional). User boleh uncheck input checkbox xsi_validate
    if (($CSDBModel->CSDBObject->document instanceof \DOMDocument) AND $request->get('xsi_validate')) {
      $CSDBValidator = new XSIValidator($CSDBModel->CSDBObject);
      if(!$CSDBValidator->validate()){
        return $this->ret2(400, [['xmleditor' => CSDBError::getErrors(true, 'validateBySchema')]]);
      }
    }

    // #4. tidak bisa mengganti filename. Filename hanya bisa diganti dengan create new, commit or releasing
    if ($old_filename != $new_filename) {
      return $this->ret2(400, ["You didn't allow to change element &lt;{$initial}Ident&gt;"]);
    }

    // #5. validate Brex (optional). User boleh uncheck input checkbox brex_validate
    // setiap create DML, tidak divalidasi BREX, validasi Brex harus dilakukan oleh user secara manual setelah di upload
    // sementara ini ICNDocument tidak di validasi oleh brex saat upload
    if (($initial != 'dml') AND $request->get('brex_validate') == 'on') {
      $CSDBValidator = new BREXValidator($CSDBModel->CSDBObject, $CSDBModel->CSDBObject->getBrexDm());
      if($CSDBValidator->validate()){
        return $this->ret2(400, [['xmleditor' => CSDBError::getErrors(true, 'validateBySchema')]]);        
      }
    }

    // #6. tambahkan remarks table berdasarkan identAndStatusSection/descendant::remarks
    // digabung ke step #7;
    
    // #7. saving
    $CSDBModel->direct_save = false;
    // $CSDBModel->setRemarks('status');
    // $CSDBModel->setRemarks('history', Carbon::now().";UPDT;Object updated with filename {$new_filename}.;{$request->user()->first_name} {$request->user()->middle_name} {$request->user()->last_name}");
    $CSDBModel->updated_at = now();
    if($CSDBModel->saveModelAndDOM()){
      return $this->ret2(200, ["{$new_filename} has been saved."], ["model" => $CSDBModel]);
    }
    return $this->ret2(400, ["{$new_filename} failed to issue."]);
  }

  /**
   * saat ini ICN tidak bisa di update, karena keterbatasan logic. Mungkin selanjutnya bisa, jadi setiap di update tidak ada erubahan di filenamenya karena tidak ada attribute inWork. Jadi setiap kali ICN di issue, editable menjadi 0
   * sudah dicoba terkait penerapan CSDBObject class, tapi baru hanya sekali dan berhasil
   * untuk ICN. 
   * Filename = auto generated, based CAGE Codes // ini catatan lama
   * prefix-cagecode-uniqueIdentifier-issueNumber-sc // ini catatan lama
   * mungkin filename tidak perlu auto generate, termasuk sequential numbernya agar lebih mudah di maintain
   * Jika input name='filename' tidak ada, makan gunakan filename name='entity'
   * @return Response JSON contain SQL object model with initiator data
   */
  public function uploadICN(Request $request)
  {
    // #1 validation input form
    $validatedData = $request->validate([
      // "filename" => '', // lakukan validasi ICN filename berdasarkan aturan S1000D dan atau aturan kita
      "filename" => [function(string $attribute, mixed $value,  Closure $fail){
        if($value){
          CSDBError::$processId = 'ICNFilenameValidation';
          $validator = new CSDBValidator('ICNName', ["validatee" => $value]);
          $validator->setStoragePath(CSDB_STORAGE_PATH);
          if(!$validator->validate()){
            $fail(join(", ", CSDBError::getErrors(true, 'ICNFilenameValidation')));
          }
        }
      }],
      // 'securityClassification' => ['required', function (string $attribute, mixed $value,  Closure $fail) {
      //   $value = (int)$value;
      //   if (!($value <= 1 or $value >= 5)) $fail("You should put the security classifcation between 1 through 5.");
      // }],
      "entity" => ['required', function (string $attribute, mixed $value,  Closure $fail) {
        $ext = strtolower($value->getClientOriginalExtension());
        $mime = strtolower($value->getMimeType());
        if ($ext == 'xml' or str_contains($mime, 'text')) {
          $fail("You should put the non-text file in {$attribute}.");
        }
      }],
      'path' => ['required', new PathRules]
    ]);
    $file = $request->file('entity');

    // #3. saving
    $new_csdb_model = new ModelsCsdb();
    $new_csdb_model->CSDBObject->load($file->path());
    $new_csdb_model->direct_save = false;
    $new_csdb_model->filename = $validatedData['filename'];
    $new_csdb_model->path = $validatedData['path'];
    $new_csdb_model->editable = 0;
    $new_csdb_model->initiator_id = $request->user()->id;
    // $new_csdb_model->setRemarks('history', Carbon::now().";CRBT;Object create with filename {$validatedData['filename']}.;{$request->user()->first_name} {$request->user()->middle_name} {$request->user()->last_name}");
    if($new_csdb_model->saveModelAndDOM()){
      $new_csdb_model->initiator; // agar ada initiator nya
      return $this->ret2(200, ["New {$new_csdb_model->filename} has been created."], ["model" => $new_csdb_model]);
    } else {
      return $this->ret2(400, ["{$validatedData['filename']} failed to upload."], CSDBError::getErrors(), ["model" => $new_csdb_model]);
    }
  }

  /**
   * hanya mengganti file saja, tidak merubah apapun (termasuk tidak merubah filename) 
   * jika user ingin mengubah filename, termasuk mengubah sequrity classification, maka harus delete, dan reupload lagi
   */
  public function updateICN(Request $request, ModelsCsdb $csdb)
  {
    $request->validate([
      "entity" => ['required', function (string $attribute, mixed $value,  Closure $fail) {
        $ext = strtolower($value->getClientOriginalExtension());
        $mime = strtolower($value->getMimeType());
        if ($ext == 'xml' or str_contains($mime, 'text')) {
          $fail("You should put the non-text file in {$attribute}.");
        }
      }],
    ]);
    $file = $request->file('entity');
    if(Storage::disk('csdb')->put($csdb->filename, $file->getContent())){
      $csdb->updated_at = now();
      if($csdb->save()){
        return $this->ret2(200, ["New {$csdb->filename} has been updated."], ["data" => $csdb]);
      }
    };
    return $this->ret2(400, ["{$csdb->filename} failed to update."]);
    
  }
  
  /**
   * nanti gunakan fungsi deletingProccess daripada processnya ditulis di fungsi ini
   * akan memindahkan file ke folder csdb_deleted.
   * Object yang sudah di delete, tidak akan bisa diapa-apain kecuali di download
   * Object hanya bisa di delete jika remark->stage != staged
   * filename akan ditambah dengan 'filename.xml__timestamp_microsecond'
   * @return Response with data = model SQL object, data2 = Deletion Object
   */
  public function delete(Request $request, string $filename)
  {
    $process = $this->deletingProcess($request, $filename);
    if($process["result"]){
      return $this->ret2($process["code"], $process["message"], ['data' => $process["model"]], ["data2" => $process["deleted_data"]]);
    } else {
      return $this->ret2($process["code"], $process["message"]);
    }
  }

  public function delete_multiple(Request $request)
  {
    // return $this->ret2(200, ['message1', 'message2'], ['models' => ['tes model1', 'tes model2']]);
    $messages = [];
    $deletedModels = [];
    // $arrayfilenames = isset($request->filenames) ? explode(', ', $request->filenames) : [$request->filename];
    $arrayfilenames = $request->filenames ?? $request->filename;
    if(!is_array($arrayfilenames)) $arrayfilenames = [$arrayfilenames];
    foreach($arrayfilenames as $filename){
      $process = $this->deletingProcess($request, $filename);
      $messages[] = $process["message"];
      $deletedModels[] = $process['model'];
    }
    return $this->ret2(200, $messages, ['models' => $deletedModels]);
  }
  
  public function deletingProcess(Request $request, string $filename)
  {
    $fnRet = function($message, $result, $code, $model, $deleted_data){
      return [
        "message" => $message,
        "result" => $result,
        "code" => $code,
        "model" => $model,
        "deleted_data" => $deleted_data,
      ];
    };

    $model = ModelsCsdb::with('initiator')->where('filename', $filename)->first(); 
    if ($model->initiator->id = !$request->user()->id) {
      return $fnRet(
        "Deleting {$filename} must be done by {$model->initiator->name}",
        false, 400, $model, null);
    };

    // staged sudah tidak relevan karena nanti akan pakai konsep data dispatch note
    // if (isset($model->remarks['stage']) AND $model->remarks['stage'] === 'staged') {
    //   return $fnRet(
    //     "{$filename} has been staged and cannot be deleted.", false, 400, $model, null);
    // }
   
    $model->hide(false);
    $model->direct_save = false;
    $time = Carbon::now()->timezone(7);
    $new_filename = ($filename . '__' . $time->timestamp . '-' . $time->microsecond);    

    // $model->setRemarks('history', Carbon::now().";DELL;Object with filename {$filename} is deleted.;{$request->user()->first_name} {$request->user()->middle_name} {$request->user()->last_name}");
    $model->save();

    $model_meta = $model->toArray();
    $insert = [
      "filename" => $new_filename,
      "deleter_id" => $request->user()->id,
      "meta" => collect($model_meta),
      "created_at" => Carbon::now(7),
    ];

    $create_deleted_db = fn () => DB::table('csdb_deleted')->insert($insert); // $insert ditaruh diluar agar bisa dikirim sebagai response, karena juga fungsi insert() ini returning boolean
    $move_file = fn() => Storage::disk('csdb_deleted')->put($new_filename, Storage::disk('csdb')->get($filename)) AND Storage::disk('csdb')->delete($filename);
    $revert_move_file = fn() => Storage::disk('csdb')->put($filename, Storage::disk('csdb_deleted')->get($new_filename)) AND Storage::disk('csdb_deleted')->delete($new_filename);

    if($move_file()){
      if($create_deleted_db()){
        if($model->delete()){
          Storage::disk('csdb')->delete($filename);
          return $fnRet("{$new_filename} has been created as a result of deleting {$filename}.", true, 200, $model, $insert);
        } else {
          $revert_move_file();
        }
        $revert_move_file();
      };
    } 
    return $fnRet("{$filename} fails to delete", false, 400, $model, null );
  }

  /**
   * Restore the soft deleted @delete() csdb object.
   * @return Response contain data is model SQL CSDB Object
   */
  public function restore(Request $request, string $filename)
  {
    // #1. get deleted model
    $deleted_QB = DB::table('csdb_deleted')->where('filename', $filename);
    $deleted_model = $deleted_QB->first();
    if(!$deleted_model) return $this->ret2(400, ["{$filename} failed to restore."]);
    if($request->user()->id != $deleted_model->deleter_id) return $this->ret2(400, ["Only {$request->user()->first_name} {$request->user()->middle_name} {$request->user()->last_name} can restore."]);
    
    $meta = function($stdClass, $fn){ // untuk mengubah seluruh stdClass menjadi array
      $stdClass = get_object_vars($stdClass);
      foreach($stdClass as $k => $v){
        if(is_object($v)) $stdClass[$k] = $fn($v, $fn);
      }
      return $stdClass;
    };
    $meta = $meta(json_decode($deleted_model->meta), $meta);

    // #2 restore by move file from path 'csdb_deleted' to 'csdb' and then re-create ModelsCsdb
    $message = "{$filename} fail to restore.";
    $model = new ModelsCsdb();
    $restore = function() use($meta, $deleted_QB, $filename, &$message, $request, &$model){
      // chek duplicate id
      if(ModelsCsdb::find($meta['id'])){
        $message = "Failed to restore due to duplication filename. See {$filename}.";
        return false;
      }
      $new_filename = preg_replace("/__[\S]+/",'',$filename);
      $move_file = fn() => Storage::disk('csdb')->put($new_filename, Storage::disk('csdb_deleted')->get($filename)) AND Storage::disk('csdb_deleted')->delete($filename);
      $revert_move_file = fn() => Storage::disk('csdb_deleted')->put($filename, Storage::disk('csdb')->get($new_filename)) AND Storage::disk('csdb')->delete($new_filename);

      // jika gagal memindahkan file, maka revert moved file
      if(!$move_file()) {
        $message = "Failed to move file from deleted to csdb.";
        return false;
      };

      // jika gagal membuat model maka revert moved file
      if(!($model = ModelsCsdb::create($meta))){
        $revert_move_file();
        $message = "Failed to create CSDB Model.";
        return false;
      }

      // jika gagal delete the deletion csdb, maka revert moved file dan delete model
      if(!($deleted_QB->delete())) {
        $revert_move_file();
        $model->delete();
        $message = "Failed to delete the deletion csdb.";
        return false;
      };

      $message = "{$filename} has been restored";
      // $model->setRemarks('history', Carbon::now().";RSTR;Object with filename {$filename} is deleted.;{$request->user()->first_name} {$request->user()->middle_name} {$request->user()->last_name}");
      $model->save();
      return $new_filename;
    };
    
    // #3. return
    if($filename = $restore()){
      return $this->ret2(200, [$message], ['data' => $model]);
    } else {
      return $this->ret2(400, [$message]);
    }
  }

  /**
   * jika ada filenamSearch, default pencarian adalah column 'filename'
   */
  public function get_deletion_list(Request $request)
  {
    $messages = [];
    $this->model = DB::table('csdb_deleted');
    $this->model->where('deleter_id', $request->user()->id);    
    if ($request->get('filenameSearch')) {
      $this->search($request->get('filenameSearch'));
    }
    $ret = $this->model
      ->latest()
      ->paginate(15);
    $ret->setPath($request->getUri());

    foreach($ret->items() as $k => $v){
      $v->meta = json_decode($v->meta);
    }
    return $this->ret2(200, $messages, $ret->toArray());
  }

  public function get_deletion_object(Request $request, string $filename)
  {
    $DeletionObject = DB::table('csdb_deleted')->first();
    if($DeletionObject){
      $file = Storage::disk('csdb_deleted')->get($filename);
      $mime = Storage::disk('csdb_deleted')->mimeType($filename);
      return Response::make($file, 200, [
        'Content-Type' => $mime,
      ]);
    }
  }

  /**
   * @return Response with data = Deletion Object;
   */
  public function permanentDelete(Request $request)
  {
    $filename = $request->get('filename');
    $message = 'There is no need to be permanently deleted.';
    $code = 400;
    if(!$filename){
      return $this->ret2($code, [$message]);
    }
    $data = [];
    $deleted_QB = DB::table('csdb_deleted')->where('filename', $filename);
    if($deleted_QB->delete() AND Storage::disk('csdb_deleted')->delete($filename)){
      $code = 200;
      $message = "{$filename} has been permanently deleted.";
      $data = ['data' => $deleted_QB];
    }
    return $this->ret2($code, [$message], $data);
  }

  /**
   * nyontek dari delete()
   */
  public function commit(Request $request, string $filename)
  {
    $process = $this->commitingProccess($request, $filename);
    if($process['result']){
      return $this->ret2($process["code"], $process["message"], ['data' => $process["oldmodel"]], ["data2" => $process["newmodel"]]);
    } else {
      return $this->ret2($process["code"], $process["message"]);
    }
  }

  /**
   * nyontek dari delete_multiple()
   */
  public function commit_multiple(Request $request)
  {
    $messages = [];
    $newModels = [];    
    $arrayfilenames = $request->filenames ?? $request->filename;
    if(!is_array($arrayfilenames)) $arrayfilenames = [$arrayfilenames];
    foreach($arrayfilenames as $filename){
      $process = $this->commitingProcess($request, $filename);
      $messages[] = $process["message"];
      $newModels[] = $process['newmodel'];
    }
    return $this->ret2(200, $messages, ['models' => $newModels]);
  }

  /**
   * see DmlController@commit. Algoritma nya sama
   * history code CMMT
   */
  public function commitingProcess(Request $request, string $filename)
  {
    $fnRet = function($message, $result, $code, $model, $newmodel){
      return [
        "message" => $message,
        "result" => $result,
        "code" => $code,
        "model" => $model,
        "newmodel" => $newmodel,
      ];
    };
    $CSDBModel = ModelsCsdb::where('filename', $filename)->first();
    if ($CSDBModel->initiator_id != $request->user()->id) {
      // $this->ret2(400, ["Only Initiator ({$CSDBModel->initiator->name}) can commit."])
      return $fnRet(
        "Only Initiator ({$CSDBModel->initiator->name}) can commit.",
        false, 400, $CSDBModel, null);
    };
    $CSDBModel->direct_save = false;

    $newCSDBObject = new CSDBObject("5.0");
    $newCSDBObject->load(CSDB_STORAGE_PATH. DIRECTORY_SEPARATOR. $CSDBModel->filename);
    if(!$newCSDBObject->commit()) {
      return $fnRet('Fail to commit.', false, 400, $CSDBModel, null);
    };
    $newFilename = $newCSDBObject->getFilename();

    $CSDBModel->editable = 0;
    // $CSDBModel->setRemarks('history', Carbon::now().";CMMT;Object is commited with new filename {$newFilename}.;{$request->user()->first_name} {$request->user()->middle_name} {$request->user()->last_name}");

    //# save old and new DOM
    $save = fn() => Storage::disk('csdb')->put($newFilename, $newCSDBObject->document->saveXML());
    $revert_save = fn() => Storage::disk('csdb')->delete($newFilename);
    if($save()){
      $newCSDBModel = ModelsCSDB::create([
        'filename' => $newFilename,
        'path' => $CSDBModel->path,
        'editable' => 1,
        'initiator_id' => $CSDBModel->initiator_id,
        'remarks' => $CSDBModel->remarks,
      ]);
      if(!($newCSDBModel)) {
        $revert_save();
        return $fnRet("Fail to create new CSDB Model.", false, 400, $CSDBModel, null);
      }
      // $newCSDBModel->setRemarks('stage', 'unstaged');
      // $newCSDBModel->setRemarks('history', Carbon::now().";CRBT;Object is created with filename {$newFilename}.;{$request->user()->first_name} {$request->user()->middle_name} {$request->user()->last_name}");
      if(!($CSDBModel->save())){
        $revert_save();
        $newCSDBModel->delete();
        return $fnRet("Fail to save {$CSDBModel->filename}.", false, 400, $CSDBModel, null);
      }
      return $fnRet("Successfully commit. New {$newFilename} has been created.", true, 200, $CSDBModel, $newCSDBModel);
    }
  }

  ################# NEW for csdb3 #################
  // public function app()
  // {
  //   return view('csdb3.app');
  // }

  /**
   * akan membuat file baru dengan issueNumber largest dan inWork '01'
   * jika filename code ada yang sama di directory, maka issueNumber tetap(largest) dan inWork largest+1. Ini sama seperti fitur commit
   * issueNumber++ adalah sebuah fitur yang hanya bisa digunakan jika reviewer sudah memvalidasi dan akan di-load ke csdb
   */
  // public function create(Request $request)
  // {
  //   // #1. create dom
  //   $proccessid = CSDB::$processid = self::class . "::create";
  //   $file = $request->file('entity');
  //   if ($file) {
  //     $dom = new ICNDocument();
  //     $dom->load($file->getPath(), $file->getFilename()); // nama, path, dll masih merefer ke tmp file
  //   } else {
  //     $xmlstring = $request->get('xmleditor');
  //     $dom = CSDB::importDocument('', '', trim($xmlstring)); // akan false jika tidak bisa jad DOM
  //   }
  //   if (!$dom) return $this->ret2(400, ['Failed to create csdb.'], ["xmleditor" => CSDB::get_errors(true, $proccessid)]);
  //   CSDB::$processid = '';

  //   // #2. validasi filename,rootname dom
  //   if ($dom instanceof \DOMDocument) {
  //     if (!($validateRootname = CSDB::validateRootname($dom))) {
  //       return $this->ret2(400, [["xmleditor" => CSDB::get_errors(true, 'validateRootname')]]);
  //     }
  //     $csdb_filename = $validateRootname[1];
  //     $ident = $validateRootname[2];
  //     $path = "csdb";
  //     if ($ident == 'dml') return $this->ret2(400, [['xmleditor' => ['You cannot create DML here.']]]);
  //   } elseif ($dom instanceof ICNDocument) {
  //     $csdb_filename = $request->file('entity')->getClientOriginalName();
  //     if (substr($csdb_filename, 0, 3) != 'ICN') return $this->ret2(400, ["The name of {$csdb_filename} is not accepted."]);
  //     $ident = 'infoEntity';
  //     $path = "csdb";
  //     preg_match("/ICN\-[A-Z0-9]{5}\-[A-Z0-9]{5,10}\-[0-9]{3}\-[0-9]{2}.[a-z]+/", $csdb_filename, $matches);
  //     if (empty($matches)) {
  //       return $this->ret2(400, ["{$csdb_filename} is not match with pattern: ICN\-[A-Z0-9]{5}\-[A-Z0-9]{5,10}\-[0-9]{3}\-[0-9]{2}.[a-z]+"]);
  //     }
  //   } else {
  //     return $this->ret2(400, ['Failed to create csdb Object.']);
  //   }

  //   // #3. validate Schema Xsd (optional). User boleh uncheck input checkbox xsi_validate
  //   if (($dom instanceof \DOMDocument) and $request->get('xsi_validate')) {
  //     CSDB::validate('XSI', $dom);
  //     if (CSDB::get_errors(false, 'validateBySchema')) {
  //       return $this->ret2(400, [['xmleditor' => CSDB::get_errors(true, 'validateBySchema')]]);
  //     }
  //   }

  //   // #4. assign inWork into '01' and issueNumber to the highest+1
  //   if (($dom instanceof \DOMDocument)) {
  //     $domXpath = new \DOMXPath($dom);
  //     $code = preg_replace("/_.+/", '', $csdb_filename);
  //     $collection = array_diff(scandir(storage_path($path))); // harusnya ga usa pakai array_dif. Nanti di cek lagi
  //     $collection = array_filter($collection, fn ($file) => str_contains($file, $code));
  //     if (empty($collection)) {
  //       $issueInfo = $domXpath->evaluate("//identAndStatusSection/{$validateRootname[3]}Address/{$validateRootname[3]}Ident/issueInfo")[0];
  //       $issueInfo->setAttribute('issueNumber', '000');
  //       $issueInfo->setAttribute('inWork', '01');
  //     } else {
  //       $collection_issueNumber = [];
  //       $collection_inWork = [];
  //       array_walk($collection, function ($file, $i) use (&$collection_issueNumber, &$collection_inWork) {
  //         $file = explode('_', $file);
  //         if (isset($file[1])) {
  //           $issueInfo = explode("-", $file[1]);
  //           $collection_issueNumber[$i] = $issueInfo[0];
  //           $collection_inWork[$i] = $issueInfo[1];
  //         }
  //       });

  //       $issueInfo = $domXpath->evaluate("//identAndStatusSection/{$validateRootname[3]}Address/{$validateRootname[3]}Ident/issueInfo")[0];
  //       $max_in = max($collection_issueNumber);
  //       $max_in = array_keys(array_filter($collection_issueNumber, fn ($v) => $v == $max_in))[0]; // output key. bukan value array
  //       $max_in = $collection_issueNumber[$max_in];
  //       $max_iw = max($collection_inWork);
  //       $max_iw = array_keys(array_filter($collection_inWork, fn ($v) => $v == $max_iw))[0]; // output key. bukan value array
  //       $max_iw = $collection_inWork[$max_iw];
  //       $max_iw++;

  //       $issueInfo->setAttribute('issueNumber', str_pad($max_in, 3, '0', STR_PAD_LEFT));
  //       $issueInfo->setAttribute('inWork', str_pad($max_iw, 2, '0', STR_PAD_LEFT));
  //     }
  //     $csdb_filename = CSDB::resolve_DocIdent($dom);
  //   }

  //   // #5. validate Brex (optional). User boleh uncheck input checkbox brex_validate
  //   // setiap create DML, tidak divalidasi BREX, validasi Brex harus dilakukan oleh user secara manual setelah di upload
  //   // sementara ini ICNDocument tidak di validasi oleh brex saat upload
  //   if (($ident != 'dml') and ($dom instanceof \DOMDocument) and $request->get('brex_validate') == 'on') {
  //     CSDB::validate('BREX', $dom, null, storage_path("app/{$path}"));
  //     if (CSDB::get_errors(false, 'validateByBrex')) {
  //       return $this->ret2(400, [['xmleditor' => CSDB::get_errors(true, 'validateByBrex')]]);
  //     }
  //   }

  //   // #6. saving dan menambahkan remarks stage dan remarks
  //   if ($dom instanceof \DOMDocument) {
  //     $save = $dom->C14NFile(storage_path($path) . DIRECTORY_SEPARATOR . $csdb_filename);
  //   } else {
  //     $save = $file->storeAs("../{$path}", $csdb_filename);
  //   }
  //   if ($save) {
  //     $new_csdb_model = ModelsCsdb::create([
  //       'filename' => $csdb_filename,
  //       'path' => $path,
  //       'editable' => 1,
  //       'initiator_id' => $request->user()->id,
  //     ]);
  //     if ($new_csdb_model) {
        // $new_csdb_model->setRemarks('stage', 'unstaged');
        // $new_csdb_model->setRemarks('remarks',$dom); // tambahkan remarks table berdasarkan identAndStatusSection/descendant::remarks
  //       return $this->ret2(200, ["New {$new_csdb_model->filename} has been created."]);
  //     }
  //   }
  //   return $this->ret2(400, ["{$csdb_filename} failed to issue."], ['object' => $new_csdb_model]);
  // }

  
  // /**
  //  * untuk ICN. 
  //  * Filename = auto generated, based CAGE Codes
  //  * prefix-cagecode-uniqueIdentifier-issueNumber-sc
  //  */
  // public function uploadICN(Request $request)
  // {
  //   $request->validate([
  //     // "cagecode" => 'required',// akan memakai latest cagecode
  //     'securityClassification' => ['required', function (string $attribute, mixed $value,  Closure $fail) {
  //       $value = (int)$value;
  //       if (!($value <= 1 or $value >= 5)) $fail("You should put the security classifcation between 1 through 5.");
  //     }],
  //     "entity" => ['required', function (string $attribute, mixed $value,  Closure $fail) {
  //       $ext = strtolower($value->getClientOriginalExtension());
  //       $mime = strtolower($value->getMimeType());
  //       if ($ext == 'xml' or str_contains($mime, 'text')) {
  //         $fail("You should put the non-text file in {$attribute}.");
  //       }
  //     }],
  //   ]);

  //   $file = $request->file('entity');
  //   $extension = $file->getClientOriginalExtension();

  //   $prefix = "ICN";

  //   // mencari nilai uniqueIdentifier terbesar +1;
  //   $latestFileName = CSDB::getLatestICNFile(storage_path("csdb"), '0001Z');
  //   $latestFileName_array = empty($latestFileName) ? [] : explode("-", $latestFileName);

  //   $cagecode = $request->get('cagecode') ?? $latestFileName_array[1] ?? '';
  //   if (preg_replace("/[A-Z0-9]{5}/", '', $cagecode) != '') return $this->ret2(400, ['cagecode' => ['Cage code company must contain capital alphabetical or numerical in five length.']]);
  //   if (!$cagecode) return $this->ret2(400, ['cagecode' => ["Cage Code company is required for naming object."]]);

  //   $uniqueIdentifier = $latestFileName_array[2] ?? 0;
  //   $uniqueIdentifier++;
  //   $uniqueIdentifier = str_pad((int) $uniqueIdentifier, 5, '0', STR_PAD_LEFT);

  //   $issueNumber = $latestFileName_array[3] ?? 0;
  //   $issueNumber++;
  //   $issueNumber = str_pad((int) $issueNumber, 3, '0', STR_PAD_LEFT);

  //   $securityClassification = str_pad((int) $request->get('securityClassification'), 2, '0', STR_PAD_LEFT);

  //   $cagecode = '0001Z';
  //   $filename = "{$prefix}-{$cagecode}-{$uniqueIdentifier}-{$issueNumber}-{$securityClassification}.{$extension}";

  //   $save = Storage::disk('csdb')->put($filename, $file->getContent());
  //   if ($save) {
  //     $new_csdb_model = ModelsCsdb::create([
  //       'filename' => $filename,
  //       'path' => 'csdb',
  //       'editable' => '1',
  //       'initiator_id' => $request->user()->id,
  //     ]);
  //     if ($new_csdb_model) {
  //       $new_csdb_model->setRemarks('stage', 'unstaged');
  //       return $this->ret2(200, ["New {$new_csdb_model->filename} has been created."]);
  //     }
  //   }
  //   return $this->ret2(400, ["{$filename} failed to issue."]);
  // }
  
  // /**
  //  * jika user mengubah filename, filename akan kembali seperti asalnya karena update akan mengubah seluruhnya selain filename
  //  */
  // public function update(Request $request, string $filename)
  // {
  //   // #0. validasi schema
  //   $old_filename = $filename;
  //   $csdb_object = ModelsCsdb::where('filename', $old_filename)->first();
  //   $old_dom = CSDB::importDocument(storage_path($csdb_object->path), $csdb_object->filename);
  //   $schema = $old_dom->documentElement->getAttribute('xsi:noNamespaceSchemaLocation');
  //   if (str_contains($schema, 'dml.xsd')) {
  //     return $this->ret2(400, ["You cannot update object with schema {$schema} here."]);
  //   }
  //   if (!$csdb_object->editable) {
  //     return $this->ret2(400, ["You cannot update object with the editable status is false."]);
  //   }

  //   // #1. create dom
  //   $proccessid = CSDB::$processid = self::class . "::update";
  //   $file = $request->file('entity');
  //   if ($file) {
  //     $dom = new ICNDocument();
  //     $dom->load($file->getPath(), $file->getFilename()); // nama, path, dll masih merefer ke tmp file
  //   } else {
  //     $xmlstring = $request->get('xmleditor');
  //     $dom = CSDB::importDocument('', '', trim($xmlstring)); // akan false jika tidak bisa jad DOM
  //   }
  //   if (!$dom) return $this->ret2(400, ['Failed to update object.'], ["xmleditor" => CSDB::get_errors(true, $proccessid)]);
  //   CSDB::$processid = '';

  //   // #2. validasi filename,rootname dom
  //   if ($dom instanceof \DOMDocument) {
  //     if (!($validateRootname = CSDB::validateRootname($dom))) return $this->ret2(400, [["xmleditor" => CSDB::get_errors(true, 'validateRootname')]]);
  //     $csdb_filename = $validateRootname[1];
  //     $ident = $validateRootname[2];
  //     $path = "csdb";
  //     if ($ident == 'dml') return $this->ret2(400, [['xmleditor' => ['You cannot update DML here.']]]);
  //   } elseif ($dom instanceof ICNDocument) {
  //     $csdb_filename = $request->file('entity')->getClientOriginalName();
  //     $ident = 'infoEntity';
  //     $path = "csdb";
  //     preg_match("/ICN\-[A-Z0-9]{5}\-[A-Z0-9]{5,10}\-[0-9]{3}\-[0-9]{2}.[a-z]+/", $csdb_filename, $matches);
  //     if (empty($matches)) {
  //       return $this->ret2(400, ["{$csdb_filename} is not match with pattern: ICN\-[A-Z0-9]{5}\-[A-Z0-9]{5,10}\-[0-9]{3}\-[0-9]{2}.[a-z]+"]);
  //     }
  //   } else {
  //     return $this->ret2(400, ['Failed to create csdb Object.']);
  //   }

  //   // #3. validate Schema Xsd (optional). User boleh uncheck input checkbox xsi_validate
  //   if (($dom instanceof \DOMDocument) and $request->get('xsi_validate')) {
  //     CSDB::validate('XSI', $dom);
  //     if ($error = CSDB::get_errors(false, 'validateBySchema')) {
  //       return $this->ret2(400, [['xmleditor' => $error]]);
  //     }
  //   }

  //   // #4. change new filename (if user change) with old filename
  //   if ($dom instanceof \DOMDocument) {
  //     $new_filename = CSDB::resolve_DocIdent($dom);
  //     if ($old_filename != $new_filename) {
  //       return $this->ret2(400, ["You didn't allow to change element &lt;{$validateRootname[3]}Ident&gt;"]);
  //       $domXpath = new \DOMXPath($dom);
  //       $ident = $domXpath->evaluate("//identAndStatusSection/{$validateRootname[3]}Address/{$validateRootname[3]}Ident")[0];
  //       $old_domXpath = new \DOMXPath($old_dom);
  //       $old_ident = $old_domXpath->evaluate("//identAndStatusSection/{$validateRootname[3]}Address/{$validateRootname[3]}Ident")[0];

  //       $ident->replaceWith($dom->importNode($old_ident, true));
  //       $dom->saveXML();
  //     }
  //   }

  //   // #5. validate Brex (optional). User boleh uncheck input checkbox brex_validate
  //   // setiap create DML, tidak divalidasi BREX, validasi Brex harus dilakukan oleh user secara manual setelah di upload
  //   // sementara ini ICNDocument tidak di validasi oleh brex saat upload
  //   if (($ident != 'dml') and ($dom instanceof \DOMDocument) and $request->get('brex_validate') == 'on') {
  //     CSDB::validate('BREX', $dom, null, storage_path($csdb_object->path));
  //     if ($error = CSDB::get_errors(true, 'validateByBrex')) {
  //       return $this->ret2(400, [['xmleditor' => $error]]);
  //     }
  //   }

  //   // #6. tambahkan remarks table berdasarkan identAndStatusSection/descendant::remarks
  //   if($dom instanceof \DOMDocument){
  //     $csdb_object->DOMDocument = $dom;
  //     $csdb_object->setRemarks('remarks');
  //     $dom = $csdb_object->DOMDocument;
  //   }        

  //   // #7. saving
  //   if ($dom instanceof \DOMDocument) {
  //     $save = $dom->C14NFile(storage_path($csdb_object->path) . DIRECTORY_SEPARATOR . $csdb_filename);
  //   } else {
  //     $save = $file->storeAs("../{$csdb_object->path}", $csdb_filename);
  //   }
  //   if ($save) {
  //     $csdb_object->updated_at = now();
  //     $csdb_object->save();
  //     return $this->ret2(200, ["{$csdb_filename} has been saved."]);
  //   }
  //   return $this->ret2(400, ["{$csdb_filename} failed to issue."]);
  // }
  

  /**
   * fungsi ini harus diubah, disesuaikan lagi dengan kondisi
   * filter by initiator_email
   * filter by stage
   * filter by filenameSearch
   */
  // public function get_objects_list(Request $request)
  // {
  //   // $masterCSDB = true; // $request->user()->masterCSDB ?? false
  //   // if($masterCSDB){
  //   //   $all = ModelsCsdb::where('initiator_id','like','%');
  //   // } else {
  //   //   $all = ModelsCsdb::where('initiator_id',$request->user()->id);
  //   // }
  //   $all = ModelsCsdb::with('initiator');
  //   if ($request->get('initiator_email')) {
  //     $initiator = User::where('email', $request->get('initiator_email'))->first();
  //     if ($initiator) {
  //       $all->where('initiator_id', $initiator->id);
  //     }
  //   }
  //   if ($request->get('stage')) {
  //     $all->where('remarks', '"stage":"staged"');
  //   }
  //   if ($request->get('filenameSearch')) {
  //     // $all->where('filename', 'like', "%" . $request->get('filenameSearch') . "%");
  //     $filenameSearch = $request->get('filenameSearch');
  //     $all->whereRaw("filename LIKE '%{$filenameSearch}%' ESCAPE '\'");
  //   }
  //   $ret = $all
  //     ->where('filename', 'not like', 'DML%')
  //     ->where('filename', 'not like', 'CSL%')
  //     // ->where('remarks', 'not like', '"crud":"deleted"')
  //     ->paginate(15);
  //   $ret->setPath($request->getUri());
  //   return $ret;
  // }

  /**
   * DEPRECIATED. dipindah ke CsdbServiceController@request_csdb_object
   * $request->get('output') = "'model'|default:''";
   * @return Illuminate\Support\Facades\Response;
   */
  public function getFile(Request $request, string $filename)
  {
    $csdb_object = ModelsCsdb::where('filename', $filename)->first();
    if ($request->get('output') == 'model') {
      return $csdb_object;
    }
    $dom = CSDB::importDocument(storage_path($csdb_object->path), $csdb_object->filename);
    if (!$dom) {
      return $this->ret2(400, ["failed to load {$filename}."]);
    } elseif ($dom instanceof ICNDocument) {
      $mime = $dom->getFileinfo()['mime_type'];
      return Response::make($dom->getFile(), 200, ['Content-Type' => $mime]);
    } else {
      // return Response::make($dom->C14N(), 200, ['Content-Type' => 'text/xml']);
      $formatter = new Formatter();
      return Response::make($formatter->format($dom->C14N()), 200, ['Content-Type' => 'text/xml']);
      // $dm = new \DOMDocument();
      // $dm->preserveWhiteSpace = true;
      // $dm->formatOutput = true;
      // $dm->loadXML($dom->C14N());
      // return Response::make($dm->C14N(), 200, ['Content-Type' => 'text/xml']);
    }
  }


  /**
   * see DmlController@commit. Algoritma nya sama
   */
  public function commit_xx(Request $request, string $filename)
  {
    $model = ModelsCsdb::where('filename', $filename)->first();
    if ($model->initiator_id != $request->user()->id) return $this->ret2(400, ["Only Initiator ({$model->initiator->name}) can commit."]);
    // if(!$model->editable) return $this->ret2(400, ['This object cannot re commit. You might have issue this object at previous.']);
    $model->direct_save = false;
    $model->DOMDocument = CSDB::importDocument(storage_path($model->path), $model->filename);

    // new DOM
    $dom = CSDB::commit($model->DOMDocument);
    if (!$dom) return $this->ret2(400, CSDB::get_errors(true, 'commit'));

    $new_filename = CSDB::resolve_DocIdent($model->DOMDocument);
    if ($new_model = ModelsCsdb::where('filename', $new_filename)->first()) return $this->ret2(400, ["This Object cannot be commited due duplication filename of {$new_filename}."]);

    // change old object editable into false
    $model->editable = 0;
    $model->save();


    // save new DOM
    $save = $dom->C14NFile(storage_path($model->path) . DIRECTORY_SEPARATOR . $new_filename);
    if ($save) {
      $new_model = ModelsCsdb::create([
        'filename' => $new_filename,
        'path' => $model->path,
        'editable' => 1,
        'initiator_id' => $model->initiator_id,
        'remarks' => $model->remarks,
      ]);
      // $new_model->setRemarks('stage', 'unstaged');
      return $this->ret2(200, ["New {$new_model->filename} has been created."]);
    }
    return $this->ret2(400, ["{$filename} failed to commit."]);
  }

  /**
   * ICN tidak bisa di issue. hanya .xml yang bisa
   * issueNumber++, $inWork = "00"
   * kalau schemanya brex.xsd, tidak akan divalidasi brex. Selanjutnya bisa di ubah jika ingin pakai controller BREX sendiri
   */
  public function issue(Request $request, string $filename)
  {
    $issueInfo = explode("_", $filename)[1] ?? '';
    $inWork = explode("-", $issueInfo)[1] ?? '';
    if ($inWork == '00' or !str_contains($filename, ".xml")) return $this->ret2(400, ["$filename cannot resolved."]);

    $model = ModelsCsdb::where('filename', $filename)->first();
    if ($model->initiator_id != $request->user()->id) return $this->ret2(400, ["Only Initiator ({$model->initiator->name}) can edit."]);
    $model->direct_save = false;
    if (!$model) return $this->ret2(400, ["$filename does not available."]);
    $dom = CSDB::importDocument(storage_path($model->path), $filename);

    $initial = $dom->documentElement->tagName;
    if ($initial == 'dmodule') $initial = 'dm';
    $domXpath = new \DOMXpath($dom);
    $issueInfo = $domXpath->evaluate("//identAndStatusSection/{$initial}Address/{$initial}Ident/issueInfo")[0];
    $issueNumber = $issueInfo->getAttribute('issueNumber');
    $issueNumber++;
    $issueNumber = str_pad($issueNumber, 3, '0', STR_PAD_LEFT);
    $issueInfo->setAttribute("issueNumber", $issueNumber);
    $issueInfo->setAttribute("inWork", '00');

    // validate XSI dan BREX
    $validateXSI = CSDB::validate('XSI', $dom);
    if (!$validateXSI) return $this->ret2(400, CSDB::get_errors());
    $schema = CSDB::getSchemaUsed($dom, '');
    if ($schema != 'brex.xsd') {
      $validateBREX = CSDB::validate('BREX', $dom);
      if (!$validateBREX) return $this->ret2(400, CSDB::get_errors());
    }

    // new csdb
    $new_filename = CSDB::resolve_DocIdent($dom);
    if (ModelsCsdb::where('filename', $new_filename)->first()) return $this->ret2(400, ["{$filename} has already issued before named {$new_filename}"]);
    $new_model = new ModelsCsdb();
    $new_model->direct_save = false;
    $new_model->DOMDocument = $dom;
    $new_model->filename = $new_filename;
    $new_model->path = $model->path;
    $new_model->editable = 0;
    $new_model->initiator_id = $model->initiator_id;
    $new_model->remarks = $model->remarks;
    // $new_model->setRemarks('securityClassification', CSDB::resolve_securityClassification($new_model->DOMDocument, 'number'));
    $save = $new_model->saveModelAndDOM();
    if ($save) {
      return $this->ret2(200, ["Object is issued named {$new_filename}"]);
    } else {
      return $this->ret2(400, ["Issue {$filename} fails."]);
    }

    // $model->save

  }

  /**
   * fungsi ini sama dengan DmlController@edit. Jadi nanti bisa digabung biar maintenancenya lebih gampang
   * akan membuat file baru issueNumber tetap, inWork '01'
   * fungsi ini dipakai ketika dml sudah di issue(), namun ada content yang harus di edit
   */
  public function edit(Request $request, string $filename)
  {
    if (!$filename or substr($filename, 0, 3) == 'DML') return $this->ret2(400, ['This object is can not open edit here.']);
    $model = ModelsCsdb::where('filename', $filename)->first();
    if ($model->initiator_id != $request->user()->id) return $this->ret2(400, ["Only Initiator ({$model->initiator->name}) can edit."]);
    if ($model->editable) return $this->ret2(400, ["{$filename} is still in editable."]);

    $dom = CSDB::importDocument(storage_path($model->path), $model->filename);
    $initial = $dom->documentElement->tagName;
    if ($initial == 'dmodule') $initial = 'dm';
    $domxpath = new \DOMXPath($dom);
    $issueInfo = $domxpath->evaluate("//identAndStatusSection/{$initial}Address/{$initial}Ident/issueInfo")[0];
    $issueInfo->setAttribute('inWork', '01');

    $new_filename = CSDB::resolve_DocIdent($dom);
    if ($new_model = ModelsCsdb::where('filename', $new_filename)->first()) return $this->ret2(400, ["This object cannot be editted due duplication filename of {$new_filename}."]);
    $save = $dom->C14NFile(storage_path($model->path) . DIRECTORY_SEPARATOR . $new_filename);
    if ($save) {
      $new_model = ModelsCsdb::create([
        'filename' => $new_filename,
        'path' => $model->path,
        'editable' => 1,
        'initiator_id' => $model->initiator_id,
        'remarks' => $model->remarks,
      ]);
      if ($new_model) {
        // $new_model->setRemarks('stage', 'unstaged');
        return $this->ret2(200, ["New {$new_model->filename} has been created."]);
      }
    }
    return $this->ret2(400, ["{$filename} failed to open edit."]);
  }

  // /**
  //  * jika ada filenamSearch, default pencarian adalah column 'filename'
  //  */
  // public function get_deletion_list(Request $request)
  // {
  //   $messages = [];
  //   $this->model = DB::table('csdb_deleted');
  //   $this->model->where('deleter_id', $request->user()->id);    
  //   if ($request->get('filenameSearch')) {
  //     $this->search($request->get('filenameSearch'));
  //   }
  //   $ret = $this->model
  //     ->latest()
  //     ->paginate(15);
  //   $ret->setPath($request->getUri());

  //   foreach($ret->items() as $k => $v){
  //     $v->meta = json_decode($v->meta);
  //   }
  //   return $this->ret2(200, $messages, $ret->toArray());
  // }

  // /**
  //  * akan memindahkan file ke folder csdb_deleted.
  //  * Object yang sudah di delete, tidak akan bisa diapa-apain kecuali di download
  //  * Object hanya bisa di delete jika remark->stage != staged
  //  * filename akan ditambah dengan 'filename.xml__timestamp_microsecond'
  //  */
  // public function delete(Request $request, string $filename)
  // {
  //   $model = ModelsCsdb::with('initiator')->where('filename', $filename)->first();
  //   if (!$model) return $this->ret2(400, ["{$filename} failed to delete."]);

  //   $model->hide(false);
  //   $model_meta = $model->toArray();
    
  //   if ($model->initiator->id = !$request->user()->id) return $this->ret2(400, ["Deleting {$filename} must be done by {$model->initiator->name}"]);
  //   if (isset($model->remarks['stage']) and $model->remarks['stage'] === 'staged') return $this->ret2(400, ["{$filename} has been staged and cannot be deleted."]);
    
  //   $model->direct_save = false;
  //   $time = Carbon::now()->timezone(7);
  //   $new_filename = ($filename . '__' . $time->timestamp . '-' . $time->microsecond);
    
    
  //   $create_deleted_db = fn () => DB::table('csdb_deleted')->insert([
  //     "filename" => $new_filename,
  //     "deleter_id" => $request->user()->id,
  //     "meta" => collect($model_meta),
  //     "created_at" => Carbon::now(7),
  //   ]);
  //   $move_file = fn() => Storage::disk('csdb_deleted')->put($new_filename, Storage::disk('csdb')->get($filename)) AND Storage::disk('csdb')->delete($filename);
    
  //   if(!($create_deleted_db() AND $move_file())) return $this->ret2(400,["{$filename} fails to delete"]);

  //   $model->delete();    
  //   return $this->ret2(200, ["{$new_filename} has been created as a result of deleting {$filename}."]);
  // }

  // /**
  //  * Restore the soft deleted @delete() csdb object.
  //  */
  // public function restore(Request $request, string $filename)
  // {
  //   // #1. get deleted model
  //   $deleted_QB = DB::table('csdb_deleted')->where('filename', $filename);
  //   $deleted_model = $deleted_QB->first();
  //   if(!$deleted_model) return $this->ret2(400, ["{$filename} failed to restore."]);
  //   $meta = function($stdClass, $fn){ // untuk mengubah seluruh stdClass menjadi array
  //     $stdClass = get_object_vars($stdClass);
  //     foreach($stdClass as $k => $v){
  //       if(is_object($v)) $stdClass[$k] = $fn($v, $fn);
  //     }
  //     return $stdClass;
  //   };
  //   $meta = $meta(json_decode($deleted_model->meta), $meta);

  //   // #2. restore by re-creating ModelsCsdb and move file from path 'csdb_deleted' to 'csdb'
  //   $message = "{$filename} fail to restore.";
  //   $restore = function() use($meta, $deleted_QB, $filename, &$message){
  //     $model = ModelsCsdb::find($meta['id']);
  //     if($model){
  //       $message = "Failed to restore due to duplication filename. See {$filename}.";
  //       return false;
  //     } else {
  //       $model = ModelsCsdb::create($meta);
  //     }
  //     $isDel = $deleted_QB->delete();
  //     if($isDel){
  //       $new_filename = preg_replace("/__[\S]+/",'',$filename);
  //       $is_move_file = Storage::disk('csdb')->put($new_filename, Storage::disk('csdb_deleted')->get($filename)) AND Storage::disk('csdb_deleted')->delete($filename);
  //       if($is_move_file) {
  //         $message = "{$filename} has been restored";
  //         return $new_filename;
  //       };
  //     }
  //     return false;
  //   };
  //   // #3. return
  //   if($filename = $restore()){
  //     $filename = preg_replace("/__[\S]+/",'',$filename);
  //     return $this->ret2(200, [$message]);
  //   } else {
  //     $filename = preg_replace("/__[\S]+/",'',$filename);
  //     return $this->ret2(400, [$message]);
  //   }
  // }

  // public function permanentDelete(Request $request)
  // {
  //   $filename = $request->get('filename');
  //   $message = 'There is no need to be permanently deleted.';
  //   $code = 400;
  //   if(!$filename){
  //     return $this->ret2($code, [$message]);
  //   }
  //   $deleted_QB = DB::table('csdb_deleted')->where('filename', $filename);
  //   if($deleted_QB->delete() AND Storage::disk('csdb_deleted')->delete($filename)){
  //     $code = 200;
  //     $message = "{$filename} has been permanently deleted.";
  //   }
  //   return $this->ret2($code, [$message]);
  // }

  /**
   * hanya digunakan untuk developersaja, tidak untuk end-user
   */
  public function harddelete(Request $request, string $filename)
  {
    // $str = "aaa/bbb/c";
    // dd(rtrim($str,'c'));
    // dd(substr($str,-1,1 ));
    $model = ModelsCsdb::where('filename', $filename)->first();
    $delete = Storage::disk('csdb')->delete($filename);
    if ($delete) {
      $model->delete();
    }
    return true;
  }
}
