<?php

namespace App\Http\Requests\Csdb;

use App\Models\Csdb;
use App\Rules\Csdb\Path;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Ptdi\Mpub\Main\CSDBError;
use Ptdi\Mpub\Main\CSDBValidator;

class UploadICN extends FormRequest
{
  public bool $isUpdate = false;
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
      "filename" => ['required', function (string $attribute, mixed $value,  Closure $fail) {
        $oldCSDBModel = Csdb::where('filename', $value)->first();
        if($oldCSDBModel) $this->isUpdate = true;

        CSDBError::$processId = 'ICNFilenameValidation';
        $validator = new CSDBValidator('ICNName', ["validatee" => $value]);
        $validator->setStoragePath(CSDB_STORAGE_PATH."/".$this->user()->storage);
        if (!$validator->validate()) $fail(join(", ", CSDBError::getErrors(true, 'ICNFilenameValidation')));
      }],
      "entity" => ['required', function (string $attribute, mixed $value,  Closure $fail) {
        $ext = strtolower($value->getClientOriginalExtension());
        $mime = strtolower($value->getMimeType());
        if ($ext === 'xml' or str_contains($mime, 'text')) {
          $fail("You should put the non-text file in {$attribute}.");
        }
      }],
      'path' => ['required', new Path]
    ];
  }
}
