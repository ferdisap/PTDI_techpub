<?php

namespace App\Http\Requests\Csdb;

use App\Models\Csdb;
use App\Rules\Csdb\SecurityClassification;
use App\Rules\Dml\EntryIdent;
use App\Rules\Dml\EntryIssueType;
use App\Rules\Dml\EntryType;
use App\Rules\EnterpriseCode;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Ptdi\Mpub\Main\CSDBStatic;

class DmlUpdateFromEditorDML extends FormRequest
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
      // dml ident
      'ident-securityClassification' => ['required', new SecurityClassification(true)],
      'ident-brexDmRef' => ['required', function (string $attribute, mixed $value, Closure $fail) {
        if (empty(CSDBStatic::decode_dmIdent($value))) {
          $fail("The {$attribute} is wrong rule.");
        }
      }],
      'ident-remarks' => '',
      // dml entries
      // 'entries' => [function (string $attribute, mixed $value, Closure $fail) {
      // }],
      'entryIdent' => [fn (string $attribute, mixed $value, Closure $fail) => count($value) !== count(array_unique($value)) ? $fail("Entry Ident must be unique.") : true],
      'entryIdent.*' => ['required', new EntryIdent(null)],
      'dmlEntryType.*' => [new EntryType],
      'issueType.*' => [new EntryIssueType],
      'securityClassification.*' => [new SecurityClassification(false)],
      'enterpriseCode.*' => [new EnterpriseCode(false)],
      'enterpriseName.*' => ['required'],
      'remarks' => 'array',
      'answer' => 'array',
      'answerToEntry.*' => [fn (string $attribute, mixed $value, Closure $fail) => (empty($value) || $value === 'y' || $value === 'n') ? true : $fail("answerToEntry value must be in 'y' or 'n'")],
    ];
  }

  public function prepareForValidation()
  {
    $ident = $this->get('ident'); // array
    $this->request->remove('ident');

    $entries = $this->get('entries');
    $this->request->remove('entries');
    $l = 0;
    $entries['no'] = [];
    $entries['entryIdent'] = [];
    $entries['dmlEntryType'] = [];
    $entries['issueType'] = [];
    $entries['securityClassification'] = [];
    $entries['enterpriseName'] = [];
    $entries['enterpriseCode'] = [];
    $entries['remarks'] = [];
    $entries['answer'] = [];
    $entries['answerToEntry'] = [];
    while (isset($entries[$l])) {
      array_push($entries['no'], $entries[$l]['no']);
      array_push($entries['entryIdent'], $entries[$l]['entryIdent']);
      array_push($entries['dmlEntryType'], $entries[$l]['dmlEntryType']);
      array_push($entries['issueType'], $entries[$l]['issueType']);
      array_push($entries['securityClassification'], $entries[$l]['securityClassification']);
      array_push($entries['enterpriseName'], $entries[$l]['enterpriseName']);
      array_push($entries['enterpriseCode'], $entries[$l]['enterpriseCode']);
      array_push($entries['remarks'], array_filter($entries[$l]['remarks'], fn($v) => $v)); // kan remarks itu array, jadi di filter supaya tidak ada yang null agar nantinya tidak dibuat element <simplePara> yang kosong
      array_push($entries['answer'], array_filter($entries[$l]['answer'], fn($v) => $v)); // kan remarks itu array, jadi di filter supaya tidak ada yang null agar nantinya tidak dibuat element <simplePara> yang kosong
      array_push($entries['answerToEntry'], $entries[$l]['answerToEntry']);
      unset($entries[$l]);
      $l++;
    }
    $this->replace(array_merge($ident,$entries));
  }

  protected function passedValidation()
  {
    $this->DMLModel = Csdb::getObject($this->route('filename'))->first();
  }
}
