<?php

namespace App\Http\Controllers;

use App\Models\Csdb;
use App\Models\Dml;
use App\Rules\Dml\EntryIdent;
use App\Rules\Dml\EntryIssueType;
use App\Rules\Dml\EntryType;
use App\Rules\EnterpriseCode;
use App\Rules\SecurityClassification;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Ptdi\Mpub\CSDB as MpubCSDB;
use Ptdi\Mpub\Helper;

class DmlController extends Controller
{
  #### csdb4 ####
  public function get_dmrl_list(Request $request)
  {
    // $this->model = Csdb::with('initiator');
    // $this->model->orderBy('path');
    // $ret = $this->model->paginate(100);
    // $ret->setPath($request->getUri());
    // return $this->ret2(200, $ret->toArray());

    $obj1 = [
      "filename" => 'cfoo1asasscsascscasas',
      'path' => 'csdb'
    ];
    $obj1_1 = [
      "filename" => 'cfoo1_1asasscsascscasas',
      'path' => 'csdb'
    ];
    $obj11 = [
      "filename" => 'cfoo11asasscsascscasas',
      'path' => 'csdb/n219'
    ];
    $obj12 = [
      "filename" => 'cfoo12asasscsascscasas',
      'path' => 'csdb/n219'
    ];
    $obj111 = [
      "filename" => 'cfoo111asasscsascscasas',
      'path' => 'csdb/n219/amm'
    ];

    $obj21 = ["filename" => 'cfoo21', "path" => 'csdb/male'];
    $obj22 = ["filename" => 'cfoo22', "path" => 'csdb/male'];

    $obj3 = ["filename" => 'xafoo1', "path" => 'xxx'];
    $obj32 = ["filename" => 'xbfooasa', "path" => 'xxx'];
    $obj31 = ["filename" => 'xfoo11', "path" => 'xxx/n219'];

    $allobj = [$obj1, $obj1_1, $obj11, $obj12 ,$obj111, $obj21, $obj22, $obj3, $obj32, $obj31];
    return $this->ret2(200, ['data' => $allobj]);
  }

  #### csdb3 ####

  public function app()
  {
    return view("dml.app");
  }

  // public function get(Request $request)
  // {
  //   $dmls = Csdb::with('initiator')->where('filename', 'like' ,"DML-%")->get();
  //   return $dmls;
  // }
  public function get_dml_list(Request $request)
  {
    $obj1 = [
      "filename" => 'cfoo1',
      'path' => 'csdb'
    ];
    $obj1_1 = [
      "filename" => 'cfoo1_1',
      'path' => 'csdb'
    ];
    $obj11 = [
      "filename" => 'cfoo11',
      'path' => 'csdb/n219'
    ];
    $obj12 = [
      "filename" => 'cfoo12',
      'path' => 'csdb/n219'
    ];
    $obj111 = [
      "filename" => 'cfoo111',
      'path' => 'csdb/n219/amm'
    ];

    $obj21 = ["filename" => 'cfoo21', "path" => 'csdb/male'];
    $obj22 = ["filename" => 'cfoo22', "path" => 'csdb/male'];

    $obj3 = ["filename" => 'xafoo1', "path" => 'xxx'];
    $obj32 = ["filename" => 'xbfooasa', "path" => 'xxx'];
    $obj31 = ["filename" => 'xfoo11', "path" => 'xxx/n219'];

    $allobj = [$obj1, $obj1_1, $obj11, $obj12 ,$obj111, $obj21, $obj22, $obj3, $obj32, $obj31];
    return Response::make($allobj,200,['content-type' => 'application/json']);

    // $obj1 = [
    //   "filename" => 'foo1',
    //   'path' => 'csdb'
    // ];
    // $obj11 = [
    //   "filename" => 'foo11',
    //   'path' => 'csdb/n219'
    // ];
    // $obj12 = [
    //   "filename" => 'foo12',
    //   'path' => 'csdb/n219'
    // ];
    // $obj111 = [
    //   "filename" => 'foo111',
    //   'path' => 'csdb/n219/amm'
    // ];
    // $allobj = [$obj1, $obj11, $obj12 ,$obj111];
    // $append = function($array, $path, $csdbObject, $callback){
    //   $exploded_path = explode("/", $path);
    //   $loop = 0;
    //   $maxloop = count($exploded_path);

    //   $folder = "__".$exploded_path[$loop];
    //   while($loop < $maxloop){
    //     $array[$folder] = $array[$folder] ?? [];
    //     if(isset($exploded_path[$loop+1])){
    //       unset($exploded_path[$loop]);
    //       $newpath = join("/",$exploded_path); // new path
    //       $array[$folder]  = $callback($array[$folder], $newpath, $csdbObject, $callback);
    //       return $array;
    //       break;
    //     }
    //     $loop++;
    //   }
    //   $array[$folder][] = $csdbObject;
    //   return $array;
    // };
    // foreach($allobj as $k => $obj){
    //   $allobj = $append($allobj, $obj['path'] ,$obj, $append);
    //   unset($allobj[$k]);
    // }
    // dd($allobj);
    $this->model = Csdb::with('initiator');
    $this->search($request->get('filenameSearch'));
    $this->model->where('filename', 'like', "DML-%");
    // $ret = $this->model->paginate(15);
    // $ret->setPath($request->getUri());
    // return $ret;
    // dd($this->model->get()->toJson());
    return $this->model->get();
  }

