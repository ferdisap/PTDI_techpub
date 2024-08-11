<?php

namespace App\Http\Requests\Csdb;

use App\Models\Csdb;
use App\Models\Csdb\Comment;
use App\Rules\Csdb\BrexDmRef;
use App\Rules\Csdb\CommentRefs;
use App\Rules\Csdb\CommentType;
use App\Rules\Csdb\Language;
use App\Rules\Csdb\Path;
use App\Rules\Csdb\S1000DConfigurableAttributeValue;
use App\Rules\Csdb\SecurityClassification;
use App\Rules\Csdb\SeqNumber;
use App\Rules\EnterpriseCode;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Ptdi\Mpub\Main\CSDBStatic;
use Illuminate\Support\Str;

/**
 * Dalam pembuatan comment, maximum comment dengan commentType = 'i' hanya 99x karena 3digit pertama adalah parentComment ('q') dan 2 digit terakhir adalah sequential
 */
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
    return [
      'path' => [new Path],
      // ident
      'modelIdentCode' => 'required',
      'senderIdent' => [new EnterpriseCode(true)],
      'seqNumber' => [new SeqNumber],
      'commentType' => [new CommentType],
      'yearOfDataIssue' => '',
      'languageIsoCode' => [new Language],
      'countryIsoCode' => [new Language],

      // address
      'commentTitle' => 'max:50',
      'enterpriseName' => '',
      'division' => '',
      'enterpriseUnit' => '',
      'lastName' => '',
      'firstName' => '',
      'jobTitle' => '',
      'department' => '',
      'street' => '',
      'postOfficeBox' => '',
      'postalZipCode' => '',
      'city' => '',
      'country' => '',
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
      'securityClassification' => [new SecurityClassification],
      'commentPriorityCode' => [new S1000DConfigurableAttributeValue('cp')],
      'responseType' => [new S1000DConfigurableAttributeValue('rt')],
      'brexDmRef' => [new BrexDmRef],
      'commentRefs' => [new CommentRefs],
      'commentRemarks' => ['array'],

      // content
      'commentContentSimplePara' => ['array'],      
    ];
  }

  /**
   * Prepare the data for validation.
   */
  protected function prepareForValidation(): void
  {
    $commentCreator = $this->user();
    $creatorEnterpriseModel = $commentCreator->work_enterprise;
    $senderIdent = $creatorEnterpriseModel->code->name;

    // $brexModel = Csdb::getObject($this->get('brexDmRef'),['exception' => ['CSDB-DELL', 'CSDB-PDEL']])->first();
    // $modelIdentCode = $brexModel ? $brexModel->modelIdentCode : null;

    if (str_contains($this->commentRefs, 'noReferences')) $commentRefs = ['noReferences'];
    else {
      $commentRefs = explode(',', $this->commentRefs);
      array_walk($commentRefs, (fn (&$v) => $v = trim($v)));
      $commentRefs = array_unique($commentRefs);

      $objectReference = $commentRefs[0];
      $objectReferenceDecoded = CSDBStatic::decode_ident($objectReference);
      $first_key = array_key_first($objectReferenceDecoded); //commentCOde, dmCode, pmCode, etc
      $modelIdentCode = $objectReferenceDecoded[$first_key]['modelIdentCode'];
      $languageIsoCode = $objectReferenceDecoded['language']['languageIsoCode'] ?? $this->get('languageIsoCode');
      $countryIsoCode = $objectReferenceDecoded['language']['countryIsoCode'] ?? $this->get('countryIsoCode');
    }

    $parentCommentFilename = $this->get('parentCommentFilename');
    if ($parentCommentFilename) {
      $previousCommentFilename = $this->get('previousCommentFilename');
      if ($previousCommentFilename) {
        // jika ada previous comment, maka tinggal tambahkan increment last two digit seqNumber
        $previousCommentDecoded = CSDBStatic::decode_commentIdent($previousCommentFilename);
        $threeDigitFirst_seqNumber = substr($previousCommentDecoded['commentCode']['seqNumber'], 0, 3);
        $twoDigitLast_seqNumber = substr($previousCommentDecoded['commentCode']['seqNumber'], 3);
        $twoDigitLast_seqNumber++;
        $twoDigitLast_seqNumber = str_pad($twoDigitLast_seqNumber, 2, '0', STR_PAD_LEFT);
        $modelIdentCode = $modelIdentCode ?? $previousCommentDecoded['commentCode']['modelIdentCode'];
      } else {
        // else pakai parentComment untuk last two digit seqNumber
        $parentCommentDecoded = CSDBStatic::decode_commentIdent($parentCommentFilename);
        $threeDigitFirst_seqNumber = substr($parentCommentDecoded['commentCode']['seqNumber'], 0, 3);
        $twoDigitLast_seqNumber = substr($parentCommentDecoded['commentCode']['seqNumber'], 3);
        $modelIdentCode = $modelIdentCode ?? $parentCommentDecoded['commentCode']['modelIdentCode'];
      }
      $seqNumber = $threeDigitFirst_seqNumber . $twoDigitLast_seqNumber;
      $commentType = $this->get('commentType') ?? 'i';
    } else {
      $seqNumber = rand(1,999) . '00';
      $seqNumber = str_pad($seqNumber, 5, '0', STR_PAD_LEFT);
      $commentType = $this->get('commentType') ?? 'q';
    }

    $this->merge([
      'path' => $this->get('path') ?? 'CSDB',
      // ident
      'modelIdentCode' => $modelIdentCode,
      'senderIdent' => $senderIdent,
      'seqNumber' => $seqNumber,
      'commentType' => $commentType,
      'yearOfDataIssue' => date("Y"),
      'languageIsoCode' => $languageIsoCode,
      'countryIsoCode' => $countryIsoCode,

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
      'brexDmRef' => $this->get('brexDmRef'),
      'commentContentSimplePara' => preg_split("/<br\/>|<br>|&#10;/m", $this->remarks),
      'commentRefs' => $commentRefs,
      'remarks' => $this->get('commentRemarks'),

      // content
      'commentContentSimplePara' => preg_split("/<br\/>|<br>|&#10;/m",$this->get('commentContentSimplePara')),
    ]);
  }

  protected function passedValidation()
  {
    $COMModel = new Comment();
    $COMModel->create_xml($this->user()->storage, $this->validated());

    $this->merge([
      // harus array atau scalar, entah kenapa
      // Expected a scalar, or an array as a 2nd argument to \"Symfony\\Component\\HttpFoundation\\InputBag::set()\", \"Ptdi\\Mpub\\Main\\CSDBObject\" given.
      'CSDBObject' => [$COMModel->CSDBObject], 
    ]);
  }
}