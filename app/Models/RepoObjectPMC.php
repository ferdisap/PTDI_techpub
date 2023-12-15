<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ptdi\Mpub\CSDB;

class RepoObjectPMC extends Model
{
  use HasFactory;


  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'repo_object_pmc';

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
  protected $fillable = ['repo_id', 'filename', 'pt', 'title', 'issuedate', 'sc'];

  /**
   * Indicates if the model should be timestamped.
   *
   * @var bool
   */
  public $timestamps = false;

  public function repo(): BelongsTo
  {
    return $this->belongsTo(Repo::class);
  }

  public function pmEntry()
  {
    $path = storage_path("app/{$this->repo->path}/");
    $filename = $this->filename;
    $doc = CSDB::importDocument($path, $filename);

    $content = $doc->getElementsByTagName('content')[0];
    $pmEntry = $content->getElementsByTagName('pmEntry')[0];
    // dd($pmEntry->C14N());
    return $this->handle_pmEntry($pmEntry);
  }

  private function handle_pmEntry(\DOMElement $pmEntry)
  {
    $pmEntries = [];
    $pmEntries[] = $this->resolve_childPmEntry($pmEntry);
    while($pmEntry->nextElementSibling){
      if($pmEntry->tagName == 'pmEntry'){
        $pmEntries[] = $this->resolve_childPmEntry($pmEntry->nextElementSibling);
      }
      $pmEntry = $pmEntry->nextElementSibling;
    }
    return $pmEntries;
  }

  private function resolve_childPmEntry($pmEntry)
  {
    $collect = [
      'level' => CSDB::checkLevel($pmEntry,2),
      'pmType' => $pmEntry->getAttribute('pmType'),
      'title' => '',
      'content' => [],
    ];
    foreach(CSDB::children($pmEntry) as $child){
      if($child->tagName == 'dmRef'){
        $collect['content'][] = CSDB::resolve_dmIdent($child->getElementsByTagName('dmRefIdent')[0]);
      }
      elseif($child->tagName == 'pmRef'){
        $collect['content'][] = CSDB::resolve_pmIdent($child->getElementsByTagName('pmRefIdent')[0]);
      }
      elseif($child->tagName == 'externalPubRef'){
        $collect['content'][] = CSDB::resolve_externalPubRefIdent($child);
      }
      elseif($child->tagName == 'pmEntry'){
        $collect['content'][] = $this->resolve_childPmEntry($child);
      }
      elseif($child->tagName == 'pmEntryTitle'){
        $collect['title'] = $child->nodeValue;
      }
    }
    return $collect;
  }

}
