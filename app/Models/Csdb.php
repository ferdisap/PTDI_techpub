<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\TryCatch;
use Ptdi\Mpub\CSDB as MpubCSDB;
use Ptdi\Mpub\Helper;
use Ptdi\Mpub\ICNDocument;
use Ptdi\Mpub\Pdf2\Applicability;

/**
 * remark ['stage'] itu cuma ada unstaged, staging, staged, deleted; 
 * kayaknya 'deleted' tidak terpakai
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
   * The attributes that should be cast.
   * @var array
   */
  protected $casts = [
    'remarks' => 'array'
  ];

  /**
   * Set the model created_at touse current timezone.
   */
  protected function createdAt(): Attribute
  {
    return Attribute::make(
      set: fn (string $v) => Carbon::now(7),
    );
  }

  /**
   * Set the model updated_at touse current timezone.
   */
  protected function updatedAt(): Attribute
  {
    return Attribute::make(
      set: fn (string $v) => Carbon::now(7),
    );
  }

  public function hide(mixed $column)
  {
    if (is_array($column)) {
      foreach ($column as $col) {
        $this->hidden[] = $col;
      }
    } elseif ($column == false) {
      $this->hidden = [];
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

  public function transform_to_xml($path_xsl, $filename_xsl = '', $configuration = '')
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

    // $xsltproc->setParameter('', 'repoName', $this->repoName);
    // $xsltproc->setParameter('', 'objectpath', $this->objectpath);
    // $xsltproc->setParameter('', 'absolute_objectpath', $this->absolute_objectpath);
    // $schemaFilename = MpubCSDB::getSchemaUsed($this->DOMDocument,'filename');
    // $xsltproc->setParameter('', 'schema', $schemaFilename);
    $xsltproc->setParameter('', 'configuration', $configuration);
    if($this->filename){
      $decode_ident = Helper::decode_ident($this->filename);
      $object_code = $decode_ident[array_key_first($decode_ident)];
      $object_code = array_filter($object_code, fn($v) => $v);
      $object_code = join("-", $object_code);
      $xsltproc->setParameter('', 'object_code', $object_code);
    }
    // $xsltproc->setParameter('', 'icnPath', '/images/'); // nanti diganti '/csdb/'
    $xsltproc->setParameter('', 'icnPath', '/csdb/icn'); // nanti diganti '/csdb/'

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
        if (count($values) >= 5) array_pop($values);
        $values = array_unique($values);
        break;
      case 'title':
        $values = $this->setRemarks_title($value);
        break;
      case 'remarks':
        $values = $this->setRemarks_remarks($value);
        break;
      case 'ident':
        $values = $this->setRemarks_ident($value);
        break;
      default:
        $values = $value;
        break;
    }
    $remarks[$key] = $values;
    $this->remarks = $remarks;

    if ($this->direct_save) {
      $this->save();
    }
  }

  private function setRemarks_ident($filename = '')
  {
    if(!$filename){
      $filename = $this->filename;
    }
    $ident = (Helper::decode_ident($filename));
    unset($ident['xml_string']);
    return $ident;
  }

  /** 
   * untuk set remarks sesuai xpath //identAndStatusSection/descendant::remarks/simplePara
   * @param mixed $value bisa berupa string, atau DOM Document
   * @return string 
   * */
  private function setRemarks_remarks($value = ''){
    $remarks_string = [];
    if($value instanceof \DOMDocument){
      $domXpath = new \DOMXPath($value);
    } else {
      $domXpath = new \DOMXPath($this->DOMDocument);
    }
    $simpleParas = $domXpath->evaluate('//identAndStatusSection/descendant::remarks/simplePara');
    foreach ($simpleParas as $key => $simplePara) {
      $remarks_string[] = $simplePara->textContent;
    }
    $remarks_string = join(PHP_EOL, $remarks_string);
    return !empty($remarks_string) ? $remarks_string : '';
  }

  /**
   * @return string
   */
  private function setRemarks_title($dom = '')
  {
    if(!$dom){
      $dom = MpubCSDB::importDocument(storage_path('csdb'), $this->filename);
    }
    if ($dom instanceof ICNDocument) {
      $imfFilename = MpubCSDB::detectIMF(storage_path('csdb'), $dom->getFilename());
      $dom = MpubCSDB::importDocument(storage_path('csdb'), $imfFilename);
      if (!$dom) return '';
    }
    return MpubCSDB::resolve_DocTitle($dom);
  }

  public function saveModelAndDOM()
  {
    // coba2 | hasil: tidak berpengaruh
    // if(isset($this->DOMDocument)){
    //   $this->DOMDocument->formatOutput = false;
    // }
    // dd($this->DOMDocument);

    // if (isset($this->DOMDocument) and $this->DOMDocument->C14NFile(storage_path($this->path) . DIRECTORY_SEPARATOR . $this->filename)) {
    if (isset($this->DOMDocument) and Storage::disk('csdb')->put($this->filename, $this->DOMDocument->saveXML())) {
      $this->setRemarks('ident');
      if ($this->save()) {
        return true;
      }
    }
    return false;
  }

  /**
   * jika $column tersedia di database 'csdb' atau 'csdb_deleted'.
   * jika ada dua column yang sama, tetap akan diambil column pertama yang found.
   * @return string 
   */
  public static function columnNameMatching(string $column , string $dbName = '')
  {
    if(!$dbName){
      $found = array_unique(array_merge(DB::getSchemaBuilder()->getColumnListing('csdb'), DB::getSchemaBuilder()->getColumnListing('csdb_deleted')));
    } else {
      $found = DB::getSchemaBuilder()->getColumnListing($dbName);
    }
    $found = array_filter($found, function($v) use($column){
      $v = str_contains($v, $column) ? $column : (
        str_contains($column, $v) ? $column : false
      );
      return $v;
    });
    $found = !empty($found) ? $found[array_key_first($found)] : '';
    return $found;
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
