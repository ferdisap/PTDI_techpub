<?php

namespace App\Models\Csdb;

use App\Jobs\Csdb\DmcTableFiller;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ptdi\Mpub\Main\CSDBObject;
use Ptdi\Mpub\Main\CSDBStatic;

class Dmc extends Model
{
  use HasFactory;

  protected $fillable = [
    'filename', 

    'modelIdentCode',
    'systemDiffCode',
    'systemCode',
    'subSystemCode',
    'subSubSystemCode',
    'assycode',
    'disassyCode',
    'disassyCodeVariant',
    'infoCode',
    'infoCodeVariant',
    'itemLocationCOde',
    'languageIsoCode',
    'countryIsoCode',
    'issueNumber',
    'inWork',
    'year',
    'month',
    'day',
    'techName',
    'infoName',
    'infoNameVariant',

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
  protected $table = 'dmc';

  /**
   * Indicates if the modul should be timestamped
   * 
   * @var bool
   */
  public $timestamps = false;

  public static function fillTable(CSDBObject $CSDBObject){

    $filename = $CSDBObject->filename;
    $decode_ident = CSDBStatic::decode_dmIdent($filename,false);
    $dmAddressItems = $CSDBObject->document->getElementsByTagName('dmAddressItems')[0];
    $issueDate = $dmAddressItems->firstElementChild;
    $dmTitle = $issueDate->nextElementSibling;
    $techName = $dmTitle->firstElementChild;
    $infoName = $techName->nextElementSibling;
    $infoNameVariant = $infoName ? $infoName->nextElementSibling : null;

    $domXpath = new \DOMXpath($CSDBObject->document);
    $sc = $domXpath->evaluate("string(//identAndStatusSection/descendant::security/@securityClassification)");
    $rsp = $domXpath->evaluate("string(//identAndStatusSection/descendant::responsiblePartnerCompany/enterpriseName)");
    $originator = $domXpath->evaluate("string(//identAndStatusSection/descendant::originator/enterpriseName)");
    $applicEl = $domXpath->evaluate("//identAndStatusSection/descendant::applic")[0];
    $applic = $CSDBObject->getApplicability($applicEl);
    $brexElement = $domXpath->evaluate("//identAndStatusSection/descendant::brexDmRef/dmRef/dmRefIdent")[0];
    $brexDmRef = CSDBStatic::resolve_dmIdent($brexElement);
    $QA = $domXpath->evaluate("//identAndStatusSection/descendant::qualityAssurance/*[last()]")[0];
    $QAtext = $CSDBObject->getQA(null, $QA);
    $remarks = $CSDBObject->getRemarks($domXpath->evaluate("//identAndStatusSection/descendant::remarks")[0]);

    $arr = [
      "filename" => $filename,
      "modelIdentCode" => $decode_ident['dmCode']['modelIdentCode'],
      "systemDiffCode" => $decode_ident['dmCode']['systemDiffCode'],
      "systemCode" => $decode_ident['dmCode']['systemCode'],
      "subSystemCode" => $decode_ident['dmCode']['subSystemCode'],
      "subSubSystemCode" => $decode_ident['dmCode']['subSubSystemCode'],
      "assycode" => $decode_ident['dmCode']['assyCode'],
      "disassyCode" => $decode_ident['dmCode']['disassyCode'],
      "disassyCodeVariant" => $decode_ident['dmCode']['disassyCodeVariant'],
      "infoCode" => $decode_ident['dmCode']['infoCode'],
      "infoCodeVariant" => $decode_ident['dmCode']['infoCodeVariant'],
      "itemLocationCOde" => $decode_ident['dmCode']['itemLocationCode'],
      
      "languageIsoCode" => $decode_ident['language']['languageIsoCode'],
      "countryIsoCode" => $decode_ident['language']['countryIsoCode'],
      
      "issueNumber" => $decode_ident['issueInfo']['issueNumber'],
      "inWork" => $decode_ident['issueInfo']['inWork'],

      "year" => $issueDate->getAttribute('year'),
      "month" => $issueDate->getAttribute('month'),
      "day" => $issueDate->getAttribute('day'),

      "techName" => $techName->nodeValue,
      "infoName" => $infoName ? $infoName->nodeValue : '',
      "infoNameVariant" => $infoNameVariant ? $infoNameVariant->nodeValue : '',

      'securityClassification' => $sc,
      'responsiblePartnerCompany' => $rsp,
      'originator' => $originator,
      'applicability' => $applic,
      'brexDmRef' => $brexDmRef,
      'qa' => $QAtext,
      'remarks' => $remarks
    ];

    // $dmc = fn() => self::create($arr);
    // dispatch($dmc)->delay(now()->addMinutes(5));

    // dispatch(fn() => Dmc::create($arr))->delay(now()->addMinutes(1));
    // dispatch(fn() => self::create($arr));
    // dispatch(self::create($arr));
    // dispatch(self::create($arr))->delay(now()->addMinutes(1));
    // dispatch(Dmc::create($arr))->delay(now()->addMinutes(5));
    // self::create($arr);
    // dispatch(Dmc::create($arr))->delay(now()->addMinutes(5));


    $dmc = self::where('filename', $filename)->first();
    if($dmc) {
      $dmc->update($arr);
      return $dmc->save();
    }
    return self::create($arr);
  }
}
