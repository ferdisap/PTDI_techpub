<?php

namespace App\Models\Csdb;

use App\Models\Csdb;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ptdi\Mpub\Main\CSDBObject;
use Ptdi\Mpub\Main\CSDBStatic;

class Pmc extends Csdb
{
  use HasFactory;

  
  protected $fillable = [
    'filename',

    'modelIdentCode',
    'pmIssuer',
    'pmNumber',
    'pmVolume',
    'languageIsoCode',
    'countryIsoCode',
    'issueNumber',
    'inWork',

    'year',
    'month',
    'day',
    
    'pmTitle',
    'shortPmTitle',

    'securityClassification',
    'responsiblePartnerCompany',
    'originator',
    'applicability',
    'brexDmRef',
    'qa',
    'remarks',
  ];

  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'pmc';

  /**
   * Indicates if the modul should be timestamped
   * 
   * @var bool
   */
  public $timestamps = false;

  public static function fillTable(CSDBObject $CSDBObject)
  {
    $filename = $CSDBObject->filename;
    $domXpath = new \DOMXpath($CSDBObject->document);


    $modelIdentCode = $domXpath->evaluate("string(//pmAddress/pmIdent/pmCode/@modelIdentCode)");
    $pmIssuer = $domXpath->evaluate("string(//pmAddress/pmIdent/pmCode/@pmIssuer)");
    $pmNumber = $domXpath->evaluate("string(//pmAddress/pmIdent/pmCode/@pmNumber)");
    $pmVolume = $domXpath->evaluate("string(//pmAddress/pmIdent/pmCode/@pmVolume)");
    
    $languageIsoCode = $domXpath->evaluate("string(//pmAddress/pmIdent/language/@languageIsoCode)");
    $countryIsoCode = $domXpath->evaluate("string(//pmAddress/pmIdent/language/@countryIsoCode)");

    $issueNumber = $domXpath->evaluate("string(//pmAddress/pmIdent/issueInfo/@issueNumber)");
    $inWork = $domXpath->evaluate("string(//pmAddress/pmIdent/issueInfo/@inWork)");

    $year = $domXpath->evaluate("string(//pmAddress/pmAddressItems/issueDate/@year)");
    $month = $domXpath->evaluate("string(//pmAddress/pmAddressItems/issueDate/@month)");
    $day = $domXpath->evaluate("string(//pmAddress/pmAddressItems/issueDate/@day)");

    $pmTitle = $domXpath->evaluate("string(//pmAddress/pmAddressItems/pmTitle)");
    $shortPmTitle = $domXpath->evaluate("string(//pmAddress/pmAddressItems/shortPmTitle)");

    $securityClassification = $domXpath->evaluate("string(//pmStatus/security/@securityClassification)");
    $brexElement = $domXpath->evaluate("//identAndStatusSection/descendant::brexDmRef/dmRef/dmRefIdent")[0];
    $brexDmRef = CSDBStatic::resolve_dmIdent($brexElement);
    $rsp = $domXpath->evaluate("string(//identAndStatusSection/descendant::responsiblePartnerCompany/enterpriseName)");
    $originator = $domXpath->evaluate("string(//identAndStatusSection/descendant::originator/enterpriseName)");
    $applicEl = $domXpath->evaluate("//identAndStatusSection/descendant::applic")[0];
    $applic = $CSDBObject->getApplicability($applicEl);

    $QA = $domXpath->evaluate("//identAndStatusSection/descendant::qualityAssurance/*[last()]")[0];
    $QAtext = $CSDBObject->getQA(null, $QA);
    $remarks = $CSDBObject->getRemarks($domXpath->evaluate("//identAndStatusSection/descendant::remarks")[0]);

    $arr = [
      "filename" => $filename,
      
      "modelIdentCode" => $modelIdentCode,
      "pmIssuer" => $pmIssuer,
      "pmNumber" => $pmNumber,
      "pmVolume" => $pmVolume,
      
      "languageIsoCode" => $languageIsoCode,
      "countryIsoCode" => $countryIsoCode,
      
      "issueNumber" => $issueNumber,
      "inWork" => $inWork,

      "year" => $year,
      "month" => $month,
      "day" => $day,

      "pmTitle" => $pmTitle,
      "shortPmTitle" => $shortPmTitle,

      'securityClassification' => $securityClassification,
      'responsiblePartnerCompany' => $rsp,
      'originator' => $originator,
      'applicability' => $applic,
      'brexDmRef' => $brexDmRef,
      'qa' => $QAtext,
      'remarks' => $remarks
    ];
 
    $pmc = self::where('filename', $filename)->first();
    if($pmc) {
      $pmc->update($arr);
      return $pmc->save();
    }
    return self::create($arr);
  }
}