  public function get_csl_list(Request $request)
  {
    $this->model = Csdb::with('initiator');
    $this->search($request->get('filenameSearch'));
    $this->model->where('filename', 'like', "D%");
    // $this->model->where('filename', 'like', "CSL-%");
    // $ret = $this->model->paginate(15);
    // $ret->setPath($request->getUri());
    $ret = $this->model->get();
    return $ret;
  }

  public function get_list(Request $request)
  {
    $this->model = Csdb::with('initiator');
    $this->search($request->get('filenameSearch'));
    $this->model->where('filename', 'like', "DML-%");
    if ($request->get('dml')) {
      $this->model->where('filename', 'like', "DML-%");
    } elseif ($request->get('csl')) {
      $this->model->where('filename', 'like', "CSL-%");
    } else {
      // ini akan membuat query tidak akan membaca apakah sudah di delete/belum. Artinya akan query all DML/CSL
      $this->model->where('filename', 'like', "DML-%")->orWhere('filename', 'like', "CSL-%");
    }
    $ret = $this->model
      // ->where('remarks', 'not like', '%"crud":"deleted"%')
      ->paginate(15);
    $ret->setPath($request->getUri());
    return $ret;
  }

  /**
   * akan membuat file baru issueNumber tetap, inWork '01'
   * fungsi ini dipakai ketika dml sudah di issue(), namun ada content yang harus di edit
   */
  public function edit(Request $request)
  {
    $filename = $request->route('filename');
    if (!$filename or substr($filename, 0, 3) != 'DML') return $this->ret2(400, ['Only DML is can be editted here.']);
    $csdb_model = Csdb::where('filename', $filename)->first();
    if ($csdb_model->initiator_id != $request->user()->id) return $this->ret2(400, ["Only Initiator ({$csdb_model->initiator->name}) can edit."]);
    if ($csdb_model->editable) return $this->ret2(400, ["{$filename} is still in editable."]);

    $dom = MpubCSDB::importDocument(storage_path($csdb_model->path), $csdb_model->filename);
    $domxpath = new \DOMXPath($dom);
    $issueInfo = $domxpath->evaluate("//identAndStatusSection/dmlAddress/dmlIdent/issueInfo")[0];
    $issueInfo->setAttribute('inWork', '01');

    $new_filename = MpubCSDB::resolve_DocIdent($dom);
    if ($new_csdb_model = Csdb::where('filename', $new_filename)->first()) return $this->ret2(400, ["This DML cannot be editted due duplication filename of {$new_filename}."]);
    $save = $dom->C14NFile(storage_path($csdb_model->path) . DIRECTORY_SEPARATOR . $new_filename);
    if ($save) {
      $new_csdb_model = Csdb::create([
        'filename' => $new_filename,
        'path' => $csdb_model->path,
        'editable' => 1,
        'initiator_id' => $csdb_model->initiator_id,
      ]);
      if($csdb_model->remarks["securityClassification"]){
        $new_csdb_model->setRemarks('securityClassification', $csdb_model->remarks["securityClassification"]);
      }
      if ($new_csdb_model) {
        return $this->ret2(200, ["New {$new_csdb_model->filename} has been created."]);
      }
    }
    return $this->ret2(400, ["{$filename} failed to open edit."]);
  }

