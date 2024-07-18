<?php

namespace App\Models\Csdb;

use App\Models\Csdb;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ptdi\Mpub\Main\CSDBStatic;
use Illuminate\Support\Facades\Auth;
use Ptdi\Mpub\Main\CSDBObject;
use Ptdi\Mpub\Main\Helper;

class Ddn extends Csdb
{
  use HasFactory;

  protected $attributes = [
    'ddnContent' => '[]',
  ];

  /**
   * harus json string
   * set value akan menjadi json string curly atau json string array []
   * get value akan menjadi array
   */
  protected function ddnContent(): Attribute
  {
    return Attribute::make(
      set: fn($v) => is_array($v) ? json_encode($v) :(
        $v && Helper::isJsonString($v) ? $v : json_encode($v ? [$v] : [])
      ),
      get: fn($v) => json_decode($v, true),
    );
  }

  public function create_xml(string $storagePath, Array $params)
  {
    $this->CSDBObject = new CSDBObject('5.0');
    $this->CSDBObject->setPath(CSDB_STORAGE_PATH . "/" . $storagePath);
    $this->CSDBObject->setConfigXML(CSDB_VIEW_PATH . DIRECTORY_SEPARATOR . "xsl" . DIRECTORY_SEPARATOR . "Config.xml"); // nanti diubah mungkin berbeda antara pdf dan html meskupun harusnya SAMA. Nanti ConfigXML mungkin tidak diperlukan jika fitur BREX sudah siap sepenuhnya.
    $this->CSDBObject->createDDN($params);

    if($this->CSDBObject->document){
      return true;
    }
    return false;
  }

  public static function fillTable(CSDBObject $CSDBObject)
  {
    $filename = $CSDBObject->filename;
    // $decode_ident = CSDBStatic::decode_ddnIdent($filename,false); 
    // $ddnAddressItems = $CSDBObject->document->getElementsByTagName('ddnAddressItems')[0];
    // $issueDate = $ddnAddressItems->firstElementChild;

    // $domXpath = new \DOMXpath($CSDBObject->document);
    // $sc = $domXpath->evaluate("string(//identAndStatusSection/descendant::security/@securityClassification)");
    // $authorization = $domXpath->evaluate("string(//identAndStatusSection/descendant::authorization)");
    // $brexElement = $domXpath->evaluate("//identAndStatusSection/descendant::brexDmRef/dmRef/dmRefIdent")[0];
    // $brexDmRef = CSDBStatic::resolve_dmIdent($brexElement);
    // $remarks = $CSDBObject->getRemarks($domXpath->evaluate("//identAndStatusSection/descendant::remarks")[0]);

    $domXpath = new \DOMXpath($CSDBObject->document);
    $modelIdentCode = $domXpath->evaluate("string(//ddnAddress/ddnIdent/ddnCode/@modelIdentCode)");
    $senderIdent = $domXpath->evaluate("string(//ddnAddress/ddnIdent/ddnCode/@senderIdent)");
    $receiverIdent = $domXpath->evaluate("string(//ddnAddress/ddnIdent/ddnCode/@receiverIdent)");
    $yearOfDataIssue = $domXpath->evaluate("string(//ddnAddress/ddnIdent/ddnCode/@yearOfDataIssue)");
    $seqNumber = $domXpath->evaluate("string(//ddnAddress/ddnIdent/ddnCode/@seqNumber)");

    $year = $domXpath->evaluate("string(//ddnAddress/ddnAddressItems/issueDate/@year)");
    $month = $domXpath->evaluate("string(//ddnAddress/ddnAddressItems/issueDate/@month)");
    $day = $domXpath->evaluate("string(//ddnAddress/ddnAddressItems/issueDate/@day)");

    $securityClassification = $domXpath->evaluate("string(//ddnStatus/security/@securityClassification)");
    $brexElement = $domXpath->evaluate("//identAndStatusSection/descendant::brexDmRef/dmRef/dmRefIdent")[0];
    $brexDmRef = CSDBStatic::resolve_dmIdent($brexElement);
    $authorization = $domXpath->evaluate("string(//identAndStatusSection/descendant::authorization)");
    $remarks = $CSDBObject->getRemarks($domXpath->evaluate("//identAndStatusSection/descendant::remarks")[0]);

    $ddnContent = $domXpath->evaluate("//ddnContent/descendant::dispatchFilename|//ddnContent/mediaIdent");
    if(!empty($ddnContent)){
      $r = [];
      foreach($ddnContent as $content){
        switch ($content->tagName) {
          case 'dispatchFilename':
            $r[] = $content->nodeValue;
            break;
          case 'mediaIdent':
            // TBD
            break;
        }
      }
      $ddnContent = $r;
    } else {
      $ddnContent = [];
    }

    $arr = [
      'filename' => $filename,
      'modelIdentCode' => $modelIdentCode,
      'senderIdent' => $senderIdent,
      'receiverIdent' => $receiverIdent,
      'yearOfDataIssue' => $yearOfDataIssue,
      'seqNumber' => $seqNumber,
      
      "year" => $year,
      "month" => $month,
      "day" => $day,
      
      'securityClassification' => $securityClassification,
      'brexDmRef' => $brexDmRef,
      'authorization' => $authorization,
      'remarks' => $remarks,
      
      'ddnContent' => $ddnContent,
    ];

    $fillable = [
      'filename',
      'modelIdentCode',
      'senderIdent',
      'receiverIdent',
      'yearOfDataIssue',
      'seqNumber',

      "year",
      "month",
      "day",

      'securityClassification',
      'brexDmRef',
      'authorization',
      'remarks',

      'ddnContent',
    ];

    $ddn = new self();
    $ddn->setProtected([
      'table' => 'ddn',
      'fillable' => $fillable,
      'casts' => [],
      // 'attributes' => [],
      'timestamps' => false
    ]);
    $ddn = $ddn->where('filename', $filename)->first() ?? $ddn;
    $ddn->timestamps = false;
    foreach($arr as $prop => $v){
      $ddn->$prop = $v;
    }
    return $ddn->save();
  }
}
