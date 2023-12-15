<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepoObjectDMC extends Model
{
  use HasFactory;

  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'repo_object_dmc';

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
  protected $fillable = ['repo_id', 'filename', 'title', 'issuedate', 'schema', 'sc'];

  /**
   * Indicates if the model should be timestamped.
   *
   * @var bool
   */
  public $timestamps = false;
}
