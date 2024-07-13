<?php

namespace App\Http\Controllers;

use App\Models\Csdb\Ddn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Rules\Csdb\BrexDmRef as BrexDmRefRules;
use Closure;
use Illuminate\Support\Facades\Auth;

class DdnController extends Controller
{
  public function create(Request $request)
  {
    dd('aa');
    $validator = Validator::make($request->all(), [
      // ident
      'modelIdentCode' => 'required',
      'senderIdent' => 'required|max_digits:5',
      'receiverIdent' => 'required|max_digits:5',

      // status
      'securityClassification' => 'required',
      'brexDmRef' => ['required', new BrexDmRefRules],
      'remarks' => 'array',

      // enterprise
      'enterpriseName' => 'required',

      // dispatchPerson
      'lastName' => ['required', function(string $attribute, mixed $lastName, Closure $fail){
        if($lastName !== Auth::user()->last_name) $fail("The last name of dispatch person is wrong.");
      }],

      // address
      'city' => 'required',
      'country' => 'required',
      'phoneNumber' => 'array',
      'faxNumber' => 'array',
      'email' => 'array',
      'internet' => 'array',

      // content
      'commentContentSimplePara' => 'required'
    ]);

    if($validator->fails()){
      return $this->ret2(400, [$validator->getMessageBag()->getMessages()]);
    }

    // supaya yang tervalidasi bisa digabung.
    $validatedData = array_merge($request->all()->toArray(),$validator->validated());
    $validatedData['commentContentSimplePara'] = preg_split("/\r\n|\n|\r/", $validatedData['commentContentSimplePara']);
    foreach ($validatedData as $k => $v){
      if(is_array($v)) $validatedData[$k] = array_filter($v, fn($vv) => !is_null($vv) && $vv !== '');
    };
    $DDNModel = new Ddn();
    $DDNModel->direct_save = false;
    $isCreated = $DDNModel->create_xml($validatedData);
    if(!($isCreated)) return $this->ret2(400, ["fails to create DDN."]);    
    $DDNModel->initiator; // supaya ada initiator saat return
    
    if($DDNModel->saveModelAndDOM()){
      return $this->ret2(200, ["{$DDNModel->filename} has been created."], ['model' => $DDNModel]);
    } else {
      return $this->ret2(400, ["fail to create and save DDN."]);
    }
  }
}