  /**
   * akan membuat file baru issuNumber tetap, inWork++
   */
  public function commit(Request $request, string $filename)
  {
    // $filename = $request->route('filename');
    if (!$filename or substr($filename, 0, 3) != 'DML') return $this->ret2(400, ['Only DML is can be commited here.']);
    $csdb_model = Csdb::where('filename', $filename)->first();
    if ($csdb_model->initiator_id != $request->user()->id) return $this->ret2(400, ["Only Initiator ({$csdb_model->initiator->name}) can commit."]);
    if (!$csdb_model->editable) return $this->ret2(400, ['This DML cannot re commit. You might have issue this DML at previous.']);

    $dom = MpubCSDB::importDocument(storage_path($csdb_model->path), $csdb_model->filename);

    // validate BREX dan XSI di sini
    $validateXSI = MpubCSDB::validate('XSI', $dom);
    if (!$validateXSI) {
      return $this->ret2(400, MpubCSDB::get_errors(true));
    }

    $dom = MpubCSDB::commit($dom);
    if (!$dom) return $this->ret2(400, MpubCSDB::get_errors(true, 'commit'));

    $new_filename = MpubCSDB::resolve_DocIdent($dom);
    if ($new_csdb_model = Csdb::where('filename', $new_filename)->first()) return $this->ret2(400, ["This DML cannot be commited due duplication filename of {$new_filename}."]);
    $save = $dom->C14NFile(storage_path($csdb_model->path) . DIRECTORY_SEPARATOR . $new_filename);
    if ($save) {
      $new_csdb_model = Csdb::create([
        'filename' => $new_filename,
        'path' => $csdb_model->path,
        'editable' => 1,
        'initiator_id' => $csdb_model->initiator_id,
      ]);
      $new_csdb_model->setRemarks('stage', 'unstaged');
      $new_csdb_model->setRemarks('securityClassification', $csdb_model->remarks['securityClassification']);
      return $this->ret2(200, ["New {$new_csdb_model->filename} has been created."]);
    }
    return $this->ret2(400, ["{$filename} failed to commit."]);
  }

