<?php

namespace App\Models\Csdb;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ptdi\Mpub\Main\CSDBObject;
use Ptdi\Mpub\Main\CSDBStatic;

class Pmc extends Model
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
    $decode_ident = CSDBStatic::decode_pmIdent($filename,false);
    $pmAddressItems = $CSDBObject->document->getElementsByTagName('pmAddressItems')[0];
    $issueDate = $pmAddressItems->firstElementChild;
    $pmTitle = $issueDate->nextElementSibling;
    $shortPmTitle = $pmTitle->nextElementSibling;

    $domXpath = new \DOMXpath($CSDBObject->document);
    $sc = $domXpath->evaluate("string(//identAndStatusSection/descendant::security/@securityClassification)");
    $rsp = $domXpath->evaluate("string(//identAndStatusSection/descendant::responsiblePartnerCompany/enterpriseName)");
    $originator = $domXpath->evaluate("string(//identAndStatusSection/descendant::originator/enterpriseName)");
    $applicEl = $domXpath->evaluate("//identAndStatusSection/descendant::applic")[0];
    $applic = $CSDBObject->getApplicability($applicEl);
    $brexElement = $domXpath->evaluate("//identAndStatusSection/descendant::brexDmRef/dmRef/dmRefIdent")[0];
    $brexDmRef = CSDBStatic::resolve_pmIdent($brexElement);
    $QA = $domXpath->evaluate("//identAndStatusSection/descendant::qualityAssurance/*[last()]")[0];
    $QAtext = $CSDBObject->getQA(null, $QA);
    $remarks = $CSDBObject->getRemarks($domXpath->evaluate("//identAndStatusSection/descendant::remarks")[0]);

    $arr = [
      "filename" => $filename,
      
      "modelIdentCode" => $decode_ident['pmCode']['modelIdentCode'],
      "pmIssuer" => $decode_ident['pmCode']['pmIssuer'],
      "pmNumber" => $decode_ident['pmCode']['pmNumber'],
      "pmVolume" => $decode_ident['pmCode']['pmVolume'],
      
      "languageIsoCode" => $decode_ident['language']['languageIsoCode'],
      "countryIsoCode" => $decode_ident['language']['countryIsoCode'],
      
      "issueNumber" => $decode_ident['issueInfo']['issueNumber'],
      "inWork" => $decode_ident['issueInfo']['inWork'],

      "year" => $issueDate->getAttribute('year'),
      "month" => $issueDate->getAttribute('month'),
      "day" => $issueDate->getAttribute('day'),

      "pmTitle" => $pmTitle->nodeValue,
      "shortPmTitle" => $shortPmTitle ? $shortPmTitle->nodeValue : '',

      'securityClassification' => $sc,
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
