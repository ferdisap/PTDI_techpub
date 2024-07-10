<?php

namespace App\Rules\Csdb;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Ptdi\Mpub\Main\CSDBStatic;

class BrexDmRef implements ValidationRule
{
  /**
   * Run the validation rule.
   *
   * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
   */
  public function validate(string $attribute, mixed $value, Closure $fail): void
  {
    if (count(explode("_", $value)) < 3) $fail("The {$attribute} must contain IssueInfo and Language.");
    $decode = CSDBStatic::decode_dmIdent($value);
    if ($decode and $decode['dmCode']['infoCode'] != '022') $fail("The {$attribute} infoCode must be '022'.");
  }
}
