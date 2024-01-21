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
    $dmls = Csdb::where('filename', 'like' ,"DML-%")->get();
    return $dmls;
  }

  /**
   * akan membuat file baru issueNumber++, inWork '01'
   * fungsi ini dipakai ketika dml sudah di issue(), namun ada content yang harus di edit
   */
  public function edit(Request $request)
  {
    $filename = $request->route('filename');
    if(!$filename OR substr($filename,0,3) != 'DML') return $this->ret(400, ['Only DML is can be editted here.']);
    $csdb_model = Csdb::where('filename',$filename)->first();
    if($csdb_model->initiator_id != $request->user()->id) return $this->ret(400, ["Only Initiator ({$csdb_model->initiator->name}) can edit."]);

    $dom = MpubCSDB::importDocument(storage_path($csdb_model->path),$csdb_model->filename);
    $domxpath = new \DOMXPath($dom);
    $issueInfo = $domxpath->evaluate("//identAndStatusSection/dmlAddress/dmlIdent/issueInfo")[0];
    // $issueNumber = (int)$issueInfo->getAttribute('issueNumber');
    // $issueNumber++;
    // $issueNumber = str_pad($issueNumber, 3, '0', STR_PAD_LEFT);
    // $issueInfo->setAttribute('issueNumber', $issueNumber);
    $issueInfo->setAttribute('inWork', '01');

    $new_filename = MpubCSDB::resolve_DocIdent($dom);
    if($new_csdb_model = Csdb::where('filename', $new_filename)->first()) return $this->ret(400, ["This DML cannot be editted due duplication filename of {$new_filename}."]);
    $save = $dom->C14NFile(storage_path($csdb_model->path).DIRECTORY_SEPARATOR.$new_filename);
    if($save){
      $new_csdb_model = Csdb::create([
        'filename' => $new_filename,
        'path' => $csdb_model->path,
        'editable' => 1,
        'initiator_id' => $csdb_model->initiator_id,
      ]);
      if($new_csdb_model){
        return $this->ret(200, ["New {$new_csdb_model->filename} has been created."]);
      }
    }
    return $this->ret(400, ["{$filename} failed to open edit."]);
  }

  /**
   * akan membuat file baru issuNumber tetap, inWork++
   */
  public function commit(Request $request)
  {
    $filename = $request->route('filename');
    if(!$filename OR substr($filename,0,3) != 'DML') return $this->ret(400, ['Only DML is can be commited here.']);
    $csdb_model = Csdb::where('filename',$filename)->first();
    if($csdb_model->initiator_id != $request->user()->id) return $this->ret(400, ["Only Initiator ({$csdb_model->initiator->name}) can commit."]);
    if(!$csdb_model->editable) return $this->ret(400, ['This DML cannot re commit. You might have issue this DML at previous.']);

    $dom = MpubCSDB::importDocument(storage_path($csdb_model->path),$csdb_model->filename);
    $domxpath = new \DOMXPath($dom);
    $issueInfo = $domxpath->evaluate("//identAndStatusSection/dmlAddress/dmlIdent/issueInfo")[0];
    $inWork = (int)$issueInfo->getAttribute('inWork');
    if($inWork > 0) $this->ret(400, ["{$filename} cannot be commited due to the current inWork is 00"]); 
    if($inWork == 99) ($inWork = 'AA');
    else ($inWork++);
    $inWork = str_pad($inWork, 2, '0', STR_PAD_LEFT);
    $issueInfo->setAttribute('inWork', $inWork);

    $new_filename = MpubCSDB::resolve_DocIdent($dom);
    if($new_csdb_model = Csdb::where('filename', $new_filename)->first()) return $this->ret(400, ["This DML cannot be commited due duplication filename of {$new_filename}."]);
    $save = $dom->C14NFile(storage_path($csdb_model->path).DIRECTORY_SEPARATOR.$new_filename);
    if($save){
      $new_csdb_model = Csdb::create([
        'filename' => $new_filename,
        'path' => $csdb_model->path,
        'editable' => 1,
        'initiator_id' => $csdb_model->initiator_id,
      ]);
      if($new_csdb_model){
        return $this->ret(200, ["New {$new_csdb_model->filename} has been created."]);
      }
    }
    return $this->ret(400, ["{$filename} failed to commit."]);
  }

  /**
   * akan membuat file baru dengan issueNumber++ dan inWork '00'
   */
  public function issue(Request $request)
  {
    $filename = $request->route('filename');
    if(!$filename OR substr($filename,0,3) != 'DML') return $this->ret(400, ['Only DML is can be issued here.']);
    $csdb_model = Csdb::where('filename',$filename)->first();
    if($csdb_model->initiator_id != $request->user()->id) return $this->ret(400, ["Only Initiator ({$csdb_model->initiator->name}) can issue."]);
    if(!$csdb_model->editable) return $this->ret(400, ['This DML cannot re issued. You have to open edit, then re issue.']);

    $dom = MpubCSDB::importDocument(storage_path($csdb_model->path),$csdb_model->filename);
    $domxpath = new \DOMXPath($dom);
    $issueInfo = $domxpath->evaluate("//identAndStatusSection/dmlAddress/dmlIdent/issueInfo")[0];
    $issueNumber = (int)$issueInfo->getAttribute('issueNumber');
    $issueNumber = str_pad($issueNumber + 1, 3, '0', STR_PAD_LEFT);
    $issueInfo->setAttribute('issueNumber', $issueNumber);
    $issueInfo->setAttribute('inWork', '00');

    dd('disini harus validate BREX dan XSI');

    $new_filename = MpubCSDB::resolve_DocIdent($dom);
    if($new_csdb_model = Csdb::where('filename', $new_filename)->first()) return $this->ret(400, ["This DML has been issued with name {$new_filename}."]);
    $save = $dom->C14NFile(storage_path($csdb_model->path).DIRECTORY_SEPARATOR.$new_filename);
    if($save){
      $new_csdb_model = Csdb::create([
        'filename' => $new_filename,
        'path' => $csdb_model->path,
        'editable' => 0,
        'initiator_id' => $csdb_model->initiator_id,
      ]);
      if($new_csdb_model){
        return $this->ret(200, ["New {$new_csdb_model->filename} has been created with doesnt have capability to edit."]);
      }
    }
    return $this->ret(400, ["{$filename} failed to issue."]);
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
      return $this->ret(400, [$validator->getMessageBag()]);
    }

    $validator->validated();    

    $dml_model = new Dml();
    $otherOptions = [];
    $csdb = $dml_model->create_xml($request->get('modelIdentCode'), $request->get('originator') ,$request->get('dmlType'), $request->get('securityClassification'), $request->get('brexDmRef'), $request->get('remarks'), $otherOptions);
    return $this->ret(200, ["{$dml_model->filename} has been created."], ['dml' => $csdb]);
  }

  public function addEntry(Request $request)
  {
    $validator = Validator::make($request->all(),[
      'filename' => 'required',
      'issueType' => ['required',function(string $attribute, mixed $value,  Closure $fail){
        if(!in_array($value,[
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
      return $this->ret(400, [$validator->getMessageBag()]);
    }

    $validator->validated();

    $csdb_model = Dml::where('filename', $request->get('filename'))->first();
    if($csdb_model->initiator->id != $request->user()->id) return $this->ret(400, ["You cannot add entry unless you are the initiator of the {$request->get('filename')}"]);
    if(!$csdb_model->editable) return $this->ret(400, ["This DML is not enabled to change. It may be has been issued. You must open edit this DML."]);

    $add = $csdb_model->add_dmlEntry($request->get('issueType'), $request->get('entryIdent'), $request->get('securityClassification'), [$request->get('enterpriseName'), $request->get('enterpriseCode')], $request->get('remarks'));
    if($add[0]){
      return $this->ret(200, ["{$request->get('entryIdent')} has been added to {$request->get('filename')}."]);
    } else {
      return $this->ret(400, [$add[1],"{$request->get('entryIdent')} is failed to add into {$request->get('filename')}."]);
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
  /**
   * sementara belum di sediakan fitur $remakrks per dmlEntry CSL. 
   */
  public function push(Request $request, string $dmlFilename)
  {
    $dml_model = Dml::where('filename', $dmlFilename)->first();
    $dml_dom = MpubCSDB::importDocument(storage_path($dml_model->path), $dml_model->filename);
    $decoder_ident = Helper::decode_dmlIdent($dml_model->filename, false);
    $dml_domxpath = new \DOMXPath($dml_dom);

    $securityClassification = $dml_domxpath->evaluate("string(//dmlStatus/security/@securityClassification)");
    $brexDmRef = MpubCSDB::resolve_dmIdent($dml_domxpath->evaluate("//dmlStatus/brexDmRef")[0]);
    $entries = $request->get('objects') ?? [];

    // #1. validasi terhadap duplikasi code pada $request->get('objects');
    $en = array_map(fn($entryIdent) => $entryIdent = preg_replace("/_.+/",'',$entryIdent), $entries);
    $en = array_count_values($en);
    foreach($en as $entryIdent => $dupplication){
      if($dupplication > 1) return $this->ret(400, ['The selected object is only one to choose. The issue info and language on its filename is counted.']);
    }

    // #2. validasi object terhadap DMRL.
    $en = [];
    foreach ($entries as $key => $entryIdent) {
      $ident = Helper::decode_ident($entryIdent);
      $codeType = array_keys($ident)[0]; // output eg.: 'dmCode', 'pmCode', etc
      $check_to_currentdml_xpath = Dml::generate_xpath_for_dmlEntry_checking($ident, $codeType);
      $res = $dml_domxpath->evaluate($check_to_currentdml_xpath);
      if($res->length <= 0) return $this->ret(400, ["Fail to push. Check your DMRL requirement file"]);
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
    array_unshift($remarks, "This CSL is required to comply the {$dmlFilename}");
    $csl_model = new Dml();
    $csl_model->direct_save = false;
    $csl_model->create_xml($decoder_ident['dmlCode']['modelIdentCode'], $decoder_ident['dmlCode']['senderIdent'], 'S', $securityClassification, $brexDmRef, $remarks, []);
    foreach ($en as $entryIdent => $option) {
      $csl_model->add_dmlEntry('new', $entryIdent, $option['securityClassification'], [$option['enterpriseName'],$option['enterpriseCode']],[]);
    }
    $csl_model->setRemarks('stage', 'staging');
    $csl_model->setRemarks('stager_id', $request->user()->id);
    // disini belum perlu validate brex, kecuali jika ingin issued / staged (fase dari stagging to issued, kalo ini kan fase editing to staging);

    // #3. save and return
    if(MpubCSDB::validate('XSI', $csl_model->DOMDocument)){
      $csl_model->saveModelAndDOM();
      return $this->ret(200, ["{$csl_model->filename} has been in staging phase. Check on staging page."]);
    } 
    else {
      return $this->ret(400, MpubCSDB::get_errors(true));
    }

  }

  public function get_cslstaging(Request $request)
  {
    $csl_models = Dml::where('filename','like', 'CSL-%')->where('remarks', 'like','%"stage":"staging"%')->get();
    return $csl_models;
  }
}
