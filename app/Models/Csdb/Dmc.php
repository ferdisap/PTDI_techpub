<?php

namespace App\Models\Csdb;

use App\Jobs\Csdb\DmcTableFiller;
use App\Models\Csdb;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ptdi\Mpub\Main\CSDBObject;
use Ptdi\Mpub\Main\CSDBStatic;

class Dmc extends Csdb
{
  use HasFactory;

  protected $fillable = [
    'filename', 

    'modelIdentCode',
    'systemDiffCode',
    'systemCode',
    'subSystemCode',
    'subSubSystemCode',
    'assyCode',
    'disassyCode',
    'disassyCodeVariant',
    'infoCode',
    'infoCodeVariant',
    'itemLocationCode',
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
    $domXpath = new \DOMXpath($CSDBObject->document);

    $modelIdentCode = $domXpath->evaluate("string(//dmAddress/dmIdent/dmCode/@modelIdentCode)");
    $systemDiffCode = $domXpath->evaluate("string(//dmAddress/dmIdent/dmCode/@systemDiffCode)");
    $systemCode = $domXpath->evaluate("string(//dmAddress/dmIdent/dmCode/@systemCode)");
    $subSystemCode = $domXpath->evaluate("string(//dmAddress/dmIdent/dmCode/@subSystemCode)");
    $subSubSystemCode = $domXpath->evaluate("string(//dmAddress/dmIdent/dmCode/@subSubSystemCode)");
    $assyCode = $domXpath->evaluate("string(//dmAddress/dmIdent/dmCode/@assyCode)");
    $disassyCode = $domXpath->evaluate("string(//dmAddress/dmIdent/dmCode/@disassyCode)");
    $disassyCodeVariant = $domXpath->evaluate("string(//dmAddress/dmIdent/dmCode/@disassyCodeVariant)");
    $infoCode = $domXpath->evaluate("string(//dmAddress/dmIdent/dmCode/@infoCode)");
    $infoCodeVariant = $domXpath->evaluate("string(//dmAddress/dmIdent/dmCode/@infoCodeVariant)");
    $itemLocationCode = $domXpath->evaluate("string(//dmAddress/dmIdent/dmCode/@itemLocationCode)");
    $languageIsoCode = $domXpath->evaluate("string(//dmAddress/dmIdent/language/@languageIsoCode)");
    $countryIsoCode = $domXpath->evaluate("string(//dmAddress/dmIdent/language/@countryIsoCode)");

    $issueNumber = $domXpath->evaluate("string(//dmAddress/dmIdent/issueInfo/@issueNumber)");
    $inWork = $domXpath->evaluate("string(//dmAddress/dmIdent/issueInfo/@inWork)");

    $year = $domXpath->evaluate("string(//dmAddress/dmAddressItems/issueDate/@year)");
    $month = $domXpath->evaluate("string(//dmAddress/dmAddressItems/issueDate/@month)");
    $day = $domXpath->evaluate("string(//dmAddress/dmAddressItems/issueDate/@day)");

    $techName = $domXpath->evaluate("string(//dmAddress/dmAddressItems/dmTitle/techName)");
    $infoName = $domXpath->evaluate("string(//dmAddress/dmAddressItems/dmTitle/infoName)");
    $infoNameVariant = $domXpath->evaluate("string(//dmAddress/dmAddressItems/dmTitle/infoNameVariant)");

    $securityClassification = $domXpath->evaluate("string(//dmStatus/security/@securityClassification)");
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
      "systemDiffCode" => $systemDiffCode,
      "systemCode" => $systemCode,
      "subSystemCode" => $subSystemCode,
      "subSubSystemCode" => $subSubSystemCode,
      "assyCode" => $assyCode,
      'disassyCode' => $disassyCode,
      'disassyCodeVariant' => $disassyCodeVariant,
      'infoCode' => $infoCode,
      'infoCodeVariant' => $infoCodeVariant,
      'itemLocationCode' => $itemLocationCode,
      'languageIsoCode' => $languageIsoCode,
      'countryIsoCode' => $countryIsoCode,
      
      'issueNumber' => $issueNumber,
      'inWork' => $inWork,

      'year' => $year,
      'month' => $month,
      'day' => $day,

      "techName" => $techName,
      "infoName" => $infoName,
      "infoNameVariant" => $infoNameVariant,

      'securityClassification' => $securityClassification,
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

  // public static function instanceModel()
  // {
  //   $self = new self();
  //   $self->setProtected([
  //     'table' => $self->getProtected('table') ?? [],
  //     'fillable' => $self->getProtected('fillable') ?? [],
  //     'casts' => $self->getProtected('casts') ?? [],
  //     'attributes' => $self->getProtected('attributes') ?? [],
  //     'timestamps' => $self->getProtected('timestamps') ?? false,
  //   ]);
  //   return $self;
  // }
}
