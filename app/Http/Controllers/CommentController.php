<?php

namespace App\Http\Controllers;

use App\Models\Csdb as ModelsCsdb;
use App\Models\Csdb\Comment;
use App\Models\Project;
use App\Models\User;
use App\Rules\Csdb\BrexDmRef as BrexDmRefRules;
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
use PrettyXml\Formatter;
use Ptdi\Mpub\Helper;

class CommentController extends Controller
{
  /**
   * selanjutnya buat attachment.
   * COM attachment diujung filename ditambah -attachmentNmber.extension see pdf page 1906/3503
   */
  public function create(Request $request)
  {
    $validator = Validator::make($request->all(), [
      // ident
      'modelIdentCode' => 'required',
      'senderIdent' => 'required|max_digits:5',
      'languageIsoCode' => 'required',
      'countryIsoCode' => 'required',

      // status
      'securityClassification' => 'required',
      'commentPriorityCode' => 'required',
      'commentRefs' => ['array', function(string $attribute, mixed $commentRefsArray, Closure $fail){
        $type = substr($commentRefsArray[0], 0, 3);
        $loop = 1;
        while(isset($commentRefsArray[$loop]))
        {
          if(substr($commentRefsArray[$loop], 0,3) != $type){
            $fail("{$commentRefsArray[$loop]} must be a type of {$type}");
            break;
          }
          $loop++;
        }
      }],
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

    // yang tidak di validasi
    $unvalidatedData = [
      'responseType', 'seqNumberRef', 'commentType', 'commentTitle', 'division', 'enterpriseUnit', 'firstName', 'middleName', 'jobTitle',
      'department', 'street', 'postOfficeBox', 'postalZipCode', 'state', 'province', 'building', 'room', 'SITA'
    ];

    $validatedData = $validator->validated();
    foreach($unvalidatedData as $v){
      $validatedData[$v] = $request->get($v);
    }
    $validatedData['commentContentSimplePara'] = preg_split("/\r\n|\n|\r/", $validatedData['commentContentSimplePara']);
    foreach ($validatedData as $k => $v){
      if(is_array($v)) $validatedData[$k] = array_filter($v, fn($vv) => !is_null($vv) && $vv !== '');
    }
    $COMModel = new Comment();
    $COMModel->direct_save = false;
    $isCreated = $COMModel->create_xml($validatedData);
    if(!($isCreated)) return $this->ret2(400, ["fails to create COM."]);    
    $COMModel->initiator; // supaya ada initiator saat return
    
    if($COMModel->saveModelAndDOM()){
      return $this->ret2(200, ["{$COMModel->filename} has been created."], ['model' => $COMModel]);
    } else {
      return $this->ret2(400, ["fail to create and save COM."]);
    }

  }
}