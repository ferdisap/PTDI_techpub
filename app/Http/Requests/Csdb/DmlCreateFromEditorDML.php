<?php

namespace App\Http\Requests\Csdb;

use App\Models\Csdb;
use App\Models\Csdb\Dml;
use App\Rules\Csdb\BrexDmRef;
use App\Rules\Csdb\Path;
use Illuminate\Foundation\Http\FormRequest;
use Ptdi\Mpub\Main\CSDBObject;

class DmlCreateFromEditorDML extends FormRequest
{
  public $fail = [];
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
      'modelIdentCode' => 'required',
      'originator' => 'required',
      'dmlType' => 'required',
      'securityClassification' => 'required',
      'brexDmRef' => ['required', new BrexDmRef],
      'remarks' => 'array',

      'path' => [new Path],
    ];
  }

  /**
   * Prepare the data for validation.
   */
  protected function prepareForValidation(): void
  {
    $this->merge([
      'path' => $this->path ?? 'CSDB',      
      'remarks' => preg_split("/<br\/>|<br>|&#10;/m",$this->remarks),
    ]);
  }


  /**
   * Handle a passed validation attempt.
   * Bukan mengubah validated data
   */
  protected function passedValidation()
  {
    $otherOptions = [];
    $DMLModel = new Dml();
    $DMLModel->create_xml($this->user()->storage, $this->get('modelIdentCode'), $this->get('originator'), $this->get('dmlType'), $this->get('securityClassification'), $this->get('brexDmRef'), $this->get('remarks'), $otherOptions);

    $this->merge([
      // harus array atau scalar, entah kenapa
      // Expected a scalar, or an array as a 2nd argument to \"Symfony\\Component\\HttpFoundation\\InputBag::set()\", \"Ptdi\\Mpub\\Main\\CSDBObject\" given.
      'CSDBObject' => [$DMLModel->CSDBObject], 
    ]);
  }

}
