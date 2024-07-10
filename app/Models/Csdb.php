<?php

namespace App\Models;

use Carbon\Carbon;
use DOMDocument;
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
use Ptdi\Mpub\Main\CSDBObject;
use Ptdi\Mpub\Main\CSDBStatic;
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
      set: fn (string $v) => now()->toString(),
      get: fn (string $v) => now()->toString(),
    );
  }

  /**
   * Set the model updated_at touse current timezone.
   */
  protected function updatedAt(): Attribute
  {
    return Attribute::make(
      set: fn (string $v) => now()->toString(),
      get:fn (string $v) => now()->toString(),
    );
  }

  /**
   * Set path tanpa slash "/" di end string
   * Get path dengan slash "/" di end string
   */
  protected function path(): Attribute
  {
    return Attribute::make(
      set: fn(string $v) => substr($v,-1,1) === '/' ? rtrim($v, "/") : $v,
      // get: fn(string $v) => substr($v,-1,1) === '/' ? $v : $v . "/",
    );
    // dd(substr($str,-1,1 ));
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
  public CSDBObject $CSDBObject;

  public function __construct(){
    $this->usesUniqueIds = true; // agar sama jika pakai/tanpa __construct
    $this->CSDBObject = new CSDBObject("5.0");
  }
  
  /**
   * DEPRECIATED. Dipindah ke Mpub CSDBObject class
   * helper function untuk crew.xsl
   * ini tidak bisa di pindah karena bukan static method
   * * sepertinya bisa dijadikan static, sehingga fungsinya lebih baik ditaruh di CsdbModel saja
   */
  public function setLastPositionCrewDrillStep(int $num)
  {
    $this->lastPositionCrewDrillStep = $num;
  }

  /**
   * DEPRECIATED. Dipindah ke Mpub CSDBObject class
   * helper function untuk crew.xsl
   * ini tidak bisa di pindah karena bukan static method
   * sepertinya bisa dijadikan static, sehingga fungsinya lebih baik ditaruh di CsdbModel saja
   */
  public function getLastPositionCrewDrillStep()
  {
    return $this->lastPositionCrewDrillStep ?? 0;
  }

  /**
   * DEPRECIATED. diganti oleh $CSDBObject
   */
  public \DOMDocument $DOMDocument;
  
  /**
   * DEPRECIATED. karena fungsi transform_to_xml dipindah ke Mpub CSDBObject class
   */
  public string $output = 'html';

  /**
   * DEPRECIATED. Tidak akan dipakai lagi
   */
  public string $repoName = '';

  /**
   * DEPRECIATED. Tidak akan dipakai lagi
   */
  public string $objectpath = '';

  /**
   * DEPRECIATED. TIdak akan dipakai lagi
   */
  public string $absolute_objectpath = '';

  /**
   * DEPRECIATED. Akan ditaruh di Mpub CSDBObject class
   */
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
      case 'status':
        $values = $this->setRemarks_status($value);
        break;
      case 'history':
        $values = $this->setRemarks_history($value);
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

  private function setRemarks_history($value)
  {
    $history = $this->remarks['history'] ?? [];
    if(!$value) return $history;
    array_push($history, $value);
    return $history;
  }

  private function setRemarks_ident()
  {
    $ident = (CSDBStatic::decode_ident($this->filename));
    unset($ident['xml_string']);
    return $ident;
  }

  /**
   * hanya untuk document instanceof \DOMDocument
   */
  private function setRemarks_status()
  {
    // $brex = ($this->CSDBObject->getBrexDm());
    // dd($brex->getFilename());
    $status = [
      'securityClassification' => $this->CSDBObject->getSC('text'),
      'brexDmRef' => $this->CSDBObject->getBrexDm()->getFilename(),
    ];
    $doctypeName = $this->CSDBObject->document->doctype->nodeName;
    if($doctypeName === 'comment'){
      $status['commentPriority'] = '';
    } elseif($doctypeName === 'dmodule' OR $doctypeName === 'pm'){
      $status['qualityAssurance'] = $this->CSDBObject->getQA();
    }
    return $status;
  }

  /** 
   * DEPRECIATED. diganti ke setRemarks_status
   * untuk set remarks sesuai xpath //identAndStatusSection/descendant::remarks/simplePara
   * @param mixed $value bisa berupa string, atau DOM Document
   * @return string 
   * */
  private function setRemarks_remarks($value = ''){
    $remarks_string = [];
    if($value instanceof \DOMDocument){
      $domXpath = new \DOMXPath($value);
    } else {
      $domXpath = new \DOMXPath($this->CSDBObject->document);
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

  /**
   * sudah termasuk revert save
   * save file dulu, kemudian model
   * sudah bisa save file ICN. Mungkin namanya tidak relevan lagi, jadi nanti didepreciated
   */
  public function saveModelAndDOM()
  {
    $fileContents = Storage::disk('csdb')->get($this->filename);
    $save_file = fn() => Storage::disk('csdb')->put($this->filename, ($this->CSDBObject->document instanceof \DOMDocument ? $this->CSDBObject->document->saveXML() : $this->CSDBObject->document->getFile()));
    $revert_save_file = 
      fn() => $fileContents 
        ? Storage::disk('csdb')->put($this->filename, $fileContents)
        : Storage::disk('csdb')->delete($this->filename);
    if ($save_file()) {
      if($this->CSDBObject->document instanceof \DOMDocument){
        $this->setRemarks('ident');
        $this->setRemarks('status');
      }
      if ($this->save()) return true;
      else {
        $revert_save_file();
        return false;
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

  /**
   * akan mengubah URI nya dari file:...../csdb/DMC-aaaa.xml menjadi file:...../csdb/
   */
  public function showCGMArkElement() :void
  {
    // dd($this->CSDBObject->document->documentElement->getAttributeNS('noNamespaceSchemaLocation', 'http://www.w3.org/2001/XMLSchema-instance'));
    if(str_contains($this->CSDBObject->document->documentElement->getAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'noNamespaceSchemaLocation'),'crew.xsd')) return;
    $xsltString = <<<XSLT
    <?xml version="1.0" encoding="UTF-8"?>
    <xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
      
      <xsl:output method="xml" omit-xml-declaration="yes"/>

      <xsl:template match="@* | node()">
        <xsl:copy>
          <xsl:apply-templates select="@* | node()"/>
        </xsl:copy>
      </xsl:template>

      <xsl:template match="//*[@changeMark = '1']">
        <__cgmark>
          <xsl:copy-of select="."/>
        </__cgmark>
      </xsl:template>
    </xsl:transform>
    XSLT;

    $xslDoc = new DOMDocument();
    $xslDoc->loadXML($xsltString);
    
    $xsltProc = new \XSLTProcessor();
    $xsltProc->importStylesheet($xslDoc);

    $newDoc= $xsltProc->transformToDoc($this->CSDBObject->document->cloneNode(true)); // di clone agar DOCTYPE tidak hilang, // baseURInya kosong
    $root = $newDoc->documentElement->cloneNode(true);
    $importRoot = $this->CSDBObject->document->importNode($root, true);
    $this->CSDBObject->document->documentElement->replaceWith($importRoot);
  }

  public function hideCGMArkElement() :void
  {
    $xsltString = <<<XSLT
    <?xml version="1.0" encoding="UTF-8"?>
    <xsl:transform version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
      
      <xsl:output method="xml" omit-xml-declaration="yes"/>

      <xsl:template match="@* | node()">
        <xsl:copy>
          <xsl:apply-templates select="@* | node()"/>
        </xsl:copy>
      </xsl:template>

      <xsl:template match="cgmark">
        <xsl:copy-of select="child::*"/>
      </xsl:template>
    </xsl:transform>
    XSLT; 

    $xslDoc = new DOMDocument();
    $xslDoc->loadXML($xsltString);
    
    $xsltProc = new \XSLTProcessor();
    $xsltProc->importStylesheet($xslDoc);

    $newDoc= $xsltProc->transformToDoc($this->CSDBObject->document->cloneNode(true)); // di clone agar DOCTYPE tidak hilang, // baseURInya kosong
    $root = $newDoc->documentElement->cloneNode(true);
    $importRoot = $this->CSDBObject->document->importNode($root, true);
    $this->CSDBObject->document->documentElement->replaceWith($importRoot);
    return;
  }
}