  /**
   * akan membuat file baru dengan issueNumber++ dan inWork '00'
   */
  public function issue(Request $request)
  {
    $filename = $request->route('filename');
    if (!$filename or substr($filename, 0, 3) != 'DML') return $this->ret2(400, ['Only DML is can be issued here.']);
    $csdb_model = Csdb::where('filename', $filename)->first();
    if ($csdb_model->initiator_id != $request->user()->id) return $this->ret2(400, ["Only Initiator ({$csdb_model->initiator->name}) can issue."]);
    if (!$csdb_model->editable) return $this->ret2(400, ['This DML cannot re issued. You have to open edit, then re issue.']);


    $dom = MpubCSDB::importDocument(storage_path($csdb_model->path), $csdb_model->filename);
    $domxpath = new \DOMXPath($dom);
    $issueInfo = $domxpath->evaluate("//identAndStatusSection/dmlAddress/dmlIdent/issueInfo")[0];
    $issueNumber = (int)$issueInfo->getAttribute('issueNumber');
    $issueNumber = str_pad($issueNumber + 1, 3, '0', STR_PAD_LEFT);
    $issueInfo->setAttribute('issueNumber', $issueNumber);
    $issueInfo->setAttribute('inWork', '00');

    // dd('disini harus validate BREX dan XSI');
    $validateXSI = MpubCSDB::validate('XSI', $dom);
    if (!$validateXSI) return $this->ret2(400, MpubCSDB::get_errors(true));

    $new_filename = MpubCSDB::resolve_DocIdent($dom);
    if ($new_csdb_model = Csdb::where('filename', $new_filename)->first()) return $this->ret2(400, ["This DML has been issued with name {$new_filename}."]);
    $save = $dom->C14NFile(storage_path($csdb_model->path) . DIRECTORY_SEPARATOR . $new_filename);
    if ($save) {
      $new_csdb_model = Csdb::create([
        'filename' => $new_filename,
        'path' => $csdb_model->path,
        'editable' => 0,
        'initiator_id' => $csdb_model->initiator_id,
      ]);
      if ($new_csdb_model) {
        $new_csdb_model->setRemarks('stage', 'staged');
        $new_csdb_model->setRemarks('securityClassification', $csdb_model->remarks['securityClassification']);
        return $this->ret2(200, ["New {$new_csdb_model->filename} has been created with doesnt have capability to edit."]);
      }
    }
    return $this->ret2(400, ["{$filename} failed to issue."]);
  }

