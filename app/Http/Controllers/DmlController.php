<?php

namespace App\Http\Controllers;

use App\Models\Csdb;
use App\Models\Dml;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
   * @return App\Models\Csdb
   */
  public function create(Request $request)
  {
    $validator = Validator::make($request->all(),[
      'modelIdentCode' => 'required',
      'originator' => 'required',
      'dmlType' => 'required',
      'securityClassification' => 'required',
      'brexDmRef' => 'required',
      'remarks' => 'array',
    ]);

    if($validator->fails()){
      return $this->ret(400, [$validator->getMessageBag()]);
    }

    $validator->validated();    

    $dml_model = new Dml();
    $csdb = $dml_model->create_xml($request->get('modelIdentCode'), $request->get('originator') ,$request->get('dmlType'), $request->get('securityClassification'), $request->get('brexDmRef'), $request->get('remarks'), 'xml');
    return $csdb;
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

    $add = $csdb_model->add_dmlEntry($request->get('issueType'), $request->get('entryIdent'), $request->get('securityClassification'), [$request->get('enterpriseName'), $request->get('enterpriseCode')], $request->get('remarks'));
    if($add[0]){
      return $this->ret(200, ["{$request->get('entryIdent')} has been added to {$request->get('filename')}."]);
    } else {
      return $this->ret(400, [$add[1],"{$request->get('entryIdent')} is failed to add into {$request->get('filename')}."]);
    }
    return '';     
  }
}
