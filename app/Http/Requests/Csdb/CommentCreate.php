<?php

namespace App\Http\Requests\Csdb;

use App\Rules\Csdb\BrexDmRef;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class CommentCreate extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return false;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    // yang tidak di validasi
    // $unvalidatedData = [
    //   'responseType', 'seqNumberRef', 'commentType', 'commentTitle', 'division', 'enterpriseUnit', 'firstName', 'middleName', 'jobTitle',
    //   'department', 'street', 'postOfficeBox', 'postalZipCode', 'state', 'province', 'building', 'room', 'SITA'
    // ];

    return [
      'com_modelIdentCode' => 'required',
      'com_senderIdent' => 'required|max_digits:5',
      'com_languageIsoCode' => 'required',
      'com_countryIsoCode' => 'required',

      // status
      'com_securityClassification' => 'required',
      'com_commentPriorityCode' => 'required',
      'com_commentRefs' => ['array', function(string $attribute, mixed $commentRefsArray, Closure $fail){
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
      'com_brexDmRef' => ['required', new BrexDmRef],
      'com_remarks' => 'array',

      // enterprise
      'com_enterpriseName' => 'required',

      // dispatchPerson
      'com_lastName' => ['required', function(string $attribute, mixed $lastName, Closure $fail){
        if($lastName !== $this->user()->last_name) $fail("The last name of dispatch person is wrong.");
      }],

      // address
      'com_city' => 'required',
      'com_country' => 'required',
      'com_phoneNumber' => 'array',
      'com_faxNumber' => 'array',
      'com_email' => 'array',
      'com_internet' => 'array',

      // content
      'com_commentContentSimplePara' => 'required'
    ];
  }
}
