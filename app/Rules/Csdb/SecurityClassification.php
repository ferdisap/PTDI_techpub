<?php

namespace App\Rules\Csdb;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SecurityClassification implements ValidationRule
{
  /**
   * Run the validation rule.
   *
   * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
   */
  public function validate(string $attribute, mixed $value, Closure $fail): void
  {
    if(strlen($value) != 2 && (int)$value) $fail("$value must be number in two digits.");
    // $allowed = ['01','02','03','04','05'];    
    // if(!$this->required) $allowed[] = '';
    // if(!in_array($value, $allowed)){
    //   $allowed = array_map( fn($v) => $v == '' ? ("'no need to be fullfilled'") : $v,$allowed);
    //   $last = count($allowed) - 1;
    //   $allowed[$last] = 'or '. $allowed[$last];
    //   $fail("The {$attribute} must be " . join(', ', $allowed) . ".");
    // }
  }
}
