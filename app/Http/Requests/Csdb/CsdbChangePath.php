<?php

namespace App\Http\Requests\Csdb;

use App\Models\Csdb;
use App\Rules\Csdb\Path;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class CsdbChangePath extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * sama/mirip dengan CsdbDelete
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'filename' => ['required', 'array', function(string $attribute, mixed $filename, Closure $fail){
        if(is_array($filename)){
          $l = count($filename);
          for ($k=0; $k < $l; $k++) { 
            if(!($this->CSDBModelArray[$k]) && ($this->CSDBModelArray[$k]->filename != $filename[$k])){ // jika null, maka $fail. Tidak mungkin !isset karena sudah di set di prepare
              $fail("There is no such {$filename[$k]} or you are not authorize to change the path.");
            }
          }
        } else {
          if(!($this->CSDBModelArray[0])) $fail("There is no such {$filename} or you are not authorize to change the path.");
        }
      }],
      'CSDBModelArray' => '',
      'path' => ['required', new Path]
    ];
  }

  /**
   * Prepare the data for validation.
   */
  protected function prepareForValidation(): void
  {
    $CSDBModelArray = [];
    $filename = $this->get('filename');
    if(is_array($filename) || ($filename = explode(",",$filename))){
      foreach($filename as $i => $f){
        $m = Csdb::where('filename',$f)->where('initiator_id',$this->user()->id)->first();
        $CSDBModelArray[$i] = $m;
      }
    } else {
      $m = Csdb::where('filename',$filename)->where('initiator_id',$this->user()->id)->first();
      array_push($CSDBModelArray, $m);
      $filename = [$filename];
    }
    $this->merge([
      'CSDBModelArray' => $CSDBModelArray,
      'filename' => $filename,
    ]);
  }
}
