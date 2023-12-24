<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['name', 'description'];

  /**
   * The primary key associated with the table.
   *
   * @var string
   */
  protected $primaryKey = 'name';

  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'project';

  /**
   * Indicates if the model's ID is auto-incrementing.
   *
   * @var bool
   */
  public $incrementing = false;

  /**
   * Get the comments for the blog post.
   */
  public function csdb(): HasMany
  {
    // return $this->hasMany(Csdb::class, 'project_name');
    return $this->hasMany(Csdb::class);
  }



  // bisa ini
  // Project::setFailMessage(["foo"], 'applicRefId');
  // return $this->ret(200, [Project::getFailMessage()]);
  // atau
  // return $this->ret(200, [['applicRefId' => ["There is no such {$applicRefId} of applicRefId."]]]);
  // hasilnya di json response.data = {messages: [{applicRefId: ["foo"]}]} // sama dengan withError() di laravel

  protected static $failMessages = [];
  public static function setFailMessage(array $messages = [], string $attribute = '')
  {
    if ($attribute != '') {
      self::$failMessages[$attribute] = array_merge(self::$failMessages[$attribute] ?? [], $messages);
    } else {
      self::$failMessages = array_merge(self::$failMessages, $messages);
    }
  }
  public static function getFailMessage(bool $empty = true, string $attribute = '')
  {
    if ($attribute != '') {
      $m = [$attribute => self::$failMessages[$attribute]];
      $empty ? (self::$failMessages[$attribute] = []) : null;
      return $m;
    } else {
      $m =  self::$failMessages;
      $empty ? (self::$failMessages = []) : null;
      return $m;
    }
  }
}
