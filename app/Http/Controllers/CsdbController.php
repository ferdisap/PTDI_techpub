<?php

namespace App\Http\Controllers;

use App\Models\Csdb as ModelsCsdb;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use DOMElement;
use DOMNode;
use DOMXPath;
use Gumlet\ImageResize;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
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

class CsdbController extends Controller
{
  use Validation;

  ################# NEW for csdb3 #################
  public function app()
  {
    return view('csdb3.app');
  }

  /**
   * akan membuat file baru dengan issueNumber largest dan inWork '01'
   * jika filename code ada yang sama di directory, maka issueNumber tetap(largest) dan inWork largest+1. Ini sama seperti fitur commit
   * issueNumber++ adalah sebuah fitur yang hanya bisa digunakan jika reviewer sudah memvalidasi dan akan di-load ke csdb
   */
  public function create(Request $request)
  {
    // #1. create dom
    $proccessid = CSDB::$processid = self::class . "::create";
    $file = $request->file('entity');
    if ($file) {
      $dom = new ICNDocument();
      $dom->load($file->getPath(), $file->getFilename()); // nama, path, dll masih merefer ke tmp file
    } else {
      $xmlstring = $request->get('xmleditor');
      $dom = CSDB::importDocument('', '', trim($xmlstring)); // akan false jika tidak bisa jad DOM
    }
    if (!$dom) return $this->ret2(400, ['Failed to create csdb.'], ["xmleditor" => CSDB::get_errors(true, $proccessid)]);
    CSDB::$processid = '';

    // #2. validasi filename,rootname dom
    if ($dom instanceof \DOMDocument) {
      if (!($validateRootname = CSDB::validateRootname($dom))) {
        return $this->ret2(400, [["xmleditor" => CSDB::get_errors(true, 'validateRootname')]]);
      }
      $csdb_filename = $validateRootname[1];
      $ident = $validateRootname[2];
      $path = "csdb";
      if ($ident == 'dml') return $this->ret2(400, [['xmleditor' => ['You cannot create DML here.']]]);
    } elseif ($dom instanceof ICNDocument) {
      $csdb_filename = $request->file('entity')->getClientOriginalName();
      if (substr($csdb_filename, 0, 3) != 'ICN') return $this->ret2(400, ["The name of {$csdb_filename} is not accepted."]);
      $ident = 'infoEntity';
      $path = "csdb";
      preg_match("/ICN\-[A-Z0-9]{5}\-[A-Z0-9]{5,10}\-[0-9]{3}\-[0-9]{2}.[a-z]+/", $csdb_filename, $matches);
      if (empty($matches)) {
        return $this->ret2(400, ["{$csdb_filename} is not match with pattern: ICN\-[A-Z0-9]{5}\-[A-Z0-9]{5,10}\-[0-9]{3}\-[0-9]{2}.[a-z]+"]);
      }
    } else {
      return $this->ret2(400, ['Failed to create csdb Object.']);
    }

    // #3. validate Schema Xsd (optional). User boleh uncheck input checkbox xsi_validate
    if (($dom instanceof \DOMDocument) and $request->get('xsi_validate')) {
      CSDB::validate('XSI', $dom);
      if (CSDB::get_errors(false, 'validateBySchema')) {
        return $this->ret2(400, [['xmleditor' => CSDB::get_errors(true, 'validateBySchema')]]);
      }
    }

    // #4. assign inWork into '01' and issueNumber to the highest+1
    if (($dom instanceof \DOMDocument)) {
      $domXpath = new \DOMXPath($dom);
      $code = preg_replace("/_.+/", '', $csdb_filename);
      $collection = array_diff(scandir(storage_path($path))); // harusnya ga usa pakai array_dif. Nanti di cek lagi
      $collection = array_filter($collection, fn ($file) => str_contains($file, $code));
      if (empty($collection)) {
        $issueInfo = $domXpath->evaluate("//identAndStatusSection/{$validateRootname[3]}Address/{$validateRootname[3]}Ident/issueInfo")[0];
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

        $issueInfo = $domXpath->evaluate("//identAndStatusSection/{$validateRootname[3]}Address/{$validateRootname[3]}Ident/issueInfo")[0];
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
      $csdb_filename = CSDB::resolve_DocIdent($dom);
    }

    // #5. validate Brex (optional). User boleh uncheck input checkbox brex_validate
    // setiap create DML, tidak divalidasi BREX, validasi Brex harus dilakukan oleh user secara manual setelah di upload
    // sementara ini ICNDocument tidak di validasi oleh brex saat upload
    if (($ident != 'dml') and ($dom instanceof \DOMDocument) and $request->get('brex_validate') == 'on') {
      CSDB::validate('BREX', $dom, null, storage_path("app/{$path}"));
      if (CSDB::get_errors(false, 'validateByBrex')) {
        return $this->ret2(400, [['xmleditor' => CSDB::get_errors(true, 'validateByBrex')]]);
      }
    }

    // #6. saving
    if ($dom instanceof \DOMDocument) {
      $save = $dom->C14NFile(storage_path($path) . DIRECTORY_SEPARATOR . $csdb_filename);
    } else {
      $save = $file->storeAs("../{$path}", $csdb_filename);
    }
    if ($save) {
      $new_csdb_model = ModelsCsdb::create([
        'filename' => $csdb_filename,
        'path' => $path,
        'editable' => 1,
        'initiator_id' => $request->user()->id,
      ]);
      if ($new_csdb_model) {
        $new_csdb_model->setRemarks('stage', 'unstaged');
        return $this->ret2(200, ["New {$new_csdb_model->filename} has been created."]);
      }
    }
    return $this->ret2(400, ["{$csdb_filename} failed to issue."], ['object' => $new_csdb_model]);
  }

  /**
   * filter by initiator_email
   * filter by stage
   * filter by filenameSearch
   */
  public function get_objects_list(Request $request)
  {
    // $masterCSDB = true; // $request->user()->masterCSDB ?? false
    // if($masterCSDB){
    //   $all = ModelsCsdb::where('initiator_id','like','%');
    // } else {
    //   $all = ModelsCsdb::where('initiator_id',$request->user()->id);
    // }
    $all = ModelsCsdb::with('initiator');
    if ($request->get('initiator_email')) {
      $initiator = User::where('email', $request->get('initiator_email'))->first();
      if ($initiator) {
        $all->where('initiator_id', $initiator->id);
      }
    }
    if ($request->get('stage')) {
      $all->where('remarks', '"stage":"staged"');
    }
    if ($request->get('filenameSearch')) {
      // $all->where('filename', 'like', "%" . $request->get('filenameSearch') . "%");
      $filenameSearch = $request->get('filenameSearch');
      $all->whereRaw("filename LIKE '%{$filenameSearch}%' ESCAPE '\'");
    }
    $ret = $all
      ->where('filename', 'not like', 'DML%')
      ->where('filename', 'not like', 'CSL%')
      // ->where('remarks', 'not like', '"crud":"deleted"')
      ->paginate(15);
    $ret->setPath($request->getUri());
    return $ret;
  }

  /**
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
      return Response::make($dom->C14N(), 200, ['Content-Type' => 'text/xml']);
    }
  }

  public function update(Request $request, string $filename)
  {
    // #0. validasi schema
    $old_filename = $filename;
    $csdb_object = ModelsCsdb::where('filename', $old_filename)->first();
    $old_dom = CSDB::importDocument(storage_path($csdb_object->path), $csdb_object->filename);
    $schema = $old_dom->documentElement->getAttribute('xsi:noNamespaceSchemaLocation');
    if (str_contains($schema, 'dml.xsd')) {
      return $this->ret2(400, ["You cannot update object with schema {$schema} here."]);
    }
    if (!$csdb_object->editable) {
      return $this->ret2(400, ["You cannot update object with the editable status is false."]);
    }

    // #1. create dom
    $proccessid = CSDB::$processid = self::class . "::update";
    $file = $request->file('entity');
    if ($file) {
      $dom = new ICNDocument();
      $dom->load($file->getPath(), $file->getFilename()); // nama, path, dll masih merefer ke tmp file
    } else {
      $xmlstring = $request->get('xmleditor');
      $dom = CSDB::importDocument('', '', trim($xmlstring)); // akan false jika tidak bisa jad DOM
    }
    if (!$dom) return $this->ret2(400, ['Failed to update object.'], ["xmleditor" => CSDB::get_errors(true, $proccessid)]);
    CSDB::$processid = '';

    // #2. validasi filename,rootname dom
    if ($dom instanceof \DOMDocument) {
      if (!($validateRootname = CSDB::validateRootname($dom))) return $this->ret2(400, [["xmleditor" => CSDB::get_errors(true, 'validateRootname')]]);
      $csdb_filename = $validateRootname[1];
      $ident = $validateRootname[2];
      $path = "csdb";
      if ($ident == 'dml') return $this->ret2(400, [['xmleditor' => ['You cannot update DML here.']]]);
    } elseif ($dom instanceof ICNDocument) {
      $csdb_filename = $request->file('entity')->getClientOriginalName();
      $ident = 'infoEntity';
      $path = "csdb";
      preg_match("/ICN\-[A-Z0-9]{5}\-[A-Z0-9]{5,10}\-[0-9]{3}\-[0-9]{2}.[a-z]+/", $csdb_filename, $matches);
      if (empty($matches)) {
        return $this->ret2(400, ["{$csdb_filename} is not match with pattern: ICN\-[A-Z0-9]{5}\-[A-Z0-9]{5,10}\-[0-9]{3}\-[0-9]{2}.[a-z]+"]);
      }
    } else {
      return $this->ret2(400, ['Failed to create csdb Object.']);
    }

    // #3. validate Schema Xsd (optional). User boleh uncheck input checkbox xsi_validate
    if (($dom instanceof \DOMDocument) and $request->get('xsi_validate')) {
      CSDB::validate('XSI', $dom);
      if ($error = CSDB::get_errors(false, 'validateBySchema')) {
        return $this->ret2(400, [['xmleditor' => $error]]);
      }
    }

    // #4. change new filename (if user change) with old filename
    if ($dom instanceof \DOMDocument) {
      $new_filename = CSDB::resolve_DocIdent($dom);
      if ($old_filename != $new_filename) {
        $domXpath = new \DOMXPath($dom);
        $ident = $domXpath->evaluate("//identAndStatusSection/{$validateRootname[3]}Address/{$validateRootname[3]}Ident")[0];
        $old_domXpath = new \DOMXPath($old_dom);
        $old_ident = $old_domXpath->evaluate("//identAndStatusSection/{$validateRootname[3]}Address/{$validateRootname[3]}Ident")[0];

        $ident->replaceWith($dom->importNode($old_ident, true));
        $dom->saveXML();
      }
    }

    // #5. validate Brex (optional). User boleh uncheck input checkbox brex_validate
    // setiap create DML, tidak divalidasi BREX, validasi Brex harus dilakukan oleh user secara manual setelah di upload
    // sementara ini ICNDocument tidak di validasi oleh brex saat upload
    if (($ident != 'dml') and ($dom instanceof \DOMDocument) and $request->get('brex_validate') == 'on') {
      CSDB::validate('BREX', $dom, null, storage_path($csdb_object->path));
      if ($error = CSDB::get_errors(true, 'validateByBrex')) {
        return $this->ret2(400, [['xmleditor' => $error]]);
      }
    }

    // #6. saving
    if ($dom instanceof \DOMDocument) {
      $save = $dom->C14NFile(storage_path($csdb_object->path) . DIRECTORY_SEPARATOR . $csdb_filename);
    } else {
      $save = $file->storeAs("../{$csdb_object->path}", $csdb_filename);
    }
    if ($save) {
      $csdb_object->updated_at = now();
      $csdb_object->save();
      return $this->ret2(200, ["{$csdb_filename} has been saved."]);
    }
    return $this->ret2(400, ["{$csdb_filename} failed to issue."]);
  }

  /**
   * untuk ICN. 
   * Filename = auto generated, based CAGE Codes
   * prefix-cagecode-uniqueIdentifier-issueNumber-sc
   */
  public function uploadICN(Request $request)
  {
    $request->validate([
      // "cagecode" => 'required',// akan memakai latest cagecode
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
    $extension = $file->getClientOriginalExtension();

    $prefix = "ICN";

    // mencari nilai uniqueIdentifier terbesar +1;
    $latestFileName = CSDB::getLatestICNFile(storage_path("csdb"), '0001Z');
    $latestFileName_array = empty($latestFileName) ? [] : explode("-", $latestFileName);

    $cagecode = $request->get('cagecode') ?? $latestFileName_array[1] ?? '';
    if (preg_replace("/[A-Z0-9]{5}/", '', $cagecode) != '') return $this->ret2(400, ['cagecode' => ['Cage code company must contain capital alphabetical or numerical in five length.']]);
    if (!$cagecode) return $this->ret2(400, ['cagecode' => ["Cage Code company is required for naming object."]]);

    $uniqueIdentifier = $latestFileName_array[2] ?? 0;
    $uniqueIdentifier++;
    $uniqueIdentifier = str_pad((int) $uniqueIdentifier, 5, '0', STR_PAD_LEFT);

    $issueNumber = $latestFileName_array[3] ?? 0;
    $issueNumber++;
    $issueNumber = str_pad((int) $issueNumber, 3, '0', STR_PAD_LEFT);

    $securityClassification = str_pad((int) $request->get('securityClassification'), 2, '0', STR_PAD_LEFT);

    $cagecode = '0001Z';
    $filename = "{$prefix}-{$cagecode}-{$uniqueIdentifier}-{$issueNumber}-{$securityClassification}.{$extension}";

    $save = Storage::disk('csdb')->put($filename, $file->getContent());
    if ($save) {
      $new_csdb_model = ModelsCsdb::create([
        'filename' => $filename,
        'path' => 'csdb',
        'editable' => '1',
        'initiator_id' => $request->user()->id,
      ]);
      if ($new_csdb_model) {
        $new_csdb_model->setRemarks('stage', 'unstaged');
        return $this->ret2(200, ["New {$new_csdb_model->filename} has been created."]);
      }
    }
    return $this->ret2(400, ["{$filename} failed to issue."]);
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

    // validate XSI
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

  // public function restore(Request $request, string $filename)
  // {
  //   $model = ModelsCsdb::where('filename', $filename)->first();
  //   if(!$model) return $this->ret2(400, ["{$filename} failed to restore."]);
  //   $model->direct_save = false;
  //   $model->editable = $model->remarks['prev_editable'];
  //   $model->setRemarks("crud", 'restored');
  //   $model->setRemarks("prev_editable", 'undefined');
  //   $model->save();
  //   return $this->ret2(200, ["{$filename} is restored."]);
  // }

  public function get_deletion_list(Request $request)
  {
    $all = DB::table('csdb_deleted');
    $all->where('deleter_id', $request->user()->id);    
    if ($request->get('filenameSearch')) {
      $filenameSearch = $request->get('filenameSearch');
      $all->whereRaw("filename LIKE '%{$filenameSearch}%' ESCAPE '\'");
    }
    $ret = $all
      ->latest()
      ->paginate(15);
    $ret->setPath($request->getUri());

    foreach($ret->items() as $k => $v){
      $v->meta = json_decode($v->meta);

      // $deleter = json_decode(json_encode([
      //   "name" => $request->user()->name,
      //   "email" => $request->user()->email
      // ]));
      // $v->deleter = $deleter;
    }
    return $ret;
  }

  /**
   * akan memindahkan file ke folder csdb_deleted.
   * Object yang sudah di delete, tidak akan bisa diapa-apain kecuali di download
   * Object hanya bisa di delete jika remark->stage != unstaged
   * filename akan ditambah dengan 'filename.xml__timestamp_microsecond'
   */
  public function delete(Request $request, string $filename)
  {
    // dd(Carbon::createFromTimestamp(1707835349,7)->toString());

    $model = ModelsCsdb::with('initiator')->where('filename', $filename)->first();
    if (!$model) return $this->ret2(400, ["{$filename} failed to delete."]);

    $model->hide(false);
    $model_meta = $model->toArray();
    
    if ($model->initiator->id = !$request->user()->id) return $this->ret2(400, ["Deleting {$filename} must be done by {$model->initiator->name}"]);
    if (isset($model->remarks['stage']) and $model->remarks['stage'] == 'staged') return $this->ret2(400, ["{$filename} has been staged and cannot be deleted."]);
    
    $model->direct_save = false;
    $time = Carbon::now()->timezone(7);
    $new_filename = ($filename . '__' . $time->timestamp . '-' . $time->microsecond);
    
    
    $create_deleted_db = fn () => DB::table('csdb_deleted')->insert([
      "filename" => $new_filename,
      "deleter_id" => $request->user()->id,
      "meta" => collect($model_meta),
      "created_at" => Carbon::now(7),
    ]);
    $move_file = fn() => Storage::disk('csdb_deleted')->put($new_filename, Storage::disk('csdb')->get($filename)) AND Storage::disk('csdb')->delete($filename);
    
    if(!($create_deleted_db() AND $move_file())) return $this->ret2(400,["{$filename} fails to delete"]);

    $model->delete();    
    return $this->ret2(200, ["{$new_filename} has been created as a result of deleting {$filename}."]);
  }

  /**
   * hanya digunakan untuk developersaja, tidak untuk end-user
   */
  public function harddelete(Request $request, string $filename)
  {
    $model = ModelsCsdb::where('filename', $filename)->first();
    $delete = Storage::disk('csdb')->delete($filename);
    if ($delete) {
      $model->delete();
    }
    return true;
  }




  ################# NEW by VUE #################
  public function general_index(Request $request)
  {
    return view('csdb.app');
  }

  public function getcsdbdata(Request $request)
  {
    if (!$request->get('project_name')) {
      Project::setFailMessage(['There is no such project name'], 'project_name');
      return response()->json(Project::getFailMessage(true, 'project_name'), 400);
    }
    $csdb = ModelsCsdb::with('initiator')->where('project_name', $request->get('project_name'))->orderBy('filename', 'asc');
    if ($request->get('filename')) {
      $csdb = $csdb->where('filename', $request->get('filename'));
    }
    $csdb = $csdb->get(['filename', 'status', 'initiator_id', 'description', 'created_at', 'updated_at', 'remarks']);
    return $csdb;
  }
  public function postupdate2(Request $request)
  {
    if (!$request->get('project_name')) return $this->ret(400, [['project_name' => ['Project Name shall be exist.']]]);

    // #1. check if old csdb is available
    $old_object = ModelsCsdb::where('filename', $request->get('filename'))->first();
    if (!$old_object) return $this->ret(400, ["The object filename is not exist. You may to go to CREATE page to build one."]);
    if (str_contains($old_object->path, "__")) return $this->ret(400, ["{$old_object->filename} with status {$old_object->status} cannot be modified. It must be returned to used csdb."]);

    // #2. validate initiator
    if ($request->user()->id != $old_object->initiator_id) return $this->ret(400, ["{$old_object->path} Only Initator which can be update this CSDB object."]);

    // #3. create dom based on file uploads or xml editor
    $file = $request->file('entity');
    if ($file) {
      $dom = new ICNDocument();
      $dom->load($file->getPath(), $file->getFilename()); // nama, path, dll masih merefer ke tmp file
      $dom->changeFilename($file->getClientOriginalName());
    } else {
      $xmlstring = $request->get('xmleditor');
      $dom = CSDB::importDocument('', '', trim($xmlstring)); // akan false jika tidak bisa jad DOM
    }


    // #4. validasi dan saving $dom
    if ($dom instanceof \DOMDocument) {
      if (!($validateRootname = CSDB::validateRootname($dom))) {
        return $this->ret(400, [["xmleditor" => CSDB::get_errors(true, 'validateRootname')]]);
      }
      $new_objectFilename = $validateRootname[1];

      // validasi terhadap attribute inWork
      $old_issueInfo = CSDB::importDocument(storage_path("app" . DIRECTORY_SEPARATOR . $old_object->path . DIRECTORY_SEPARATOR), $old_object->filename)->getElementsByTagName('issueInfo')[0];
      $old_inwork = $old_issueInfo->getAttribute('inWork');
      $old_issueNumber = $old_issueInfo->getAttribute('issueNumber');
      $new_issueInfo = $dom->getElementsByTagName('issueInfo')[0];
      $new_inwork = $new_issueInfo->getAttribute('inWork');
      $new_issueNumber = $new_issueInfo->getAttribute('issueNumber');

      // validasi issueNumber. User tidak boleh memperbarui issueNumber disini. IssueNumber hanya bisa di isi saat pertama kali create. Jika ingin mengubah issue number, create Object.
      if ($old_issueNumber != $new_issueNumber) return $this->ret(400, [['xmleditor' => ['you cannot update issueNumber of data module identification here.']]]);

      // validasi incremented inwork
      if (is_numeric($old_inwork)) {
        if ($new_inwork - $old_inwork != 1 and $new_inwork != $old_inwork) return $this->ret(400, [['xmleditor' => ['the inwork number of data module identification must be increment of 1.']]]);
        if ($new_inwork == 99) ($new_inwork = 'AA');
      } elseif ($tes = ((int)$old_inwork) . '' and (isset($tes[0])) and (isset($tes[1]))) { // ini untuk @inwork='AA', dst. Expression pakai AND untuk menghindari jika user nulis @inwork = '9A'. Kalau 'A9' sudah pasti false karena 'A' tidak bisa di convert ke integer
        // dd(is_numeric('A1')); // false
        // dd(is_numeric('01')); // true`
        // dd(is_string('001')); // true
        // dd(is_numeric('9A')); // false
        // dd((int)'A9'); // false (0)
        // dd((int)'9A'); // 9
        // dd((int)'01'); // true (1)
        // dd((int)'00'); // false (0)
        // dd((int)'AA'); // false (0)`
        $count = 0;
        while ($old_inwork < $new_inwork) {
          $old_inwork++;
          $count++;
        }
        if ($count > 1) return $this->ret(400, [['xmleditor' => ['the inwork number of data module identification must be increment of 1.']]]);
      } else return $this->ret(400, [['xmleditor' => ['The inwork number cannot be processed.']]]);

      // validasi terhadap sql dupplication
      // if(ModelsCsdb::where('filename',$new_objectFilename)->first()) return $this->ret(400,[['xmleditor' => ["The object ({$new_objectFilename}) is available. "]]]);

      // validate Schema Xsd (optional). User boleh uncheck input checkbox xsi_validate
      if (($dom instanceof \DOMDocument) and $request->get('xsi_validate')) {
        CSDB::validate('XSI', $dom);
        if (CSDB::get_errors(false, 'validateBySchema')) {
          return $this->ret(400, [['xmleditor' => CSDB::get_errors(true, 'validateBySchema')]]);
        }
      }

      // validate Brex (optional). User boleh uncheck input checkbox brex_validate
      // sementara ini ICNDocument tidak di validasi oleh brex saat upload
      if (($dom instanceof \DOMDocument) and $request->get('brex_validate') == 'on') {
        CSDB::validate('BREX', $dom, null, storage_path("app/csdb/{$old_object->project->name}"));
        if (CSDB::get_errors(false, 'validateByBrex')) {
          return $this->ret(400, [['xmleditor' => CSDB::get_errors(true, 'validateByBrex')]]);
        }
      }

      // #5. saving
      // Old file akan dipindahkan ke __unused dan New file akan di buat kalau berbeda filename lama dan baru atau di commit
      $button = $request->get('button');
      $old_name = $old_object->filename;
      $old_path = $old_object->path;

      // jika button update DAN filename yang lama dan yang baru sama, akan merubah file lama saja
      if ($button == 'update' and ($old_path == ("csdb/{$old_object->project->name}") and $old_name == $new_objectFilename)) {
        // validasi terhadap dmrl. Walaupun ini cuma update, bisa saja dmrl nya sudah di revisi jadi dokumen ini tidak diperlukan lagi
        $firstElementChildName = CSDB::importDocument(storage_path("/app/{$old_object->path}"), $old_object->filename)->firstElementChild->tagName;
        if ($validateRootname[2] != 'dml' and !($r = self::validateByDMRL(storage_path("app/{$old_path}"), $request->get('dmrl'), $old_name, $firstElementChildName))[0]) return $this->ret(400, [[$r[2] => [$r[1]]]]);
        $old_object->update(array_merge($request->all(), ['status' => 'modified']));
        Storage::disk('local')->put("csdb/{$old_object->project->name}" . DIRECTORY_SEPARATOR . $new_objectFilename, $xmlstring);
      }
      // jika commit, filename lama dan baru pasti berbeda, sehingga file lama dipindahkan ke __unused
      else {
        if ($button == 'commit') {
          $domXpath = new \DOMXpath($dom);
          $issueInfo = $domXpath->evaluate("//identAndStatusSection/{$validateRootname[3]}Address/{$validateRootname[3]}Ident/issueInfo")[0];
          if (!$issueInfo) return $this->ret(400, [['xmlEditor' => ['Cannot commit due to issueInfo element not exist']]]);
          $current_inWork = (int)$issueInfo->getAttribute('inWork');
          $current_inWork++;
          $issueInfo->setAttribute('inWork', str_pad($current_inWork, 2, '0', STR_PAD_LEFT));
          $new_objectFilename = CSDB::resolve_DocIdent($dom);
        }
        // validasi terhadap dmrl. Walaupun ini cuma update, bisa saja dmrl nya sudah di revisi jadi dokumen ini tidak diperlukan lagi
        if ($validateRootname[2] != 'dml' and !($r = self::validateByDMRL(storage_path("app/{$old_path}"), $request->get('dmrl'), $new_objectFilename, $validateRootname[2]))[0]) return $this->ret(400, [[$r[2] => [$r[1]]]]);

        $old_object->update(['path' => $old_path . "/__unused", 'status' => 'unused']);
        Storage::disk('local')->move($old_path . DIRECTORY_SEPARATOR . $old_name, $old_path . "/__unused" . DIRECTORY_SEPARATOR . $old_name);
        $new_object = ModelsCsdb::create([
          'filename' => $new_objectFilename,
          'path' => ("csdb/{$old_object->project->name}"),
          'status' => 'new',
          'description' => '',
          // 'editable' => 1,
          'initiator_id' => $request->user()->id,
          'project_name' => $old_object->project->name,
        ]);
        Storage::disk('local')->put("csdb/{$old_object->project->name}" . DIRECTORY_SEPARATOR . $new_objectFilename, $dom->C14N());
        $new_object->setRemarks('title');
      }
      return $this->ret(200, ["saved with filename: {$new_objectFilename}"]);
    } elseif ($dom instanceof ICNDocument) {
      // ICNDocument tidak bisa di commit
      // saat update ICNDocument, tidak bisa ubah filename, melainkan user harus buat baru.
      // validation DMRL masih diperlukan, kalau-kalau DMRL nya pernah diubah (dulu perlu ICN, sekarang tidak perlu ICN)
      if (!(($r = self::validateByDMRL($old_object->path, $request->get('dmrl'), $dom->getFilename(), 'infoEntity'))[0]))  return $this->ret(400, [[$r[2] => [$r[1]]]]);

      $old_object->update(array_merge($request->all(), ['status' => 'modified']));
      $file->storeAs($old_object->path, $old_object->filename);
      return $this->ret(200, ["{$old_object->filename} has been changed with old name."]);
    } else {
      return $this->ret(400, ['Failed tp update csdb.']);
    }
  }

  /**
   * bisa provide csdb string atau sesuai mimenya
   */
  public function getcsdb(Request $request)
  {
    if (!$request->get('filename') and !$request->get('project_name')) {
      Project::setFailMessage(['Object filename and Project Name must be provided.'], 'messages');
      return response()->json(Project::getFailMessage(true, 'messages'), 400);
    }
    if (!$request->mime) {
      $csdb = ModelsCsdb::where('filename', $request->get('filename'))->first();
      $file = Storage::get($csdb->path . DIRECTORY_SEPARATOR . $csdb->filename);
      $mime = Storage::mimeType($csdb->path . DIRECTORY_SEPARATOR . $csdb->filename);
      return response($file, 200, [
        'Content-Type' => $mime
      ]);
    }
  }

  /**
   * mendahulukan uploaded file, baru editor
   */
  public function postcreate2(Request $request)
  {
    // #1. validate form
    if (!($project = Project::find($request->get('project_name')))) {
      Project::setFailMessage(['There is no such project name'], 'project_name');
      Project::setFailMessage(['check the input of Project Name.'], 'INFO');
      return $this->ret(400, [Project::getFailMessage(true, 'project_name')]);
    }
    $dom = null;
    $proccessid = CSDB::$processid = self::class . "::create";

    // #2. create dom
    $file = $request->file('entity');
    if ($file) {
      $dom = new ICNDocument();
      $dom->load($file->getPath(), $file->getFilename()); // nama, path, dll masih merefer ke tmp file
    } else {
      $xmlstring = $request->get('xmleditor');
      $dom = CSDB::importDocument('', '', trim($xmlstring)); // akan false jika tidak bisa jad DOM
    }
    if (!$dom) return $this->ret(400, ['Failed tp create csdb.', ["xmleditor" => CSDB::get_errors(true, $proccessid)]]);

    // #3. validasi filename,rootname dom
    if ($dom instanceof \DOMDocument) {
      if (!($validateRootname = CSDB::validateRootname($dom))) {
        return $this->ret(400, [["xmleditor" => CSDB::get_errors(true, 'validateRootname')]]);
      }
      $csdb_filename = $validateRootname[1];
      $ident = $validateRootname[2];
      $path = "csdb/{$project->name}";
    } elseif ($dom instanceof ICNDocument) {
      $csdb_filename = $request->file('entity')->getClientOriginalName();
      $ident = 'infoEntity';
      $path = "csdb/{$project->name}";
      preg_match("/ICN\-[A-Z0-9]{5}\-[A-Z0-9]{5,10}\-[0-9]{3}\-[0-9]{2}.[a-z]+/", $csdb_filename, $matches);
      if (empty($matches)) {
        return $this->ret(400, ["{$csdb_filename} is not match with pattern: ICN\-[A-Z0-9]{5}\-[A-Z0-9]{5,10}\-[0-9]{3}\-[0-9]{2}.[a-z]+"]);
      }
    } else {
      return $this->ret(400, ['Failed tp create csdb.']);
    }

    // #4. validasi terhadap storage exist
    if (Storage::disk('local')->exists($path . DIRECTORY_SEPARATOR . $csdb_filename) or ModelsCsdb::where('filename', $csdb_filename)->first()) {
      return $this->ret(400, ["{$csdb_filename} is already existed."]);
    }

    // #5. validasi terhadap DMRL
    if (($ident != 'dml') and !(($r = self::validateByDMRL(storage_path("app/$path"), $request->get('dmrl'), $csdb_filename, $ident))[0])) {
      return $this->ret(400, [[$r[2] ?? 'default' => [$r[1]]]]);
    }

    // #6. validate Schema Xsd (optional). User boleh uncheck input checkbox xsi_validate
    if (($dom instanceof \DOMDocument) and $request->get('xsi_validate')) {
      CSDB::validate('XSI', $dom);
      if (CSDB::get_errors(false, 'validateBySchema')) {
        return $this->ret(400, [['xmleditor' => CSDB::get_errors(true, 'validateBySchema')]]);
      }
    }

    // #7. assign inWork number into '01';
    if (($dom instanceof \DOMDocument)) {
      $domXpath = new \DOMXPath($dom);
      $inWork = $domXpath->evaluate("//identAndStatusSection/{$validateRootname[3]}Address/{$validateRootname[3]}Ident/issueInfo/@inWork")[0];
      $inWork->nodeValue = '01';
      $csdb_filename = CSDB::resolve_DocIdent($dom);
    }

    // #8. validate Brex (optional). User boleh uncheck input checkbox brex_validate
    // setiap create DML, tidak divalidasi BREX, validasi Brex harus dilakukan oleh user secara manual setelah di upload
    // sementara ini ICNDocument tidak di validasi oleh brex saat upload
    if (($ident != 'dml') and ($dom instanceof \DOMDocument) and $request->get('brex_validate') == 'on') {
      CSDB::validate('BREX', $dom, null, storage_path("app/{$path}"));
      if (CSDB::get_errors(false, 'validateByBrex')) {
        return $this->ret(400, [['xmleditor' => CSDB::get_errors(true, 'validateByBrex')]]);
      }
    }

    // #8. saving to storage
    $saved = false;
    if ($dom instanceof \DOMDocument) {
      Storage::disk('local')->put($path . DIRECTORY_SEPARATOR . $csdb_filename, $dom->C14N());
      $saved = true;
    } else {
      $request->file('entity')->storeAs($path, $csdb_filename);
      $saved = true;
    }

    // #9. saving to sql
    if ($saved) {
      $new_object = ModelsCsdb::create([
        'filename' => $csdb_filename,
        'path' => $path,
        'description' => $request->get('description'),
        'status' => 'new',
        // 'editable' => 1,
        'initiator_id' => $request->user()->id,
        'project_name' => $project->name,
      ]);
      $new_object->setRemarks('title');
      return $this->ret(200, ["saved with filename: {$csdb_filename}"]);
    }
    return $this->ret(400, ["Error while writing new objects."]);
  }

  public function getrestore2(Request $request)
  {
    $filename = $request->route('filename');
    $project_name = $request->route('project_name');
    $csdb_object_model = ModelsCsdb::where('filename', $filename)->where('project_name', $project_name)->first();
    if (!str_contains($csdb_object_model->path, "/__")) return $this->ret(400, ["{$filename} does not need to restore."]);
    $old_path = $csdb_object_model->path;
    $new_path = "csdb/{$csdb_object_model->project->name}";

    $csdb_object_model->path = $new_path;
    $csdb_object_model->status = 'modified';
    $csdb_object_model->save();
    Storage::disk('local')->move($old_path . DIRECTORY_SEPARATOR . $filename, "{$new_path}/{$filename}");
    return $this->ret(200, ["Restoring {$filename} is success."], ['object' => $csdb_object_model]);
  }

  public function getdelete2(Request $request)
  {
    $user_id = $request->user()->id;
    $filename = $request->route('filename');
    $project_name = $request->route('project_name');
    $csdb_object_model = ModelsCsdb::where('filename', $filename)->where('project_name', $project_name)->first();
    if (str_contains($csdb_object_model, "__deleted")) return $this->ret(400, ["{$filename} has been deleted status."]);
    if ($user_id != $csdb_object_model->initiator_id) return $this->ret(400, ['Only the initiator can delete the object']);

    $old_path = $csdb_object_model->path;
    $new_path = "csdb/{$csdb_object_model->project->name}/__delete";

    $csdb_object_model->path = $new_path;
    $csdb_object_model->status = 'deleted';
    $csdb_object_model->save();
    Storage::disk('local')->move($old_path . DIRECTORY_SEPARATOR . $filename, "{$new_path}/{$filename}");

    $csdb_object_model->hide(['id', 'path']);
    return $this->ret(200, ["{$filename} has been soft deleted."], ["object" => $csdb_object_model]);
  }

  public function postdelete2(Request $request)
  {
    $filename = $request->route('filename');
    $project_name = $request->route('project_name');
    $csdb_model = ModelsCsdb::where('filename', $filename)->where('project_name', $project_name)->first();

    Storage::disk('local')->delete("{$csdb_model->path}/{$csdb_model->filename}");
    $csdb_model->delete();
    return $this->ret(200, ["{$filename} has been HARD deleted. You may to referesh the page."]);
  }


  ################# OLD by Blade #################
  /**
   * mendahulukan uploaded file, baru editor
   */
  // public function postcreate(Request $request)
  // {
  //   // dd($request->all());
  //   // validate form
  //   if (!($project = Project::find($request->get('project_name')))) {
  //     Project::setFailMessage(['There is no such project name'], 'project_name');
  //     return back()->withInput()->with(['result' => 'fail'])
  //       ->withErrors(Project::getFailMessage(true, 'project_name'))
  //       ->withErrors(['check the input of Project Name.'], 'info');
  //   }
  //   $dom = null;
  //   $proccessid = CSDB::$processid = self::class . "::create";

  //   // set type and get dom if xml, by the string or file upload
  //   $type = '';
  //   $file = $request->file('entity');
  //   if ($file and in_array($file->getMimeType(), array('text/xml', 'text/plain'))) {
  //     $type = 'xml';
  //     $xmlstring = file_get_contents($file->getPathname());
  //     $dom = CSDB::importDocument('', '', $xmlstring);
  //   } elseif ($file and in_array($file->getMimeType(), array('image/jpg', 'image/jpeg', 'image/png', 'image/svg'))) {
  //     $type = 'multimedia';
  //   } else {
  //     $type = 'xml';
  //     $xmlstring = $request->get('xmleditor');
  //     $dom = CSDB::importDocument('', '', trim($xmlstring), '', 'tes'); // akan false jika tidak bisa jad DOM
  //   }

  //   // return false jika dom tidak bisa di buat
  //   if ($type == 'xml' and !$dom) {
  //     return back()->withInput()->with(['result' => 'fail'])->withErrors(CSDB::get_errors(true, $proccessid), 'info');
  //   } elseif ($dom) {
  //     // validation: return false jika rootname buka dmodule ataupun pm
  //     if (!($validateRootname = CSDB::validateRootname($dom))) {
  //       return back()->withInput()->with(['result' => 'fail'])->withErrors(CSDB::get_errors(true, 'validateRootname'), 'info');
  //     }
  //     $csdb_filename = $validateRootname[1];
  //     $ident = $validateRootname[2];
  //     $path = "csdb/{$project->name}";
  //   } elseif ($type = 'multimedia') {
  //     $csdb_filename = $request->file('entity')->getClientOriginalName();
  //     $ident = 'infoEntity';
  //     $path = "csdb/{$project->name}";
  //     preg_match("/ICN\-[A-Z0-9]{5}\-[A-Z0-9]{5,10}\-[0-9]{3}\-[0-9]{2}.[a-z]+/", $csdb_filename, $matches);
  //     if (empty($matches)) {
  //       return back()->withInput()->with(['result' => 'fail'])->withErrors(["{$csdb_filename} is not match with pattern: ICN\-[A-Z0-9]{5}\-[A-Z0-9]{5,10}\-[0-9]{3}\-[0-9]{2}.[a-z]+"], 'info');
  //     }
  //   }

  //   // writing: return true or false jika ada/tidak file existing
  //   if (Storage::disk('local')->exists($path . DIRECTORY_SEPARATOR . $csdb_filename)) {
  //     return back()->withInput()->with(['result' => 'fail'])->withErrors(["{$csdb_filename} is already existed."], 'info');
  //   } elseif ($csdb_filename) {

  //     // validate referenced dmrl       
  //     if (!(($r = self::validateByDMRL($path, $request->get('dmrl'), $csdb_filename, $ident))[0])) {
  //       return back()->withInput()->with(['result' => 'fail'])->withErrors($r[1], $r[2] ?? 'default');
  //     }

  //     // saving
  //     $saved = false;
  //     if ($type == 'xml') {
  //       Storage::disk('local')->put($path . DIRECTORY_SEPARATOR . $csdb_filename, $xmlstring);
  //       $saved = true;
  //     } elseif ($type == 'multimedia') {
  //       $request->file('entity')->storeAs($path, $csdb_filename);
  //       $saved = true;
  //     }
  //     if ($saved) {
  //       ModelsCsdb::create([
  //         'filename' => $csdb_filename,
  //         'path' => $path,
  //         'description' => $request->get('description'),
  //         'status' => 'new',
  //         // 'editable' => 1,
  //         'initiator_id' => $request->user()->id,
  //         'project_name' => $project->name,
  //       ]);

  //       return back()->withInput()->with(['result' => 'success'])->withErrors(["saved with filename: {$csdb_filename}"], 'info');
  //     }
  //     return back()->withInput()->with(['result' => 'fail'])->withErrors(["Error while processing {$csdb_filename}"]);
  //   } else {
  //     // return $this->result(false, []);
  //     return back()->withInput()->with(['result' => 'fail']);
  //   }
  // }

  // public function postupdate(Request $request)
  // {
  //   // harusnya ga pakai id, karena filename itu unique jadi pakai filename.
  //   $csdb_object = ModelsCsdb::find($request->get('id'));

  //   // validasi existing and editable
  //   if (!$csdb_object) return back()->withInput()->with(['result' => 'fail'])->withErrors(["{$csdb_object->filename} is not exist. You may to go to CREATE page to build one."], 'info');
  //   if (!$csdb_object->editable) return back()->withInput()->with(['result' => 'fail'])->withErrors(["{$csdb_object->filename} is not enable to edit."], 'info');
  //   if (str_contains($csdb_object->path, "__")) return back()->withInput()->with(['result' => 'fail'])->withErrors(["{$csdb_object->filename} with status {$csdb_object->status} cannot be modified. It must be returned to used csdb."], 'info');

  //   $path = "csdb/{$csdb_object->project->name}";

  //   // validate initiator
  //   if ($request->user()->id != $csdb_object->initiator_id) {
  //     return back()->withInput()->with(['result' => 'fail'])->withErrors(["{$csdb_object->path} Only Initator which can be update this CSDB object."], 'info');
  //   }

  //   $entity = $request->file('entity');
  //   if ($entity and str_contains($entity->getMimeType(), 'text')) {
  //     $request->replace(['xmleditor' => file_get_contents($entity->getPathname())]);
  //     $entity = false;
  //   }
  //   // update by xmleditor
  //   if ($xmlstring = $request->get('xmleditor')) {
  //     $proccessid = CSDB::$processid = self::class . "::update";
  //     $dom = CSDB::importDocument('', '', trim($xmlstring));
  //     if (!$dom) {
  //       return back()->withInput()->with(['result' => 'fail'])->withErrors(CSDB::get_errors(true, $proccessid), 'info');
  //     }

  //     // validate rootname pm or dm, sekaligus mendapatkan filename nya juga
  //     if (!($validateRootname = CSDB::validateRootname($dom))) {
  //       return back()->withInput()->with(['result' => 'fail'])->withErrors(CSDB::get_errors(true, 'validateRootname'), 'info');
  //     }
  //     $csdb_filename = $validateRootname[1];

  //     // validate sequencial attribbute inWork
  //     $old_issueInfo = CSDB::importDocument(storage_path("app" . DIRECTORY_SEPARATOR . $csdb_object->path . DIRECTORY_SEPARATOR), $csdb_object->filename)->getElementsByTagName('issueInfo')[0];
  //     $old_inwork = $old_issueInfo->getAttribute('inWork');
  //     $old_issueNumber = $old_issueInfo->getAttribute('issueNumber');
  //     $new_issueInfo = $dom->getElementsByTagName('issueInfo')[0];
  //     $new_inwork = $new_issueInfo->getAttribute('inWork');
  //     $new_issueNumber = $new_issueInfo->getAttribute('issueNumber');
  //     if ($old_issueNumber != $new_issueNumber) {
  //       // fail, m: you cannot update issueNumber at here.
  //       return back()->withInput()->with(['result' => 'fail'])->withErrors(['you cannot update issueNumber of data module identification here.'], 'info');
  //     }
  //     if ($old_inwork != $new_inwork) {
  //       if ($new_inwork - $old_inwork != 1) {
  //         return back()->withInput()->with(['result' => 'fail'])->withErrors(['the inwork number of data module identification must be increment of 1.'], 'info');
  //       }
  //     }

  //     // validate Schema
  //     if ($request->get('xsi_validate')) {
  //       CSDB::validate('XSI', $dom);
  //       if (CSDB::get_errors(false, 'validateBySchema')) {
  //         return back()->withInput()->with(['result' => 'fail'])->withErrors(CSDB::get_errors(true, 'validateBySchema'), 'info');
  //       }
  //     }

  //     // validate Brex
  //     if ($request->get('brex_validate') == 'on') {
  //       CSDB::validate('BREX', $dom, null, storage_path("app/{$path}"));
  //       if (CSDB::get_errors(false, 'validateByBrex')) {
  //         return back()->withInput()->with(['result' => 'fail'])->withErrors(CSDB::get_errors(true, 'validateByBrex'), 'info');
  //       }
  //     }

  //     // saving to database
  //     $old_name = $csdb_object->filename;
  //     $old_path = $csdb_object->path;
  //     $old_status = $csdb_object->status;
  //     // dd($old_path, $path, $old_name, $csdb_filename);
  //     if ($old_path == $path and $old_name == $csdb_filename) {
  //       $csdb_object->update(array_merge($request->all(), ['status' => 'modified']));
  //       Storage::disk('local')->put($path . DIRECTORY_SEPARATOR . $csdb_filename, $xmlstring);
  //     } else {
  //       // validate referenced dmrl, dilakukan jika filename (tidak termasuk issueNumber dan inWork) nya berbeda dengan yang lama
  //       if (preg_replace("/_\d{3,5}-\d{2}|_[A-Za-z]{2,3}-[A-Z]{2}/", '', $old_name) != preg_replace("/_\d{3,5}-\d{2}|_[A-Za-z]{2,3}-[A-Z]{2}/", '', $csdb_filename)) { // untuk membersihkan inwork dan issue number pada filename
  //         $ident = $validateRootname[2];
  //         if (!(($r = self::validateByDMRL($path, $request->get('dmrl'), $csdb_filename, $ident))[0])) {
  //           return back()->withInput()->with(['result' => 'fail'])->withErrors($r[1], $r[2] ?? 'default');
  //         }
  //       }

  //       ModelsCsdb::create([
  //         'filename' => $csdb_filename,
  //         'path' => $path,
  //         'status' => 'new',
  //         'description' => '',
  //         // 'editable' => 1,
  //         'initiator_id' => $request->user()->id,
  //         'project_name' => $csdb_object->project->name,
  //       ]);

  //       // saving to local
  //       // moving old file
  //       $csdb_object->update(['path' => $old_path . "/__unused", 'status' => 'unused']);
  //       Storage::disk('local')->move($old_path . DIRECTORY_SEPARATOR . $old_name, $old_path . "/__unused" . DIRECTORY_SEPARATOR . $old_name);
  //       // create new file
  //       Storage::disk('local')->put($path . DIRECTORY_SEPARATOR . $csdb_filename, $xmlstring);
  //     }

  //     $url = preg_replace("/filename=.+.xml/", "filename={$csdb_filename}", back()->getTargetUrl());
  //     return back()->setTargetUrl($url)->withInput()->with(['result' => 'success'])->withErrors(["saved with filename: {$csdb_filename}"], 'info');
  //   }
  //   // update by entity jika mime bukan text
  //   elseif ($entity) {
  //     // tidak perlu validate referenced dmrl karena ini update entity sehingga pasti filename nya sama

  //     // validate Brex
  //     if ($request->get('brex_validate') == 'on') {
  //       CSDB::validate('BREX-NONCONTEXT', $csdb_object->path . DIRECTORY_SEPARATOR . $csdb_object->filename, null, storage_path("app"));
  //       if (CSDB::get_errors(false, 'validateByBrex')) {
  //         return back()->withInput()->with(['result' => 'fail'])->withErrors(CSDB::get_errors(true, 'validateByBrex'), 'info');
  //       }
  //     }
  //     // saving to database
  //     $csdb_object->update(array_merge($request->all(), ['status' => 'modified']));

  //     // saving to local
  //     $entity->storeAs($path, $csdb_object->filename);
  //     return back()->withInput()->with(['result' => 'success'])->withErrors(["{$csdb_object->filename} has been deleted from local disk."], 'info');
  //   }
  //   // update ex: description or others if entity or xmleditor is empty
  //   else {
  //     $csdb_object->update(array_merge($request->all(), ['status' => 'modified']));
  //     return back()->withInput()->with(['result' => 'success'])->withErrors(["{$csdb_object->filename} has been updated."], 'info');
  //   }
  // }

  // public function getrestore(Request $request)
  // {
  //   $filename = $request->get('filename');
  //   $csdb_model = ModelsCsdb::where('filename', $filename);

  //   $cm = $csdb_model->first();
  //   Storage::disk('local')->move($cm->path . DIRECTORY_SEPARATOR . $filename, "csdb/{$cm->project->name}/{$filename}");
  //   $csdb_model->update(['status' => 'modified', 'path' => "csdb/{$cm->project->name}"]);

  //   return back()->withInput()->with(['result' => 'success'])->withErrors(["{$filename} has been restored to project {$cm->project->name}.",], 'info');
  // }

  // public function getdelete(Request $request)
  // {
  //   $user_id = $request->user()->id;
  //   $filename = $request->get('filename');
  //   $csdb_model = ModelsCsdb::where('filename', $filename);

  //   // validation initator id
  //   if ($csdb_model->first(['initiator_id'])->initiator_id != $user_id) return back()->withInput()->with(['result' => 'fail'])->withErrors(["Only the initiator can delete the DM."], 'info');

  //   // update table project
  //   $pr = DB::table('project')->where('csdbs', 'like', "%{$csdb_model->first()->id}%")->first();
  //   if ($pr) {
  //     $pr->csdbs = str_replace($csdb_model->first()->id, '', $pr->csdbs);
  //     DB::table('project')->updateOrInsert(['id' => $pr->id], collect($pr)->toArray());
  //   }

  //   // update storage
  //   $cm = $csdb_model->first();
  //   Storage::disk('local')->move($cm->path . DIRECTORY_SEPARATOR . $filename, "csdb/{$cm->project->name}/__deleted/{$filename}");
  //   $cm = $csdb_model->update(['status' => 'deleted', 'path' => "csdb/{$cm->project->name}/__deleted"]);

  //   // update table csdb
  //   $csdb_model->update([
  //     'status' => 'deleted'
  //   ]);

  //   return back()->withInput()->with(['result' => 'success'])->withErrors(["{$filename} has been deleted from local disk.",], 'info');
  // }

  // public function postdelete(Request $request)
  // {
  //   $filename = $request->get('filename');
  //   $csdb_model = ModelsCsdb::where('filename', $filename)->first();

  //   Storage::disk('local')->delete("{$csdb_model->path}/{$csdb_model->filename}");
  //   $csdb_model->delete();
  //   return back()->withInput()->with(['result' => 'success'])->withErrors(["{$filename} has been HARD deleted.",], 'info');
  // }

  // public function index(Request $request)
  // {
  //   $query = array();
  //   foreach ($request->all() as $k => $v) {
  //     switch ($k) {
  //       case 'mic':
  //         if ($v == 'entity') {
  //           $query[] = ['filename', 'like', "%ICN-%"];
  //         } else {
  //           $v = strtoupper($v);
  //           $query[] = ['filename', 'like', "%{$v}%"];
  //         }
  //         break;
  //       case 'status':
  //         $query[] = ['status', '=', $v];
  //         break;
  //       case 'initiator':
  //         $query[] = ['initiator_id', '=', User::where('email', '=', $v)->first('id')->id];
  //         break;
  //     }
  //   }
  //   foreach ($query as $q) {
  //     if (isset($lists)) {
  //       $lists->where($q[0], $q[1], $q[2]);
  //     } else {
  //       $lists = ModelsCsdb::where($q[0], $q[1], $q[2]);
  //     }
  //   }
  //   isset($lists) ? ($lists = $lists->get()) : null;
  //   return  view('csdb.index', [
  //     'listsobj' => $lists ?? null,
  //     'table' => 'csdb',
  //   ]);
  // }

  // public function getcreate(Request $request)
  // {
  //   return view('csdb.create', $request->all());
  // }

  // public function getupdate(Request $request)
  // {
  //   // untuk generate URL
  //   if ($entity = $request->get('entity')) {
  //     $csdb_object = ModelsCsdb::where('filename', 'like', "%{$entity}")->first(['filename', 'path']);
  //     $file = Storage::get($csdb_object->path . DIRECTORY_SEPARATOR . $csdb_object->filename);
  //     $mime = Storage::mimeType($csdb_object->path . DIRECTORY_SEPARATOR . $csdb_object->filename);
  //     if (str_contains($mime, 'image')) {
  //       $scale = $request->get('scale') ?? 50;
  //       $file = new ImageResize(storage_path("app/csdb/{$entity}"));
  //       $file->scale($scale);
  //       $file = $file->output();
  //     }
  //     $r = Response::make($file, 200, ['Content-Type' => $mime]);
  //     return $r;
  //   }


  //   $filename = $request->get('filename');
  //   if (!$filename) {
  //     return back();
  //   }
  //   $csdb_object = ModelsCsdb::where('filename', 'like', "%{$filename}")->first(['id', 'filename', 'path', 'description', 'initiator_id']);
  //   $mime = Storage::mimeType($csdb_object->path . DIRECTORY_SEPARATOR . $csdb_object->filename);
  //   $data = [
  //     'id' => $csdb_object->id,
  //     'description' => $csdb_object->description,
  //     'initiator' => $csdb_object->initiator->email,
  //   ];
  //   if (str_contains($mime, "text")) {
  //     $data = array_merge($data, ['xmleditor' => Storage::get($csdb_object->path . DIRECTORY_SEPARATOR . $csdb_object->filename)]);
  //   } else {
  //     $data = array_merge($data, ['use_xmleditor' => false, 'entitysrc' => route('get_update_csdb_object') . "?entity={$filename}&scale={$request->get('scale')}"]);
  //   }
  //   // return view dan text
  //   if ($csdb_object) {
  //     return view('csdb.update', $data);
  //   } else {
  //     return back();
  //   }
  // }

  // /**
  //  * jika $object type tidak termasuk bagian yang perlu di validasi dmrl, maka dianggap true
  //  * if true, return [true, ''];
  //  * else, return [false, [$text], 'info'];
  //  * @return array
  //  */
  // public function validateByDMRL_xx(string $dmrlfilename = null, string $object_name = '', string $object_type)
  // {
  //   if (!in_array($object_type, ['dmodule', 'pm', 'infoEntity', 'comment', 'dml'])) {
  //     return [true, ''];
  //   }
  //   if ($dmrlfilename == '' or !($dmrl = ModelsCsdb::where('filename', $dmrlfilename)->first(['id', 'filename', 'path'])) or !(Storage::exists($dmrl->path . "/" . $dmrl->filename))) {
  //     return [false, ["No such DMRL"], 'info'];
  //   } else {
  //     $dmrl_dom = CSDB::importDocument(storage_path("app/{$dmrl->path}/"), $dmrl->filename);
  //     if (!CSDB::validate('XSI', $dmrl_dom, 'dml.xsd')) {
  //       $err = CSDB::get_errors(true, 'validateBySchema');
  //       return [false, ["dmrl" => array_merge(["DMRL must be comply to dml.xsd"], $err)]]; // key 'dmrl' ini adalah input name pada HTML
  //     }
  //     $xpath = new \DOMXPath($dmrl_dom);
  //     $dmlEntries = $xpath->evaluate("//dmlEntry");
  //     $nominal_idents = array();
  //     foreach ($dmlEntries as $key => $dmlEntry) {
  //       $ident = str_replace("Ref", '', $dmlEntry->firstElementChild->tagName);
  //       if ($dmlEntry->firstElementChild->tagName == 'infoEntityRef') {
  //         $nominal_idents[] = $dmlEntry->firstElementChild->getAttribute('infoEntityRefIdent');
  //       } else {
  //         $nominal_idents[] = call_user_func_array(CSDB::class . "::resolve_{$ident}Ident", [$dmlEntry->getElementsByTagName("{$ident}RefIdent")[0]]);
  //       }
  //     }
  //     $actual_ident = preg_replace("/_\d{3,5}-\d{2}|_[A-Za-z]{2,3}-[A-Z]{2}/", '', $object_name); // untuk membersihkan inwork dan issue number pada filename
  //     if (!in_array($actual_ident, $nominal_idents)) {
  //       $actual_ident = preg_replace('/\.\w+$/', '', $actual_ident);
  //       return [false, ["{$actual_ident} is not required by the DMRL."], 'info'];
  //     }
  //     return [true, ''];
  //   }
  // }

  // public function getdetail(Request $request)
  // {
  //   if (!($filename = $request->get('filename'))) {
  //     return back();
  //   }

  //   $csdb_model = ModelsCsdb::where('filename', $filename)->first();
  //   if (!isset($csdb_model->id)) {
  //     abort(403, "{$filename} is not available in database.");
  //   }

  //   return view('csdb.detail', [
  //     'object' => $csdb_model,
  //     'id' => $csdb_model->id,
  //     'filename' => $filename,
  //   ]);

  //   // $csdb_model = ModelsCsdb::where('filename',$filename)->first();
  //   // $file = Storage::get("{$csdb_model->path}/{$csdb_model->filename}");

  // }


  public function request(Request $request)
  {
    $csdb = ModelsCsdb::where('filename', $request->get('filename'))->first(['path', 'filename', 'project_name']);
    $file = Storage::get("{$csdb->path}/{$csdb->filename}");
    return response()->json([
      'csdb' => $file
    ], 200);
  }

  // public function fail(array $messages = [], $inputName = ''){
  //   if(!empty($inputName)){
  //    $res = [
  //       'result' => 'fail',
  //       'error' => [
  //         $inputName => $messages,
  //       ]
  //     ];
  //   }
  //   else {
  //     $res = [
  //       'result' => 'fail',
  //       'messages' => $messages,
  //     ];
  //   }

  //   return back()->withInput()->with($res);
  // }

  // /**
  //  * if you want to return message with input @name, please attach input @name as key, and the value contains array of text message. eg. argument#2 = ["dmrl" => ['DMRL column is required']]
  //  */
  // public function result(bool $result, array $messages, array $errors = [])
  // {

  //   $r = [
  //     'result' => $result ? 'success' : 'fail',
  //     'messages' => $messages,
  //   ];
  //   // $e = new MessageBag(['tes']);
  //   // return back()->withInput()->with($r)->withErrors(['dmrl' => ['bar','asa']]); // 'foo' akan dibaca sebagai input@name, ['bar','asa'] adalah valuenya. Bisa juga string saja eg.:'bar'
  //   // return back()->withInput()->with($r)->withErrors(['foo','bar'],'top')->withErrors(['foo','bar']);
  //   // return back()->withInput()->with($r)->withErrors($errors);
  //   // dd(new MessageBag(['foo']));

  //   dd($r);
  //   return back()->withInput()->with($r);
  //   $c = function()use($r){
  //     return back()->withInput()->with($r);    
  //   };
  //   // $e = new MessageBag(['tes']);
  //   return $c();
  // }

}
