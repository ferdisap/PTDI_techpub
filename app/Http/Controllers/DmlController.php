<?php

namespace App\Http\Controllers;

use App\Models\Csdb;
use App\Models\Dml;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Ptdi\Mpub\CSDB as MpubCSDB;
use Ptdi\Mpub\Helper;

class DmlController extends Controller
{
  public function app()
  {
    return view("dml.app");
  }

  public function get(Request $request)
  {
    $dmls = Csdb::with('initiator')->where('filename', 'like' ,"DML-%")->get();
    return $dmls;
  }

  /**
   * akan membuat file baru issueNumber tetap, inWork '01'
   * fungsi ini dipakai ketika dml sudah di issue(), namun ada content yang harus di edit
   */
  public function edit(Request $request)
  {
    $filename = $request->route('filename');
    if(!$filename OR substr($filename,0,3) != 'DML') return $this->ret2(400, ['Only DML is can be editted here.']);
    $csdb_model = Csdb::where('filename',$filename)->first();
    if($csdb_model->initiator_id != $request->user()->id) return $this->ret2(400, ["Only Initiator ({$csdb_model->initiator->name}) can edit."]);
    if($csdb_model->editable) return $this->ret2(400, ["{$filename} is still in editable."]);

    $dom = MpubCSDB::importDocument(storage_path($csdb_model->path),$csdb_model->filename);
    $domxpath = new \DOMXPath($dom);
    $issueInfo = $domxpath->evaluate("//identAndStatusSection/dmlAddress/dmlIdent/issueInfo")[0];
    $issueInfo->setAttribute('inWork', '01');

    $new_filename = MpubCSDB::resolve_DocIdent($dom);
    if($new_csdb_model = Csdb::where('filename', $new_filename)->first()) return $this->ret2(400, ["This DML cannot be editted due duplication filename of {$new_filename}."]);
    $save = $dom->C14NFile(storage_path($csdb_model->path).DIRECTORY_SEPARATOR.$new_filename);
    if($save){
      $new_csdb_model = Csdb::create([
        'filename' => $new_filename,
        'path' => $csdb_model->path,
        'editable' => 1,
        'initiator_id' => $csdb_model->initiator_id,
      ]);
      if($new_csdb_model){
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
    if(!$filename OR substr($filename,0,3) != 'DML') return $this->ret2(400, ['Only DML is can be commited here.']);
    $csdb_model = Csdb::where('filename',$filename)->first();
    if($csdb_model->initiator_id != $request->user()->id) return $this->ret2(400, ["Only Initiator ({$csdb_model->initiator->name}) can commit."]);
    if(!$csdb_model->editable) return $this->ret2(400, ['This DML cannot re commit. You might have issue this DML at previous.']);

    $dom = MpubCSDB::importDocument(storage_path($csdb_model->path),$csdb_model->filename);
    
    // validate BREX dan XSI di sini
    $validateXSI = MpubCSDB::validate('XSI', $dom);
    if(!$validateXSI){
      return $this->ret2(400, MpubCSDB::get_errors(true));
    }

    $dom = MpubCSDB::commit($dom);
    if(!$dom) return $this->ret2(400, MpubCSDB::get_errors(true,'commit'));

    $new_filename = MpubCSDB::resolve_DocIdent($dom);
    if($new_csdb_model = Csdb::where('filename', $new_filename)->first()) return $this->ret2(400, ["This DML cannot be commited due duplication filename of {$new_filename}."]);
    $save = $dom->C14NFile(storage_path($csdb_model->path).DIRECTORY_SEPARATOR.$new_filename);
    if($save){
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
    if(!$filename OR substr($filename,0,3) != 'DML') return $this->ret2(400, ['Only DML is can be issued here.']);
    $csdb_model = Csdb::where('filename',$filename)->first();
    if($csdb_model->initiator_id != $request->user()->id) return $this->ret2(400, ["Only Initiator ({$csdb_model->initiator->name}) can issue."]);
    if(!$csdb_model->editable) return $this->ret2(400, ['This DML cannot re issued. You have to open edit, then re issue.']);
    

    $dom = MpubCSDB::importDocument(storage_path($csdb_model->path),$csdb_model->filename);
    $domxpath = new \DOMXPath($dom);
    $issueInfo = $domxpath->evaluate("//identAndStatusSection/dmlAddress/dmlIdent/issueInfo")[0];
    $issueNumber = (int)$issueInfo->getAttribute('issueNumber');
    $issueNumber = str_pad($issueNumber + 1, 3, '0', STR_PAD_LEFT);
    $issueInfo->setAttribute('issueNumber', $issueNumber);
    $issueInfo->setAttribute('inWork', '00');

    // dd('disini harus validate BREX dan XSI');
    $validateXSI = MpubCSDB::validate('XSI', $dom);
    if(!$validateXSI) return $this->ret2(400, MpubCSDB::get_errors(true));

    $new_filename = MpubCSDB::resolve_DocIdent($dom);
    if($new_csdb_model = Csdb::where('filename', $new_filename)->first()) return $this->ret2(400, ["This DML has been issued with name {$new_filename}."]);
    $save = $dom->C14NFile(storage_path($csdb_model->path).DIRECTORY_SEPARATOR.$new_filename);
    if($save){
      $new_csdb_model = Csdb::create([
        'filename' => $new_filename,
        'path' => $csdb_model->path,
        'editable' => 0,
        'initiator_id' => $csdb_model->initiator_id,
      ]);
      if($new_csdb_model){
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
    $validator = Validator::make($request->all(),[
      'modelIdentCode' => 'required',
      'originator' => 'required',
      'dmlType' => 'required',
      'securityClassification' => 'required',
      'brexDmRef' => ['required', function(string $attribute, mixed $value,  Closure $fail){
        if(count(explode("_",$value)) < 3){
          $fail("The {$attribute} must contain IssueInfo and Language.");
        }
      }],
      'remarks' => 'array',
    ]);

    if($validator->fails()){
      return $this->ret2(400, [$validator->getMessageBag()->getMessages()]);
    }

    $validator->validated();    

    $dml_model = new Dml();
    // $dml_model->setWith(['initiator']);
    $dml_model->direct_save = false;
    $otherOptions = [];
    $dml_model->create_xml($request->get('modelIdentCode'), $request->get('originator') ,$request->get('dmlType'), $request->get('securityClassification'), $request->get('brexDmRef'), $request->get('remarks'), $otherOptions);
    $dml_model->initiator; // supaya ada initiator saat return
    $dml_model->saveModelAndDOM();
    return $this->ret2(200, ["{$dml_model->filename} has been created."], ['dml' => $dml_model]);
  }

  public function addEntry(Request $request)
  {
    $validator = Validator::make($request->all(),[
      'filename' => 'required',
      'issueType' => [function(string $attribute, mixed $value,  Closure $fail){
        if(!in_array($value,[
          "",
          "new",
          "changed",
          "deleted",
          "revised",
          "status",
          "rinstate-changed",
          "rinstate-revised",
          "rinstate-status",
        ])){
          $fail("The {$attribute} is invalid.");
        }
      }],
      'dmlEntryType' => [function(string $attribute, mixed $value,  Closure $fail){
        if(!in_array($value,[
          "",
          "new",
          "changed",
          "deleted",
        ])){
          $fail("The {$attribute} is invalid.");
        }
      }],
      'entryIdent' => 'required',
      'securityClassification' => 'required',
      'enterpriseName' => 'required',
      'enterpriseCode' => [function(string $attribute, mixed $value, Closure $fail){
        if(strlen($value) != 5){
          $fail("The {$attribute} must be contain five digit alphanumeric or letter.");
        }
      }],
      'remarks' => 'array',
    ]);

    if($validator->fails()){
      return $this->ret2(400, [$validator->getMessageBag()->getMessages()]);
    }

    $validator->validated();

    $csdb_model = Dml::where('filename', $request->get('filename'))->first();
    if($csdb_model->initiator->id != $request->user()->id) return $this->ret2(400, ["You cannot add entry unless you are the initiator of the {$request->get('filename')}"]);
    if(!$csdb_model->editable) return $this->ret2(400, ["This DML is not enabled to change. It may be has been issued. You must open edit this DML."]);

    $otherOptions = [];
    $otherOptions['issueType'] = $request->get('issueType');
    $otherOptions['dmlEntryType'] = $request->get('dmlEntryType');
    $add = $csdb_model->add_dmlEntry($request->get('entryIdent'), $request->get('securityClassification'), [$request->get('enterpriseName'), $request->get('enterpriseCode')], $request->get('remarks'), $otherOptions);
    if($add[0]){
      $csdb_model->saveModelAndDOM();
      return $this->ret2(200, ["{$request->get('entryIdent')} has been added to {$request->get('filename')}."]);
    } else {
      return $this->ret2(400, [$add[1],"{$request->get('entryIdent')} is failed to add into {$request->get('filename')}."]);
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
    $model = Csdb::where('filename',$filename)->first();
    $dom = MpubCSDB::importDocument(storage_path($model->path), $model->filename);
    $dmlEntries = MpubCSDB::identifyDmlEntries($dom);
    foreach($dmlEntries as $k => $dmlEntry){
      $objects = Csdb::where('filename', 'like', "%{$dmlEntry['code']}%")->get();
      $dmlEntries[$k]['objects'] = $objects;
    }
    return $dmlEntries;    
  }

  // ############# untuk stage #############
  public function get_csl_forstaging(Request $request)
  {
    $csl_models = Csdb::where('filename', 'like' ,"CSL-%")
        ->where('remarks','like', '%"stage":"unstaged"%')
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
    $dml_model = Dml::where('filename', $filename)->first();
    $dml_dom = MpubCSDB::importDocument(storage_path($dml_model->path), $dml_model->filename);
    $decoder_ident = Helper::decode_dmlIdent($dml_model->filename, false);
    $dml_domxpath = new \DOMXPath($dml_dom);

    $securityClassification = $dml_domxpath->evaluate("string(//dmlStatus/security/@securityClassification)");
    $brexDmRef = MpubCSDB::resolve_dmIdent($dml_domxpath->evaluate("//dmlStatus/brexDmRef")[0]);
    $entries = $request->get('objects') ?? [];

    // #1. validasi terhadap duplikasi code pada $request->get('objects'); dan validasi jika tidak ada dmlEntry
    $en = array_map(fn($entryIdent) => $entryIdent = preg_replace("/_.+/",'',$entryIdent), $entries);
    $en = array_count_values($en);
    foreach($en as $entryIdent => $dupplication){
      if($dupplication > 1) return $this->ret2(400, ['The selected object is only one to choose. The issue info and language on its filename is counted.']);
    }
    if(empty($en)) return $this->ret2(400, ["There must be at least one dml entry."]);

    // #2. validasi object terhadap DMRL.
    $en = [];
    foreach ($entries as $key => $entryIdent) {
      $ident = Helper::decode_ident($entryIdent);
      $codeType = array_keys($ident)[0]; // output eg.: 'dmCode', 'pmCode', etc
      $check_to_currentdml_xpath = Dml::generate_xpath_for_dmlEntry_checking($ident, $codeType);
      $res = $dml_domxpath->evaluate($check_to_currentdml_xpath);
      if($res->length <= 0) return $this->ret2(400, ["Fail to push. Check your DMRL requirement file"]);
      $sc = $dml_domxpath->evaluate("string(security/@securityClassification)",$res[$res->length-1]);
      $rsp_en = $dml_domxpath->evaluate("string(responsiblePartnerCompany/enterpriseName)",$res[$res->length-1]);
      $rsp_ec = $dml_domxpath->evaluate("string(responsiblePartnerCompany/@enterpriseCode)",$res[$res->length-1]);
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
      if($entry_model = Csdb::where('filename', $entryIdent)->first()){
        $entry_model->editable = 1;

        $otherOptions = [
          'dmlEntryType' => $request->get('dmlEntryType') ?? 'new',
        ];
        $add = $csl_model->add_dmlEntry($entryIdent, $option['securityClassification'], [$option['enterpriseName'],$option['enterpriseCode']],$otherOptions);
        if($add[0]){
          $entry_model->save();
        }
      }
    }
    $csl_model->setRemarks('stage', 'unstaged');
    $csl_model->setRemarks('stager_id', $request->user()->id);
    // disini belum perlu validate brex, kecuali jika ingin issued / staged (fase dari stagging to issued, kalo ini kan fase editing to staging);

    // #3. save and return
    if(MpubCSDB::validate('XSI', $csl_model->DOMDocument)){
      $csl_model->editable = 1;
      $csl_model->saveModelAndDOM();
      return $this->ret2(200, ["{$csl_model->filename} has been in staging phase. Check on staging page."],["csl" => $csl_model]);
    } 
    else {
      return $this->ret2(400, MpubCSDB::get_errors(true));
    }
  }

  public function get_cslstaging(Request $request)
  {
    $csl_models = Dml::where('filename','like', 'CSL-%')->where('remarks', 'like','%"stage":"staging"%')->get();
    return $csl_models;
  }

  public function dmlcontentupdate(Request $request, string $filename)
  {
    $dml_model = Dml::where('filename', $filename)->first();
    if($request->user()->id != $dml_model->initiator_id) return $this->ret(400, ["Only Initiator DML can do an update."]);
    $dml_model->DOMDocument = MpubCSDB::importDocument(storage_path($dml_model->path), $dml_model->filename);
    
    $request->validate([
      'entryIdent' => 'required'
    ]);

    $entryIdents = $request->get('entryIdent');
    $dmlEntryTypes = $request->get('dmlEntryType');
    $issueTypes = $request->get('issueType');
    $securityClassifications = $request->get('securityClassification');
    $enterpriseNames = $request->get('enterpriseName');
    $enterpriseCodes = $request->get('enterpriseCode');
    $remarkses = $request->get('remarks') ?? []; // harusnya tidak ada lagi isset disini. perbaiki nanti di frontend dan di sini lakukan validasi

    // #1. remove all dmlEntry
    $dmlContent = $dml_model->DOMDocument->getElementsByTagName("dmlContent")[0];
    while($dmlContent->firstElementChild){
      $dmlContent->firstElementChild->remove();
    }
    $dml_model->DOMDocument->saveXML();
    $dml_model->direct_save = false;
    foreach($entryIdents as $pos => $entryIdent){
      $remarks = isset($remarkses[$pos]) ? [$remarkses[$pos]] : [];
      $otherOptions = [
        'issueType' => $issueTypes[$pos],
        'dmlEntryType' => $dmlEntryTypes[$pos],
      ];
      $add = $dml_model->add_dmlEntry($entryIdent, $securityClassifications[$pos], [$enterpriseNames[$pos], $enterpriseCodes[$pos]], $remarks, $otherOptions);
      if(!$add[0]) return $this->ret2(400, [$add[1]]);
    }
    $dml_model->DOMDocument->saveXML();
    $dml_model->saveModelAndDOM();
    return $this->ret2(200, ['Update Success. Please reload the page.']);
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
    $csl_model->setRemarks('stage','staging');
    $csl_model->setRemarks('stager_id',$request->user()->id);
    
    // #3. validasi XSI dan BREX
    $validateXSI = MpubCSDB::validate('XSI', $csl_model->DOMDocument);
    $validateBREX = true; // validate BREX disini nanti
    if(!($validateXSI && $validateBREX)) return $this->ret2(400, MpubCSDB::get_errors());

    // #4. validasi duplikasi new filename
    if(Csdb::where('filename', $new_filename)->first()) return $this->ret2(400, ["When {$filename} changed into {$new_filename}, it failed due to dupplication object."]);
    
    // #5. save and return 200
    $csl_model->saveModelAndDOM();
    return $this->ret2(200, ["Push to stage is success. {$filename} is issued by changing filename into {$new_filename}."]);
  }

  public function decline_csl_forstaging(Request $request, string $filename)
  {
    $csl_model = Dml::where('filename', $filename)->first();
    $csl_model->setRemarks('stage','unstaged');
    $csl_model->setRemarks('stager_id',$request->user()->id);
    return $this->ret2(200, ["{$filename} is remarked as unstaged. You may referesh page."]);
  }

  public function deletedml(Request $request, string $filename)
  {
    $csl_model = Dml::where('filename', $filename)->first();
    $csl_model->setRemarks('stage','deleted');
    $csl_model->setRemarks('stager_id',$request->user()->id);
    return $this->ret2(200, ["{$filename} is remarked as deleted."]);
  }

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
    $dmlEntries = array_map(function($dmlEntry) use(&$nothingEntry){
      $dmlEntry['model'] = Csdb::where('filename', $dmlEntry['code'].$dmlEntry['extension'])
                              ->where('remarks','not like', '%"stage":"staged"%')
                              ->where('remarks','not like', '%"stage":"deleted"%')
                              ->first();
      if(!$dmlEntry['model']) ($nothingEntry[] = $dmlEntry['code'].$dmlEntry['extension']);
      return $dmlEntry;
    }, $dmlEntries);
    if(!empty($nothingEntry)) return $this->ret2(400, ["CSL entry must not be 'staged' or 'deleted'. Ref: ". join(", ", $nothingEntry). "."]);

    
    // #2. Set remakrs staged
    $csl_model->editable = 0;
    $csl_model->setRemarks('stage','staged');
    $csl_model->setRemarks('stager_id',$request->user()->id);
    $csl_model->save();
    foreach ($dmlEntries as $position => $dmlEntry) {
      $dmlEntry['model']->direct_save = false;
      $dmlEntry['model']->editable = 0;
      $dmlEntry['model']->setRemarks('stage','staged');
      $dmlEntry['model']->setRemarks('stager_id',$request->user()->id);
      $dmlEntry['model']->save();
    }

    return $this->ret2(200, ["{$filename} is successfully staged."]);
  }
}
