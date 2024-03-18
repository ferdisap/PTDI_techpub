<?php

namespace App\Http\Controllers;

use App\Models\Csdb as ModelsCsdb;
use App\Models\Project;
use App\Models\User;
use BREXValidator;
use Carbon\Carbon;
use Closure;
use DOMElement;
use DOMNode;
use DOMXPath;
use Gumlet\ImageResize;
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
    // Vite::useBuildDirectory('production'); // Customize the build directory...

    // ini bisa
    $blade = file_get_contents(resource_path('views/csdb4/app.blade.php'));
    $blade = Blade::render($blade);
    return Response::make($blade,200,[
      'content-type' => 'text/html'
    ]);

    // ini bisa
    // $view = view('csdb4.app')->render();
    // return Response::make($view,200,[
    //   'content-type' => 'text/html'
    // ]);
    
    // ini bisa
    return view('csdb4.app');
  }

  /**
   * tidak bisa pakai fitur search dan tidak pakai pagination karena digunakan untuk ListTree.vue
   * jika $request->('listtree'), return all with only filename and path column
   * notApplicable: jika $request->get('path'), maka query where path like $request->get('path'); return all column
   */
  public function get_allobjects_list(Request $request)
  {
    if($request->get('listtree')){
      return $this->ret2(200, ["data" => ModelsCsdb::get(['filename', 'path'])->toArray()]);
      // return $this->ret2(200, ModelsCsdb::selectRaw("filename, path")->paginate(200)->toArray()); // hanya untuk dump karena database isinya ribuan rows
    }
    $this->model = ModelsCsdb::with('initiator');    
    return $this->ret2(200, ['data' => $this->model->get()->toArray()]);

    // $this->model = ModelsCsdb::with('initiator');
    // $this->model->orderBy('path');
    // $ret = $this->model->paginate(100);
    // $ret->setPath($request->getUri());
    // return $this->ret2(200, $ret->toArray());

    $obj1 = [
      "filename" => 'cfoo1asasscsascscasas',
      'path' => 'csdb/'
    ];
    $obj1_1 = [
      "filename" => 'cfoo1_1asasscsascscasas',
      'path' => 'csdb/'
    ];
    $obj11 = [
      "filename" => 'n2foo11asasscsascscasas',
      'path' => 'csdb/n219/'
    ];
    $obj12 = [
      "filename" => 'n2foo12asasscsascscasas',
      'path' => 'csdb/n219/'
    ];
    // $obj111 = [
    //   "filename" => 'cfoo111asasscsascscasas',
    //   'path' => 'csdb/n219/amm'
    // ];
    // $allobj = [$obj1, $obj11, $obj1_1, $obj12];
    // return $this->ret2(200, ['data' => $allobj]);

    $obj21 = ["filename" => 'cfoo21', "path" => 'csdb/male/'];
    $obj22 = ["filename" => 'cfoo22', "path" => 'csdb/male/'];

    $obj3 = ["filename" => 'xafoo1', "path" => 'xxx/'];
    $obj32 = ["filename" => 'xbfooasa', "path" => 'xxx/'];
    $obj31 = ["filename" => 'xfoo11', "path" => 'xxx/n219/'];
    
    // $obj41 = ["filename" => 'yfoo11', "path" => 'yyy/'];
    // $obj42 = ["filename" => 'yfoo11', "path" => 'yyy/aaa/'];


    // $allobj = [$obj111, $obj1, $obj11, $obj1_1, $obj12, $obj21, $obj22, $obj3, $obj32, $obj31];
    $allobj = [$obj1, $obj11, $obj1_1, $obj12, $obj21, $obj22, $obj3, $obj32, $obj31];
    // $allobj = [$obj1, $obj11, $obj1_1, $obj12, $obj21, $obj22, $obj3, $obj32, $obj31, $obj41, $obj42];
    // $allobj = [$obj1, $obj11, $obj1_1, $obj12, $obj21, $obj22];
    return $this->ret2(200, ['data' => $allobj]);
  }

  public function get_object_model(Request $request, string $filename)
  {
    $model = ModelsCsdb::with('initiator')->where('filename', $filename)->first();
    return $model ? $this->ret2(200, ["data" => $model->toArray()]) : $this->ret2(400, ["no such {$filename} available."]);
  }

  public function forfolder_get_allobjects_list(Request $request)
  {
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
    $keywords = $this->search($request->get('filenameSearch'));

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
    $folder = ModelsCsdb::selectRaw('path')->whereRaw("path LIKE '{$path}'")->get()->unique('path', true);
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
    
    $this->model->orderBy('filename');
    $ret = $this->model->paginate(100);
    $ret->setPath($request->getUri());
    return $this->ret2(200, $ret->toArray(), ['folder' => $folder ?? []], $current_path ?? ["current_path" => ""]);
  }

  /**
   * @return Response with 'data' = csdb sql model
   */
  public function changePath(Request $request, string $filename)
  {
    $model = null;
    $request->merge(['filename' => $filename]);
    $request->validate([
      'path' => 'required',
      'filename' => [function(string $attribute, mixed $value,  Closure $fail) use(&$model){
        $model = ModelsCsdb::where('filename', $value)->first();
        if(!$model) $fail("No such $value available in CSDB.");
      }]
    ]);
    
    if($model AND $model instanceof ModelsCsdb){
      $model->path = $request->path;
      if($model->save()){
        return $this->ret2(200, ['data' => $model], ["Path $filename has been updated."]);
      }
    }
    return $this->ret2(400, ["Failed to update option."]);
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
    // #1. create dom
    $proccessid = CSDBError::$processId = self::class . "::create";
    $CSDBObject = new CSDBObject("5.0");
    $CSDBObject->loadByString(trim($request->get('xmleditor')));
    if (!$CSDBObject->isS1000DDoctype()) return $this->ret2(400, ['Failed to create csdb.'], ["xmleditor" => CSDBError::getErrors(true, $proccessid)]);
    CSDBError::$processId = '';

    // #2. validasi filename,rootname dom
    if ($CSDBObject->document instanceof \DOMDocument AND $CSDBObject->isS1000DDoctype()) {
      $csdb_filename = $CSDBObject->filename;
      $initial = $CSDBObject->getInitial();
      if ($initial === 'dml') return $this->ret2(400, [['xmleditor' => ['You cannot create DML here.']]]);
    } else {
      return $this->ret2(400, ['Failed to create csdb Object.']);
    }

    // #3. validate Schema Xsd (optional). User boleh uncheck input checkbox xsi_validate
    if (($CSDBObject->document instanceof \DOMDocument) AND $request->get('xsi_validate')) {
      $CSDBValidator = new XSIValidator($CSDBObject);
      if(!$CSDBValidator->validate()){
        return $this->ret2(400, [['xmleditor' => CSDBError::getErrors(true, 'validateBySchema')]]);
      }
    }

    // #4. assign inWork into '01' and issueNumber to the highest+1
    $domXpath = new \DOMXPath($CSDBObject->document);
    $code = preg_replace("/_.+/", '', $csdb_filename);
    $collection = scandir(storage_path('csdb'));
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
    $csdb_filename = $CSDBObject->getFilename();

    // #5. validate Brex (optional). User boleh uncheck input checkbox brex_validate
    // setiap create DML, tidak divalidasi BREX, validasi Brex harus dilakukan oleh user secara manual setelah di upload
    // sementara ini ICNDocument tidak di validasi oleh brex saat upload
    if (($initial != 'dml') AND $request->get('brex_validate') == 'on') {
      $CSDBValidator = new BREXValidator($CSDBObject, $CSDBObject->getBrexDm());
      if($CSDBValidator->validate()){
        return $this->ret2(400, [['xmleditor' => CSDBError::getErrors(true, 'validateBySchema')]]);        
      }
    }

    // #6. saving dan menambahkan remarks stage dan remarks
    $save = Storage::disk('csdb')->put($csdb_filename, $CSDBObject->document->saveXML());
    if ($save) {
      $new_csdb_model = ModelsCsdb::create([
        'filename' => $csdb_filename,
        'path' => 'csdb',
        'editable' => 1,
        'initiator_id' => $request->user()->id,
      ]);
      if ($new_csdb_model) {
        $new_csdb_model->setRemarks('stage', 'unstaged');
        $new_csdb_model->setRemarks('remarks',$CSDBObject->document); // tambahkan remarks table berdasarkan identAndStatusSection/descendant::remarks
        $new_csdb_model->setRemarks('history', Carbon::now().";CRBT;Object create with filename {$csdb_filename}.;{$request->user()->name}");
        $new_csdb_model->initiator = [
          'name' => $request->user()->name,
          'email' => $request->user()->email,
        ];
        return $this->ret2(200, ["New {$new_csdb_model->filename} has been created."], ["data" => $new_csdb_model]);
      }
    }
    return $this->ret2(400, ["{$csdb_filename} failed to issue."], ['object' => $new_csdb_model]);
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

    // #1. create dom
    $proccessid = CSDBError::$processId = self::class . "::update";
    // $xmlstring = $request->get('xmleditor');
    // $CSDBModel->DOMDocument = CSDB::importDocument('', '', trim($xmlstring)); // akan false jika tidak bisa jad DOM
    // if (!$CSDBModel->DOMDocument) return $this->ret2(400, ['Failed to update object.'], ["xmleditor" => CSDB::get_errors(true, $proccessid)]);
    $CSDBModel->CSDBObject = new CSDBObject("5.0");
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
    $save = Storage::disk('csdb')->put($new_filename, $CSDBModel->CSDBObject->document->saveXML());
    if ($save) {
      $CSDBModel->setRemarks('history', Carbon::now().";UPDT;Object updated with filename {$new_filename}.;{$request->user()->name}");
      $CSDBModel->setRemarks('remarks');
      $CSDBModel->updated_at = now();
      $CSDBModel->save();
      return $this->ret2(200, ["{$new_filename} has been saved."], ["data" => $CSDBModel]);
    }
    return $this->ret2(400, ["{$new_filename} failed to issue."]);
  }

  /**
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
    $request->validate([
      // "filename" => '', // lakukan validasi ICN filename berdasarkan aturan S1000D dan atau aturan kita
      "filename" => [function(string $attribute, mixed $value,  Closure $fail){
        if($value){
          CSDBError::$processId = 'ICNFilenameValidation';
          $validator = new CSDBValidator('ICNName', ["validatee" => $value]);
          if(!$validator->validate()){
            $fail(join(", ", CSDBError::getErrors(true, 'ICNFilenameValidation')));
          }
        }
      }],
      'securityClassification' => ['required', function (string $attribute, mixed $value,  Closure $fail) {
        $value = (int)$value;
        if (!($value <= 1 or $value >= 5)) $fail("You should put the security classifcation between 1 through 5.");
      }],
      "entity" => ['required', function (string $attribute, mixed $value,  Closure $fail) {
        $ext = strtolower($value->getClientOriginalExtension());
        $mime = strtolower($value->getMimeType());
        if ($ext == 'xml' or str_contains($mime, 'text')) {
          $fail("You should put the non-text file in {$attribute}.");
        }
      }],
    ]);
    $file = $request->file('entity');
    $filename = $request->filename ?? (function($filename){
      CSDBError::$processId = 'ICNFilenameValidation';
      $validator = new CSDBValidator('ICNName', ["validatee" => $filename]);
      return $validator->validate() ? $filename : '';
    })($file->getClientOriginalName());
    
    // #2. validation storage + autogenerated uniqueIdentifier
    // tambahkan fitur jika ingin autogenerated uniqueIdentifier image jika file_exist
    $isFileExist = true;
    if(file_exists(storage_path("csdb/$filename"))){
      if($request->autoGeneratedUniqueIdentifier){
        // $filename = ... gunakan/buatkan fungsi di class CSDBObject atau ICNDocument
        $filename = $filename; // SEMENTARA saja ini;
        $isFileExist = false;
        // $save =  Storage::disk('csdb')->put($filename, $file->getContent()); // akan fail jika filename = '';
      } else {
        // $save = false;
        $isFileExist = true;
      }
    } else {
      // $save =  Storage::disk('csdb')->put($filename, $file->getContent()); // akan fail jika filename = '';
      $isFileExist = false;
    }

    // #3. saving
    if($filename AND 
      !ModelsCsdb::where('filename', $filename)->first() AND 
      !$isFileExist AND 
      Storage::disk('csdb')->put($filename, $file->getContent())) 
    {
      $new_csdb_model = ModelsCsdb::create([
        'filename' => $filename,
        'path' => 'csdb',
        'editable' => '1',
        'initiator_id' => $request->user()->id,
      ]);
      if ($new_csdb_model) {
        $new_csdb_model->setRemarks('stage', 'unstaged');
        $new_csdb_model->setRemarks('history', Carbon::now().";CRBT;Object create with filename {$filename}.;{$request->user()->name}");
        $new_csdb_model->initiator = [
          'name' => $request->user()->name,
          'email' => $request->user()->email,
        ];
        return $this->ret2(200, ["New {$new_csdb_model->filename} has been created."], ["data" => $new_csdb_model]);
      }
    }
    return $this->ret2(400, ["{$filename} failed to upload."], CSDBError::getErrors());
  }

  /**
   * akan memindahkan file ke folder csdb_deleted.
   * Object yang sudah di delete, tidak akan bisa diapa-apain kecuali di download
   * Object hanya bisa di delete jika remark->stage != staged
   * filename akan ditambah dengan 'filename.xml__timestamp_microsecond'
   * @return Response with data = model SQL object, data2 = Deletion Object
   */
  public function delete(Request $request, string $filename)
  {
    $model = ModelsCsdb::with('initiator')->where('filename', $filename)->first();
    if(!$model) return $this->ret2(400, ["{$filename} failed to delete."]);

    
    if ($model->initiator->id = !$request->user()->id) return $this->ret2(400, ["Deleting {$filename} must be done by {$model->initiator->name}"]);
    if (isset($model->remarks['stage']) AND $model->remarks['stage'] === 'staged') return $this->ret2(400, ["{$filename} has been staged and cannot be deleted."]);
    
    $model->hide(false);
    $model->direct_save = false;
    $time = Carbon::now()->timezone(7);
    $new_filename = ($filename . '__' . $time->timestamp . '-' . $time->microsecond);    
    
    $model->setRemarks('history', Carbon::now().";DELL;Object with filename {$filename} is deleted.;{$request->user()->name}");
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
    
    if(!($create_deleted_db() AND $move_file())) return $this->ret2(400,["{$filename} fails to delete"]);
    $model->delete();    
    return $this->ret2(200, ["{$new_filename} has been created as a result of deleting {$filename}."], ['data' => $model], ['data2' => $insert]);
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
    if($request->user()->id != $deleted_model->deleter_id) return $this->ret2(400, ["Only {$request->user()->name} can restore."]);
    
    $meta = function($stdClass, $fn){ // untuk mengubah seluruh stdClass menjadi array
      $stdClass = get_object_vars($stdClass);
      foreach($stdClass as $k => $v){
        if(is_object($v)) $stdClass[$k] = $fn($v, $fn);
      }
      return $stdClass;
    };
    $meta = $meta(json_decode($deleted_model->meta), $meta);

    // #2. restore by re-creating ModelsCsdb and move file from path 'csdb_deleted' to 'csdb'
    $message = "{$filename} fail to restore.";
    $model = new ModelsCsdb();
    $restore = function() use($meta, $deleted_QB, $filename, &$message, $request, &$model){
      if(ModelsCsdb::find($meta['id'])){
        $message = "Failed to restore due to duplication filename. See {$filename}.";
        return false;
      }
      $model = ModelsCsdb::create($meta);
      if($model){
        $isDel = $deleted_QB->delete();
        if($isDel){
          $new_filename = preg_replace("/__[\S]+/",'',$filename);
          $is_move_file = Storage::disk('csdb')->put($new_filename, Storage::disk('csdb_deleted')->get($filename)) AND Storage::disk('csdb_deleted')->delete($filename);
          if($is_move_file) {
            $message = "{$filename} has been restored";
            $model->setRemarks('history', Carbon::now().";RSTR;Object with filename {$filename} is deleted.;{$request->user()->name}");
            $model->save();
            return $new_filename;
          };
        }
        $model->delete(); // delete model jika gagal save / gagal move file;
      }
      return false;
    };
    // #3. return
    if($filename = $restore()){
      // $filename = preg_replace("/__[\S]+/",'',$filename);
      return $this->ret2(200, [$message], ['data' => $model]);
    } else {
      // $filename = preg_replace("/__[\S]+/",'',$filename);
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
  //       $new_csdb_model->setRemarks('stage', 'unstaged');
  //       $new_csdb_model->setRemarks('remarks',$dom); // tambahkan remarks table berdasarkan identAndStatusSection/descendant::remarks
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
  public function commit(Request $request, string $filename)
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
      $new_model->setRemarks('stage', 'unstaged');
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
    $new_model->setRemarks('securityClassification', CSDB::resolve_securityClassification($new_model->DOMDocument, 'number'));
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
        $new_model->setRemarks('stage', 'unstaged');
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
