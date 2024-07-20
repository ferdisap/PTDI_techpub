<?php

namespace App\Http\Requests\Csdb;

use App\Models\Csdb;
use App\Rules\Csdb\BrexDmRef;
use App\Rules\Csdb\SecurityClassification;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CommentCreate extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
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
      'seqNumberRef' => '',

      // ident
      'modelIdentCode' => 'required',
      'senderIdent' => 'required',
      'commentType' => '',
      'languageIsoCode' => 'required',
      'countryIsoCode' => 'required',

      // address
      'commentTitle' => '',
      'enterpriseName' => '',
      'division' => '',
      'enterpriseUnit' => '',
      'lastName' => ['required', function(string $attribute, mixed $lastName, Closure $fail){
        if($lastName !== Auth::user()->last_name) $fail("The last name of dispatch person is not suitable.");
      }],
      'firstName' => '',
      'jobTitle' => '',
      'department' => '',
      'street' => '',
      'postOfficeBox' => '',
      'postalZipCode' => '',
      'city' => 'required',
      'country' => 'required',
      'state' => '',
      'province' => '',
      'building' => '',
      'room' => '',
      'phoneNumber' => '',
      'faxNumber' => '',
      'email' => '',
      'internet' => '',
      'SITA' => '',

      // status
      'securityClassification' => ['required', new SecurityClassification],
      'commentPriorityCode' => 'required',
      'responseType' => '',
      'commentRefs' => ['array', function(string $attribute, mixed $commentRefsArray, Closure $fail){
        if(!empty($commentRefsArray)){
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
        }
      }],
      'brexDmRef' => ['required', new BrexDmRef],
      'remarks' => 'array',

      // content
      'commentContentSimplePara' => 'required'
    ];
  }

  protected function prepareForValidation(): void
  {
    $commentCreator = $this->user();
    $creatorEnterpriseModel = $commentCreator->work_enterprise;
    $senderIdent = $creatorEnterpriseModel->code;
    // dd($senderIdent);

    $brexModel = Csdb::where('filename', $this->get('brexDmRef'))->first();
    $modelIdentCode = $brexModel->meta->modelIdentCode;    

    $remarks = $this->get('remarks');
    if(!is_array($remarks) AND is_string($remarks)){
      $rand = rand(0,99999);
      $remarks = preg_replace("/[\r\n]+/", $remarks, $rand);
      $remarks = explode($rand,$remarks);
    }

    $this->merge([
      'seqNumberRef' => $this->get('seqNumberRef'),

      // ident
      'modelIdentCode' => $modelIdentCode,
      'senderIdent' => $senderIdent,
      'commentType' => $this->get('commentType'),
      // 'languageIsoCode' => $this->get('languageIsoCode'),
      // 'countryIsoCode' => $this->get('countryIsoCode'),
      'languageIsoCode' => $brexModel->meta->languageIsoCode,
      'countryIsoCode' => $brexModel->meta->countryIsoCode,

      // address
      'commentTitle' => $this->get('commentTitle'),
      'enterpriseName' => $creatorEnterpriseModel->name,
      'division' => $creatorEnterpriseModel->remarks['division'] ?? '',
      'enterpriseUnit' => $creatorEnterpriseModel->remarks['enterpriseUnit'] ?? '',
      'lastName' => $commentCreator->last_name,
      'firstName' => $commentCreator->first_name,
      'jobTitle' => $commentCreator->jobTitle,
      'department' => $creatorEnterpriseModel->address['department'] ?? '',
      'street' => $creatorEnterpriseModel->address['street'] ?? '',
      'postOfficeBox' => $creatorEnterpriseModel->address['postOfficeBox'] ?? '',
      'postalZipCode' => $creatorEnterpriseModel->address['postalZipCode'] ?? '',
      'city' => $creatorEnterpriseModel->address['city'] ?? '',
      'country' => $creatorEnterpriseModel->address['country'] ?? '',
      'state' => $creatorEnterpriseModel->address['state'] ?? '',
      'province' => $creatorEnterpriseModel->address['province'] ?? '',
      'building' => $creatorEnterpriseModel->address['building'] ?? '',
      'room' => $creatorEnterpriseModel->address['room'] ?? '',
      'phoneNumber' => $creatorEnterpriseModel->address['phoneNumber'] ?? [],
      'faxNumber' => $creatorEnterpriseModel->address['faxNumber'] ?? [],
      'email' => $creatorEnterpriseModel->address['email'] ?? [],
      'internet' => $creatorEnterpriseModel->address['internet'] ?? [],
      'SITA' => $creatorEnterpriseModel->address['SITA'] ?? '',

      // status
      'securityClassification' => $this->get('securityClassification'),
      'commentPriorityCode' => $this->get('commentPriorityCode'),
      'responseType' => $this->get('responseType'),
      'commentRefs' => array_filter($this->get('commentRefs'), fn($v) => $v != null && $v != ''),
      'brexDmRef' => $this->get('brexDmRef'),
      'remarks' => $remarks,

      // content
      'commentContentSimplePara' => explode("\r\n",$this->get('commentContentSimplePara')),
    ]);
  }
}
