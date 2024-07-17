<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Enterprise extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'code',
    'address',
    'remarks',
  ];

  /**
   * Indicates if the modul should be timestamped
   * 
   * @var bool
   */
  public $timestamps = false;

  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'enterprises';

  /**
   * The primary key associated with the table.
   *
   * @var string
   */
  protected $primaryKey = 'id';

  /**
   * The attributes that should be cast.
   * @var array
   */
  protected $casts = [
    'address' => 'array'
  ];

  protected $hidden = ['id'];

  protected function address(): Attribute
  {
    return Attribute::make(
      set: function ($v) {
        $v = is_string($v) ? json_decode($v) : $v;
        $a = [
          "city" => $v->city ?? '',
          "country" => $v->country ?? '',
          'department' => $v->department ?? '',
          'street' => $v->street ?? '',
          'postOfficeBox' => $v->postOfficeBox ?? '',
          'postalZipCode' => $v->postalZipCode ?? '',
          'city' => $v->city ?? '',
          'country' => $v->country ?? '',
          'state' => $v->state ?? '',
          'province' => $v->province ?? '',
          'building' => $v->building ?? '',
          'room' => $v->room ?? '',
          'phoneNumber' => $v->phoneNumber ?? [],
          'faxNumber' => $v->faxNumber ?? [],
          'email' => $v->email ?? [],
          'internet' => $v->internet ?? [],
          'SITA' => $v->SITA ?? '',
        ];
        return json_encode($a);
      },
      get: fn ($v) => json_decode($v),
    );
  }
}
