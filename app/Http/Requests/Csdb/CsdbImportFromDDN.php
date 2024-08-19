<?php

namespace App\Http\Requests\Csdb;

use App\Models\Csdb;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class CsdbImportFromDDN extends FormRequest
{
  public $duplicatedCSDBModels = [];

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
      'path' => '',
      'DDNCSDBModel' => [function(string $a, mixed $v, Closure $fail){
        if($this->user()->id !== $v->object->dispatchTo_id) $fail("You are not allowed access the {$v->filename}");
      }],
      'filenames' => ['array'],
      'filenames.*' => [function(string $a, mixed $v, Closure $fail){
        if(!in_array($v, $this->DDNCSDBModel->object->ddnContent)) $fail("{$v} is not covered by the {$this->CSDBModel->filename}");
        $this->duplicatedCSDBModels[] = Csdb::getCsdb($v)->first();
      }]
    ];
  }

  protected function prepareForValidation(): void
  {
    Csdb::$storage_user_id = null;
    $CSDBModel = Csdb::getCsdb($this->route()->parameter('filename'),["exception" => ["CSDB-DELL", "CSDB_PDEL"]])->with('object')->first();

    $this->merge([
      'path' => 'CSDB/IMPORTED',
      'DDNCSDBModel' => $CSDBModel
    ]);
  }

  protected function passedValidation()
  {
    $CSDBImportModel = [];

    $this->merge([
      // harus array atau scalar, entah kenapa
      // Expected a scalar, or an array as a 2nd argument to \"Symfony\\Component\\HttpFoundation\\InputBag::set()\", \"Ptdi\\Mpub\\Main\\CSDBObject\" given.
      'CSDBImportModel' => $CSDBImportModel,
    ]);
  }
}
