<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Repo extends Model
{
  use HasFactory;


  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'repo';

  /**
   * The primary key associated with the table.
   *
   * @var string
   */
  protected $primaryKey = 'id';

  /**
   * The database connection that should be used by the migration.
   *
   * @var string
   */
  protected $connection = 'sqlite_ietm';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['name', 'path', 'project_name', 'token'];

  /**
   * Get the project of the repo
   */
  public function project(): BelongsTo
  {
    return $this->belongsTo(Project::class);
  }
}
