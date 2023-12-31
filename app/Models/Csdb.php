<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ptdi\Mpub\CSDB as MpubCSDB;

class Csdb extends Model
{
  use HasFactory, HasUlids;

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
   * Indicates if the model's ID is auto-incrementing.
   *
   * @var bool
   */
  public $incrementing = false;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['filename', 'path', 'status', 'description', 'editable', 'initiator_id', 'project_name'];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = ['initiator_id'];


  /**
   * Get the initiator for the csdb object
   */
  public function initiator(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Get the post that owns the comment.
   */
  public function project(): BelongsTo
  {
    return $this->belongsTo(Project::class, 'project_name');
  }



  ###### CUSTOM #######
  /**
   * helper function untuk crew.xsl
   * ini tidak bisa di pindah karena bukan static method
   * * sepertinya bisa dijadikan static, sehingga fungsinya lebih baik ditaruh di CsdbModel saja
   */
  public function setLastPositionCrewDrillStep(int $num)
  {
    $this->lastPositionCrewDrillStep = $num;
  }

  /**
   * helper function untuk crew.xsl
   * ini tidak bisa di pindah karena bukan static method
   * sepertinya bisa dijadikan static, sehingga fungsinya lebih baik ditaruh di CsdbModel saja
   */
  public function getLastPositionCrewDrillStep()
  {
    return $this->lastPositionCrewDrillStep ?? 0;
  }

  public \DOMDocument $DOMDocument;
  public string $output = 'html';
  public string $repoName = '';
  public string $objectpath = '';
  public string $absolute_objectpath = '';

  public function transform_to_xml($path_xsl, $filename_xsl = '')
  {
    if (!$filename_xsl) {
      $type = $this->DOMDocument->documentElement->nodeName;
      $filename_xsl = "{$type}.xsl";
    }
    
    $xsl = MpubCSDB::importDocument($path_xsl . "/", $filename_xsl);
    // dd($xsl, $path_xsl, $filename_xsl);
    // dd($filename_xsl, $xsl);
    $xsltproc = new \XSLTProcessor();
    $xsltproc->importStylesheet($xsl);
    $xsltproc->registerPHPFunctions((fn () => array_map(fn ($name) => MpubCSDB::class . "::$name", get_class_methods(MpubCSDB::class)))());
    $xsltproc->registerPHPFunctions([self::class . "::getLastPositionCrewDrillStep", self::class . "::setLastPositionCrewDrillStep"]);
    $xsltproc->registerPHPFunctions();
    
    $xsltproc->setParameter('', 'repoName', $this->repoName);
    $xsltproc->setParameter('', 'objectpath', $this->objectpath);
    $xsltproc->setParameter('', 'absolute_objectpath', $this->absolute_objectpath);
    // dd($path_xsl, $filename_xsl);
    
    
    if ($this->output == 'html') {
      $transformed = str_replace("#ln;", '<br/>', $xsltproc->transformToXml($this->DOMDocument));
    } else {
      $transformed = str_replace("#ln;", chr(10), $xsltproc->transformToXml($this->DOMDocument));
    }

    $transformed = str_replace("\n", '', $transformed);

    $transformed = preg_replace("/\s+/", ' ', $transformed);
    $transformed = preg_replace("/v-on_/", 'v-on:', $transformed);
    
    return $transformed;
  }
}
