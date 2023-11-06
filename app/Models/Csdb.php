<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Csdb extends Model
{
  use HasFactory;

  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'csdb';

  /**
   * The primary key associated with the table.
   *
   * @var string
   */
  protected $primaryKey = 'id';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['path', 'status', 'description', 'initiator_id'];

  /**
   * Get the initiator for the DMC
   */
  public function initiator(): BelongsTo
  {
    // return $this->hasMany(User::class);
    return $this->belongsTo(User::class);
  }
}