  /**
   * pada request, tinggal tambah dmlRef di frontend. Nanti tambahkan $otherOptions['dmlRef'] = ['DML-...', 'DML-...'];
   * @return App\Models\Csdb
   */
  public function create(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'modelIdentCode' => 'required',
      'originator' => 'required',
      'dmlType' => 'required',
      'securityClassification' => 'required',
      'brexDmRef' => ['required', function (string $attribute, mixed $value,  Closure $fail) {
        if (count(explode("_", $value)) < 3) $fail("The {$attribute} must contain IssueInfo and Language.");
        $decode = Helper::decode_dmIdent($value);
        if ($decode and $decode['dmCode']['infoCode'] != '022') $fail("The {$attribute} infoCode must be '022'.");
      }],
      'remarks' => 'array',
    ]);

    if ($validator->fails()) {
      return $this->ret2(400, [$validator->getMessageBag()->getMessages()]);
    }

    $validator->validated();

    $dml_model = new Dml();
    // $dml_model->setWith(['initiator']);
    $dml_model->direct_save = false;
    $otherOptions = [];
    $dml_model->create_xml($request->get('modelIdentCode'), $request->get('originator'), $request->get('dmlType'), $request->get('securityClassification'), $request->get('brexDmRef'), $request->get('remarks'), $otherOptions);
    $dml_model->initiator; // supaya ada initiator saat return
    
    $dml_model->saveModelAndDOM();
    return $this->ret2(200, ["{$dml_model->filename} has been created."], ['dml' => $dml_model]);
  }

  public function addEntry(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'filename' => 'required',
      'issueType' => [new EntryIssueType],
      'dmlEntryType' => [new EntryType],
      'entryIdent' => 'required',
      'securityClassification' => ['required', new SecurityClassification(true)],
      'enterpriseName' => 'required',
      'enterpriseCode' => [new EnterpriseCode(false)],
      'remarks' => 'array',
    ]);

    if ($validator->fails()) {
      return $this->ret2(400, [$validator->getMessageBag()->getMessages()]);
    }

    $validator->validated();

    $csdb_model = Dml::where('filename', $request->get('filename'))->first();
    if ($csdb_model->initiator->id != $request->user()->id) return $this->ret2(400, ["You cannot add entry unless you are the initiator of the {$request->get('filename')}"]);
    if (!$csdb_model->editable) return $this->ret2(400, ["This DML is not enabled to change. It may be has been issued. You must open edit this DML."]);

    $otherOptions = [];
    $otherOptions['issueType'] = $request->get('issueType');
    $otherOptions['dmlEntryType'] = $request->get('dmlEntryType');
    $add = $csdb_model->add_dmlEntry($request->get('entryIdent'), $request->get('securityClassification'), [$request->get('enterpriseName'), $request->get('enterpriseCode')], $request->get('remarks'), $otherOptions);
    if ($add[0]) {
      $csdb_model->saveModelAndDOM();
      return $this->ret2(200, ["{$request->get('entryIdent')} has been added to {$request->get('filename')}."]);
    } else {
      return $this->ret2(400, [$add[1], "{$request->get('entryIdent')} is failed to add into {$request->get('filename')}."]);
    }
    return '';
  }

  /**
   * belum mengakomodir element <answer>
   * @return array
   */
  public function getEntry(Request $request)
  {
    $filename = $request->route('filename');
    $model = Csdb::where('filename', $filename)->first();
    $dom = MpubCSDB::importDocument(storage_path($model->path), $model->filename);
    $dmlEntries = MpubCSDB::identifyDmlEntries($dom);
    foreach ($dmlEntries as $k => $dmlEntry) {
      $objects = Csdb::with('initiator')->where('filename', 'like', "%{$dmlEntry['code']}%")->get();
      $dmlEntries[$k]['objects'] = $objects;
    }
    return $dmlEntries;
  }

  // ############# untuk stage #############
  public function get_csl_forstaging(Request $request)
  {
    $csl_models = Csdb::where('filename', 'like', "CSL-%")
      ->where('remarks', 'like', '%"stage":"unstaged"%')
      ->get();
    return $csl_models;
  }

  /**
   * sementara belum di sediakan fitur $remakrks per dmlEntry CSL. 
   * tambahkan request name=dmlEntryType
   * tambahkan request name=issueType
   * 
   * create CSL, issueNumber latest+1, inWork='01'
   * setiap entryIdent, issueNumber tetap, inWork tetap, editable=1, remarks'stage': tidak diisi
   */
  public function create_csl_forstaging(Request $request, string $filename)
  {
    // #0. validasi inWork dan editable
    $issueInfo = explode("_", $filename)[1];
    if (substr($issueInfo, 4, 2) != '00') return $this->ret2(400, ["inWork {$filename} must be '00'."]);
    $dml_model = Dml::where('filename', $filename)->first();
    if (!$dml_model) return $this->ret2(400, ["There is no such {$filename} in database."]);
    if ($dml_model->editable) return $this->ret2(400, ["{$filename} must be not editable."]);

    $dml_dom = MpubCSDB::importDocument(storage_path($dml_model->path), $dml_model->filename);
    $decoder_ident = Helper::decode_dmlIdent($dml_model->filename, false);
    $dml_domxpath = new \DOMXPath($dml_dom);

    $securityClassification = $dml_domxpath->evaluate("string(//dmlStatus/security/@securityClassification)");
    $brexDmRef = MpubCSDB::resolve_dmIdent($dml_domxpath->evaluate("//dmlStatus/brexDmRef")[0]);
    $entries = $request->get('objects') ?? [];

    // #1. validasi terhadap duplikasi code pada $request->get('objects'); dan validasi jika tidak ada dmlEntry
    $en = array_map(fn ($entryIdent) => $entryIdent = preg_replace("/_.+/", '', $entryIdent), $entries);
    $en = array_count_values($en);
    foreach ($en as $entryIdent => $dupplication) {
      if ($dupplication > 1) return $this->ret2(400, ['The selected object is only one to choose. The issue info and language on its filename is counted.']);
    }
    if (empty($en)) return $this->ret2(400, ["There must be at least one dml entry."]);

    // #2. validasi object terhadap DMRL.
    $en = [];
    foreach ($entries as $key => $entryIdent) {
      $ident = Helper::decode_ident($entryIdent);
      $codeType = array_keys($ident)[0]; // output eg.: 'dmCode', 'pmCode', etc
      $check_to_currentdml_xpath = Dml::generate_xpath_for_dmlEntry_checking($ident, $codeType);
      $res = $dml_domxpath->evaluate($check_to_currentdml_xpath);
      if ($res->length <= 0) return $this->ret2(400, ["Fail to push. Check your DMRL requirement file"]);
      $sc = $dml_domxpath->evaluate("string(security/@securityClassification)", $res[$res->length - 1]);
      $rsp_en = $dml_domxpath->evaluate("string(responsiblePartnerCompany/enterpriseName)", $res[$res->length - 1]);
      $rsp_ec = $dml_domxpath->evaluate("string(responsiblePartnerCompany/@enterpriseCode)", $res[$res->length - 1]);
      $en[$entryIdent] = [
        'securityClassification' => $sc,
        'enterpriseName' => $rsp_en,
        'enterpriseCode' => $rsp_ec,
      ];
    }
    // #3. create CSL and add dmlEntry
    $remarks =  $request->get('remarks') ?? [];
    array_unshift($remarks, "This CSL is required to comply the {$filename}");
    $csl_model = new Dml();
    $csl_model->direct_save = false;
    $csl_model->create_xml($decoder_ident['dmlCode']['modelIdentCode'], $decoder_ident['dmlCode']['senderIdent'], 'S', $securityClassification, $brexDmRef, $remarks, []);
    foreach ($en as $entryIdent => $option) {
      if ($entry_model = Csdb::where('filename', $entryIdent)->first()) {
        $entry_model->editable = 1;

        $otherOptions = [
          'dmlEntryType' => $request->get('dmlEntryType') ?? 'new',
        ];
        $add = $csl_model->add_dmlEntry($entryIdent, $option['securityClassification'], [$option['enterpriseName'], $option['enterpriseCode']], $otherOptions);
        if ($add[0]) {
          $entry_model->save();
        }
      }
    }
    $csl_model->setRemarks('stage', 'unstaged');
    $csl_model->setRemarks('stager_id', $request->user()->id);
    // disini belum perlu validate brex, kecuali jika ingin issued / staged (fase dari stagging to issued, kalo ini kan fase editing to staging);

    // #3. save and return
    if (MpubCSDB::validate('XSI', $csl_model->DOMDocument)) {
      $csl_model->editable = 1;
      $csl_model->saveModelAndDOM();
      return $this->ret2(200, ["{$csl_model->filename} has been in staging phase. Check on staging page."], ["csl" => $csl_model]);
    } else {
      return $this->ret2(400, MpubCSDB::get_errors(true));
    }
  }

  public function get_csl_staging_list(Request $request)
  {
    $this->model = Csdb::with('initiator');
    $this->search($request->get('filenameSearch'));
    $this->model->where('filename', 'like', "CSL-%");
    $this->model->where('remarks', 'like' ,'%"stage":"staging"%');
    $ret = $this->model->paginate(15);
    $ret->setPath($request->getUri());
    return $ret;

    $csl_models = Dml::where('filename', 'like', 'CSL-%')->where('remarks', 'like', '%"stage":"staging"%')->get();
    return $csl_models;
  }

  /**
   * CSL juga bisa di update di sini oleh user
   */
  public function dmlupdate(Request $request, string $filename)
  {
    // #0. validation
    $dml_model = Dml::where('filename', $filename)->first();
    if ($request->user()->id != $dml_model->initiator_id) return $this->ret(400, ["Only Initiator DML can do an update."]);
    $validator = Validator::make($request->all(), [
      'ident-securityClassification' => ['required', new SecurityClassification(true)],
      'ident-brexDmRef' => ['required', function (string $attribute, mixed $value, Closure $fail) {
        if (!Helper::decode_dmIdent($value)) {
          $fail("The {$attribute} is wrong rule.");
        }
      }],
      'entryIdent' => [fn (string $attribute, mixed $value, Closure $fail) => count($value) !== count(array_unique($value)) ? $fail("Entry Ident must be unique.") : ''],
      'entryIdent.*' => ['required', new EntryIdent($filename)],
      'dmlEntryType.*' => [new EntryType],
      'issueType.*' => [new EntryIssueType],
      'securityClassification.*' => [new SecurityClassification(false)],
      'enterpriseCode.*' => [new EnterpriseCode(false)],
      'enterpriseName.*' => ['required'],
    ]);
    if ($validator->fails()) {
      return $this->ret2(400, [$validator->getMessageBag()->getMessages()]);
    }
    $validator->validated();
    $dml_model->DOMDocument = MpubCSDB::importDocument(storage_path($dml_model->path), $dml_model->filename);
    $dml_model->direct_save = false;

    // #0. update identAndStatusSection
    $ident = [
      'securityClassification' => $request->get('ident-securityClassification'),
      'brexDmRef' => $request->get('ident-brexDmRef'),
      'remarks' => $request->get('ident-remarks'),
    ];
    $dml_model->updateIdentAndStatusSection($ident);

    $entryIdents = $request->get('entryIdent');
    $dmlEntryTypes = $request->get('dmlEntryType');
    $issueTypes = $request->get('issueType');
    $securityClassifications = $request->get('securityClassification');
    $enterpriseNames = $request->get('enterpriseName');
    $enterpriseCodes = $request->get('enterpriseCode');
    $remarkses = $request->get('remarks') ?? []; // harusnya tidak ada lagi isset disini. perbaiki nanti di frontend dan di sini lakukan validasi

    // #1. remove all dmlEntry
    if($entryIdents){
      $dmlContent = $dml_model->DOMDocument->getElementsByTagName("dmlContent")[0];
      while ($dmlContent->firstElementChild) {
        $dmlContent->firstElementChild->remove();
      }
      $dml_model->DOMDocument->saveXML();
      foreach ($entryIdents as $pos => $entryIdent) {
        $remarks = isset($remarkses[$pos]) ? [$remarkses[$pos]] : [];
        $otherOptions = [
          'issueType' => $issueTypes[$pos],
          'dmlEntryType' => $dmlEntryTypes[$pos],
        ];
        $add = $dml_model->add_dmlEntry($entryIdent, $securityClassifications[$pos], [$enterpriseNames[$pos], $enterpriseCodes[$pos]], $remarks, $otherOptions);
        if (!$add[0]) return $this->ret2(400, [$add[1]]);
      }
      $dml_model->DOMDocument->saveXML();
    }

    // #2. tambkan save remarks berdasarkan identAndStatusSection/descendant::remarks/para
    $dml_model->setRemarks('remarks');

    $dml_model->saveModelAndDOM();
    return $this->ret2(200, ['Update Success.']);
  }

  /**
   * remark ['stage'] itu cuma ada unstaged, staging, staged, deleted;
   * CSL nya seperti di issue, yaitu issueNumber++, inWork='00', editable=0, tapi remarks['stage':'staging']
   * csl model lama di database akan di replace filename, sedangkan file distorage akan bertambah dikarenakan filename berubah
   * setiap csl entry, status stage remarks menjadi 'unstaged'
   * tapi, setiap csl entry tidak diubah issueNumber atau inWork number nya
   */
  public function push_csl_forstaging(Request $request, string $filename)
  {
    $csl_model = Dml::where('filename', $filename)->first();
    $csl_model->direct_save = false;

    // #1. ubah issueNumber dan inWork
    $csl_model->DOMDocument = MpubCSDB::importDocument(storage_path($csl_model->path), $csl_model->filename);
    $csl_domxpath = new \DOMXPath($csl_model->DOMDocument);
    $issueInfo = $csl_domxpath->evaluate("//identAndStatusSection/dmlAddress/dmlIdent/issueInfo")[0];
    $issueNumber = $issueInfo->getAttribute('issueNumber');
    $issueNumber++;
    $issueNumber = str_pad($issueNumber, 3, '0', STR_PAD_LEFT);
    $issueInfo->setAttribute('issueNumber', $issueNumber);
    $issueInfo->setAttribute('inWork', '00');
    $csl_model->DOMDocument->saveXML();

    // #2. set editable and remarks stage
    $new_filename = MpubCSDB::resolve_DocIdent($csl_model->DOMDocument);
    $csl_model->filename = $new_filename;
    $csl_model->editable = 0;
    $csl_model->setRemarks('stage', 'staging');
    $csl_model->setRemarks('stager_id', $request->user()->id);

    // #3. validasi XSI dan BREX
    $validateXSI = MpubCSDB::validate('XSI', $csl_model->DOMDocument);
    $validateBREX = true; // validate BREX disini nanti
    if (!($validateXSI && $validateBREX)) return $this->ret2(400, MpubCSDB::get_errors());

    // #4. validasi duplikasi new filename
    if (Csdb::where('filename', $new_filename)->first()) return $this->ret2(400, ["When {$filename} changed into {$new_filename}, it failed due to dupplication object."]);

    // #5. save and return 200
    $csl_model->saveModelAndDOM();
    return $this->ret2(200, ["Push to stage is success. {$filename} is issued by changing filename into {$new_filename}."]);
  }

  /**
   * csl issueNumber tetap, inWorkNumber tetap (umumnya csl yang sudah issue baru bisa masuk ke path decline)
   */
  public function decline_csl_forstaging(Request $request, string $filename)
  {
    $csl_model = Dml::where('filename', $filename)->first();
    $csl_model->setRemarks('stage', 'unstaged');
    $csl_model->setRemarks('stager_id', $request->user()->id);
    return $this->ret2(200, ["{$filename} is remarked as unstaged. You may referesh page."]);
  }

  // diganti CsdbController@delete
  // public function deletedml(Request $request, string $filename)
  // {
  //   $csl_model = Dml::where('filename', $filename)->first();
  //   $csl_model->setRemarks('stage','deleted');
  //   $csl_model->setRemarks('stager_id',$request->user()->id);
  //   return $this->ret2(200, ["{$filename} is remarked as deleted."]);
  // }

  /**
   * setiap issueInfo object entry tetap. issueNumber tetap, inWork tetap
   * issueInfo CSL nya juga tetap. issueNumber tetap, inWork tetap
   * namun csl, object entry 'editable=0' dan remarks['stage':'staged']
   */
  public function acceptcsl(Request $request, string $filename)
  {
    $csl_model = Dml::where('filename', $filename)->first();
    $csl_model->direct_save = false;
    $csl_model->DOMDocument = MpubCSDB::importDocument(storage_path($csl_model->path), $csl_model->filename);
    $dmlEntries = MpubCSDB::identifyDmlEntries($csl_model->DOMDocument);

    // #1. Mengambil model dmlEntry
    $nothingEntry = [];
    $dmlEntries = array_map(function ($dmlEntry) use (&$nothingEntry) {
      $dmlEntry['model'] = Csdb::where('filename', $dmlEntry['code'] . $dmlEntry['extension'])
        ->where('remarks', 'not like', '%"stage":"staged"%')
        ->where('remarks', 'not like', '%"stage":"deleted"%')
        ->first();
      if (!$dmlEntry['model']) ($nothingEntry[] = $dmlEntry['code'] . $dmlEntry['extension']);
      return $dmlEntry;
    }, $dmlEntries);
    if (!empty($nothingEntry)) return $this->ret2(400, ["CSL entry must not be 'staged' or 'deleted'. Ref: " . join(", ", $nothingEntry) . "."]);


    // #2. Set remakrs staged
    $csl_model->editable = 0;
    $csl_model->setRemarks('stage', 'staged');
    $csl_model->setRemarks('stager_id', $request->user()->id);
    $csl_model->save();
    foreach ($dmlEntries as $position => $dmlEntry) {
      $dmlEntry['model']->direct_save = false;
      $dmlEntry['model']->editable = 0;
      $dmlEntry['model']->setRemarks('stage', 'staged');
      $dmlEntry['model']->setRemarks('stager_id', $request->user()->id);
      $dmlEntry['model']->save();
    }

    return $this->ret2(200, ["{$filename} is successfully staged."]);
  }
}
