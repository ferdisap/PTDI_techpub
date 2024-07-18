<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class RemarksTes implements CastsAttributes
{
  /**
   * Cast the given value.
   *
   * @param  array<string, mixed>  $attributes
   */
  public function get(Model $model, string $key, mixed $value, array $attributes): mixed
  {
    return ['get default'];
    // dd('ccc');
    return $value;
  }

  /**
   * Prepare the given value for storage.
   *
   * @param  array<string, mixed>  $attributes
   */
  public function set(Model $model, string $key, mixed $value, array $attributes): mixed
  {
    return ['set defualt'];
    // dd('bbb');
    // return $value;
  }
}
