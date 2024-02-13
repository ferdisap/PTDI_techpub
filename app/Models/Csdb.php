<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ptdi\Mpub\CSDB as MpubCSDB;
use Ptdi\Mpub\ICNDocument;
use Ptdi\Mpub\Pdf2\Applicability;

/**
 * remark ['stage'] itu cuma ada unstaged, staging, staged, deleted;
 */
class Csdb extends Model
{
  use HasFactory, HasUlids, Applicability;

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
  // protected $fillable = ['filename', 'path', 'status', 'description', 'initiator_id', 'project_name', 'remarks'];
  protected $fillable = ['filename', 'path', 'editable', 'initiator_id', 'remarks'];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = ['initiator_id'];

  /**
   * 
   */
  protected $casts = [
    'remarks' => 'array'
  ];

  public function hide(mixed $column)
  {
    if(is_array($column)){
      foreach($column as $col){
        $this->hidden[] = $col;
      }
    } else {
      $this->hidden[] = $column;
    }
    $this->hidden = array_unique($this->hidden);
  }

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
    
    $xsltproc = new \XSLTProcessor();
    $xsltproc->importStylesheet($xsl);
    $xsltproc->registerPHPFunctions((fn () => array_map(fn ($name) => MpubCSDB::class . "::$name", get_class_methods(MpubCSDB::class)))());
    $xsltproc->registerPHPFunctions((fn () => array_map(fn ($name) => self::class . "::$name", get_class_methods(self::class)))());
    // $xsltproc->registerPHPFunctions([self::class . "::getLastPositionCrewDrillStep", self::class . "::setLastPositionCrewDrillStep"]);
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
    
    $transformed = preg_replace("/\s+/m", ' ', $transformed);
    $transformed = preg_replace("/v-on_/m", 'v-on:', $transformed); // nanti ini dihapus. Setiap xml akan ditambahkan namespace xmlns:v-bind, xmlns:v-on, dll 
    $transformed = preg_replace('/xmlns:[\w\-=":\/\\\\._]+/m', '', $transformed); // untuk menghilangkan attribute xmlns
    
    return $transformed;
  }

  /**
   * syaratnya harus manggil id agar bisa di save. Sengaja tidak dibuat manual agar tidak asal isi
   * biasanya, securityClassification, stage, crud
   * @return void
   */
  public bool $direct_save = true;
  public function setRemarks($key, $value = '')
  {
    $remarks = $this->remarks;
    $values = $remarks[$key] ?? [];
    switch ($key) {
      case 'searchkey':
        array_unshift($values, $value);
        if(count($values) >= 5) array_pop($values);
        $values = array_unique($values);
        break;
      case 'title':
        if(!$value){
          $value = $this->setRemarks_title();
        }
        $values = $value;
        break;
      default:
        $values = $value;
        break;
    }
    $remarks[$key] = $values;
    $this->remarks = $remarks;
    
    if($this->direct_save){
      $this->save();
    }
  }

  /**
   * @return string
   */
  private function setRemarks_title()
  {
    $dom = MpubCSDB::importDocument(storage_path("app/".$this->path),$this->filename);
    if($dom instanceof ICNDocument){
      $imfFilename = MpubCSDB::detectIMF(storage_path("app/".$this->path), $dom->getFilename());
      $dom = MpubCSDB::importDocument(storage_path("app/".$this->path), $imfFilename);
      if(!$dom) return '';
    }
    $value = MpubCSDB::resolve_DocTitle($dom);
    return $value;
  }

  public function saveModelAndDOM()
  {
    // coba2 | hasil: tidak berpengaruh
    // if(isset($this->DOMDocument)){
    //   $this->DOMDocument->formatOutput = false;
    // }
    // dd($this->DOMDocument);
    
    if(isset($this->DOMDocument) AND $this->DOMDocument->C14NFile(storage_path($this->path).DIRECTORY_SEPARATOR.$this->filename)){
      if($this->save()){
        return true;
      }
    }
    return false;
  }

 

  /**
   * untuk menambah namespace pada DOMDocument xsl
   */
  // private function addVueNamespace(\DOMDocument $doc)
  // {
  //   $ns = ['v-bind','v-on'];
  //   $root = $doc->firstElementChild;
  //   // xmlns:v="https://vuejs.org"
  //   foreach ($ns as $namespace) {
  //     $root->setAttribute("xmlns:{$namespace}", "https://vuejs.org/{$namespace}");
  //   }
  //   return $doc;
  // }
}
