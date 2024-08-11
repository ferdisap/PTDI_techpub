<?php

namespace App\Rules\Csdb;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SeqNumber implements ValidationRule
{
  /**
   * Run the validation rule.
   *
   * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
   */
  public function validate(string $attribute, mixed $value, Closure $fail): void
  {
    if((strlen($value) !== 5) && ((strlen(preg_replace('/[0-9]+/m','',$value))) > 0)) $fail("{$attribute} must contain five numeric characters.");
  }
}
