<?php

namespace App\Models;

use App\Models\Csdb as ModelsCsdb;
use DOMDocument;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Ptdi\Mpub\CSDB;
use Ptdi\Mpub\Helper;

use function PHPUnit\Framework\directoryExists;

class Dml extends ModelsCsdb
{
  use HasFactory;

  protected $with = ['initiator'];

  public function tes()
  {
    return 'foo';
  }

  /**
   * fungsi ini akan merecord di sql
   */
  public function create_xml(string $modelIdentCode, string $originator, string $dmlType, string $securityClassification, string $brexDmRef, array $remarks = [], array $otherOptions = [])
  {
    $identAndStatusSection = $this->create_identAndStatusSection($modelIdentCode, $originator, $dmlType, $securityClassification, $brexDmRef, $remarks, $otherOptions);

    $dom = new \DOMDocument('1.0', 'UTF-8');
    $dml = $dom->createElement('dml');
    $dml->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:noNamespaceSchemaLocation', './dml.xsd');
    $dom->appendChild($dml);

    $identAndStatusSection = $dom->importNode($identAndStatusSection->documentElement, true);
    $dml->appendChild($identAndStatusSection);

    $dmlContent = $dom->createElement('dmlContent');
    $dom->documentElement->appendChild($dmlContent);
    $dom->saveXML();

    $filename = CSDB::resolve_DocIdent($dom);
    $this->filename = $filename;
    $this->path = "csdb";
    $this->editable = 1;
    $this->initiator_id = Auth::user()->id;
    if ($this->direct_save) {
      if ($dom->C14NFile(storage_path("csdb/{$filename}"))) {
        $this->save();
      }
    }
    $this->DOMDocument = $dom;
    return $this;
  }

  /**
   * untuk update identAndStatusSection
   */
  public function updateIdentAndStatusSection($data = [])
  {
    $domXpath = new \DOMXpath($this->DOMDocument);
    foreach ($data as $key => $value) {
      if ($key == 'securityClassification') {
        $security = $domXpath->evaluate("//identAndStatusSection/dmlStatus/security")[0];
        $security->setAttribute('securityClassification', str_pad((int)$value, 2, '0', STR_PAD_LEFT));
      } elseif ($key == 'brexDmRef') {
        $new_dmRef_string = Helper::decode_dmIdent($value)['xml_string'];
        $new_dmRef = new \DOMDocument();
        $new_dmRef->loadXML($new_dmRef_string);
        $new_dmRef = $this->DOMDocument->importNode($new_dmRef->documentElement, true);

        $old_dmRef = $domXpath->evaluate("//identAndStatusSection/dmlStatus/brexDmRef/dmRef")[0];
        $old_dmRef->replaceWith($new_dmRef);
      } elseif ($key == 'remarks') {
        $paras = preg_split("/[\r\n]+/", $value);
        if (!empty($paras)) {
          $old_remarks = $domXpath->evaluate("//identAndStatusSection/dmlStatus/remarks")[0];
          $new_remarks = $this->DOMDocument->createElement('remarks');
          foreach ($paras as $para) {
            $simplePara = $this->DOMDocument->createElement('simplePara');
            $simplePara->nodeValue = $para;
            $new_remarks->appendChild($simplePara);
          }
          $old_remarks->replaceWith($new_remarks);
        }
      }
      $filename = CSDB::resolve_DocIdent($this->DOMDocument);
      if ($this->direct_save) {
        if ($this->DOMDocument->C14NFile(storage_path("csdb/{$filename}"))) {
          $this->save();
          return true;
        }
      }
    }
  }

  /**
   * @param Array $data berasal dari Helper::decode...;
   * @return string ancestor::dmlEntry element
   */
  public static function generate_xpath_for_dmlEntry_checking($data = [], $codeType, $useIssueInfo = false, $useLanguage = false)
  {
    if ($codeType == 'infoEntityIdent') {
      $infoEntityRefIdent = $data['prefix'] . join("-", $data[$codeType]) . $data['extension'];
      return "//infoEntityRef[@infoEntityRefIdent='{$infoEntityRefIdent}']/ancestor::dmlEntry";
    }
    $check = [];
    $code = $data[$codeType];
    array_walk($code, function ($value, $name) use (&$check) {
      if ($value != '') {
        $check[] = "@{$name} = '{$value}'";
      }
    });
    $check = join(" and ", $check);
    // $xpath = "//dmlEntry/descendant::dmCode[$check]";
    $xpath = "//dmlEntry/descendant::{$codeType}[$check]"; // eg.: //dmlEntry/descendant::dmCode[@modelIdentCode]
    // $xpath = "//dmlEntry/descendant::dmCode[$check]/ancestor::dmlEntry";

    if ($useIssueInfo and isset($data['issueInfo'])) {
      $inWork = $data['issueInfo']['inWork'];
      $issueNumber = $data['issueInfo']['issueNumber'];
      $xpath .= "/ancestor::dmlEntry/issueInfo[@inWork = '{$inWork}' and @issueNumber = '{$issueNumber}']";
    }
    if ($useLanguage and isset($data['language'])) {
      $countryIsoCode = $data['language']['countryIsoCode'];
      $languageIsoCode = $data['language']['languageIsoCode'];
      $xpath .= "/ancestor::dmlEntry/language[@countryIsoCode = '{$countryIsoCode}' and @languageIsoCode = '{$languageIsoCode}']";
    }
    // return $xpath;
    return $xpath . "/ancestor::dmlEntry";
  }

  /**
   * element security belum bisa mengcover @commercialSecurityAttGroup dan @derivativeClassificationRefId
   * ada fitur check dmlEntry di setiap DML yang tersimpan, tapi hanya yang 'p' saja karena 's' itu adalah CSL yang digenerate setiap ada load/unload object ke CSDB
   * fitur ini tidak digunakan untuk check dmlEntry yang pakai issueType/dmlEntryType. Jika mau ubah dari 'new' ke 'changed', maka pakai fungsi cloneDmlEntry()
   * @param Array $responsiblePartnerCompany; #0:enterpriseName, #1:enterpriseCode
   * @return Array index#0 = result boolean
   */
  // public function add_dmlEntry(string $issueType = '', string $entryIdent, string $securityClassification = '', array $responsiblePartnerCompany = ['', ''], $remarks = [])
  public function add_dmlEntry(string $entryIdent, string $securityClassification = null, array $responsiblePartnerCompany = ['', ''], $remarks = [], $otherOptions = [])
  {
    $dml_dom = $this->DOMDocument ?? CSDB::importDocument(storage_path($this->path), $this->filename);
    $this->DOMDocument = $dml_dom;
    $domxpath = new \DOMXPath($dml_dom);

    // #1. validasi dmlType
    $dmlType = $domxpath->evaluate("string(//dmlAddress/descendant::dmlCode/@dmlType)");
    if ($dmlType == 'c') return [false, "Only the DML Type with 'p' or 's' can be add entry."];

    $dmlContent = $domxpath->evaluate("//dmlContent")[0];

    // #2. decode string filename entry into array
    $ident = Helper::decode_ident($entryIdent);
    if (!$ident) return [false, "{$entryIdent} cannot be decoded."];

    // #3. checking if duplicate dmlEntry (hanya dmltype='p' saja, tapi tidak dicheck jika entrynya ICN)
    // $xpath = function ($data, $codeType, $useIssueInfo = false ,$useLanguage = false) {};    
    $check_to_alldmls = function () use ($ident, $entryIdent) {
      $codeType = array_keys($ident)[0]; // output eg.: 'dmCode', 'pmCode', etc
      $modelIdentCode = $ident[$codeType]['modelIdentCode'];
      $alldmls = ModelsCsdb::where('filename', 'not like', $this->filename)->where('filename', 'like', "DML-{$modelIdentCode}-%_%")->get(); // get all dmls which only code same in modelIdentCode;
      // $xpath = $xpath($ident, $codeType);
      $xpath = self::generate_xpath_for_dmlEntry_checking($ident, $codeType);
      // $results = [];
      foreach ($alldmls as $dml) {
        $dom = CSDB::importDocument(storage_path($dml->path), $dml->filename);
        $domxpath = new \DOMXPath($dom);
        // $res = $domxpath->evaluate($xpath); // expect output: DOMElement dmCode/pmCode/dmlCode
        $res = $domxpath->evaluate($xpath); // expect output: DOMElement dmlEntry
        if ($res->length > 0) {
          return [false, "Entry Ident {$entryIdent} has been already listed in {$dml->filename}"];
        }
        // foreach ($res as $r) {
        //   $results[] = $r;
        // }
        // if (count($results) > 0) {
        //   $rs = array_map(function ($element) {
        //     $docIdent = CSDB::resolve_DocIdent($element->ownerDocument);
        //     return $docIdent;
        //   }, $results);
        //   $rs = join(", ", $rs);
        //   return [false, "Entry Ident {$entryIdent} has been already listed in {$rs}"];
        // }
      }
      return [true];
    };
    $check_to_currentdml = function () use ($ident, $entryIdent, $dml_dom) {
      $codeType = array_keys($ident)[0]; // output eg.: 'dmCode', 'pmCode', etc
      // $xpath = $xpath($ident, $codeType, true, true);
      $xpath = self::generate_xpath_for_dmlEntry_checking($ident, $codeType, true, true);
      // $dml_dom = CSDB::importDocument(storage_path($this->path), $this->filename);
      $domxpath = new \DOMXPath($dml_dom);
      $res = $domxpath->evaluate($xpath); // expect output: DOMElement dmlEntry
      if ($res->length > 0) {
        return [false, "The {$entryIdent} is cannot added because already exist in this DML. If you want to change the issue type of the entry, go to edit DML."];
      }
      return [true];
    };
    if ($dmlType == 'p' and ($ident['prefix'] != 'ICN-') and !($check = $check_to_alldmls())[0]) {
      return [false, $check[1]];
    } elseif ($dmlType == 's' and !($check = $check_to_currentdml())[0]) {
      return [false, $check[1]];
    }

    // #4. create dmlEntry element 
    if ($dmlType == 'c' or $dmlType == 'p') {
      $ident['xml_string'] = preg_replace('/<(language|issueInfo)[\w\d\s="]+\/>/m', '', $ident['xml_string']);
    }
    if ($issueType = $otherOptions['issueType'] ?? '') {
      $issueType = ' issueType=' . '"' . $issueType . '"';
    }
    if ($dmlEntryType = $otherOptions['dmlEntryType'] ?? '') {
      $dmlEntryType = ' dmlEntryType=' . '"' . $dmlEntryType . '"';
    }
    $dmlEntry_string = <<<EOL
    <dmlEntry{$issueType}{$dmlEntryType}>
    {$ident['xml_string']}
    </dmlEntry>
    EOL;
    $dmlEntry_string = preg_replace('/\s{2,}|\n/m', '', $dmlEntry_string);
    $dmlEntry = new DOMDocument('1.0', 'UTF-8');
    $dmlEntry->loadXML($dmlEntry_string);
    $dmlEntry = $dml_dom->importNode($dmlEntry->documentElement, true);

    // #5. add securityClassification
    if ($securityClassification) {
      $security = $dml_dom->createElement('security');
      $security->setAttribute('securityClassification', str_pad((int)$securityClassification, 2, '0', STR_PAD_LEFT));
      // selanjutnya tambah commercialSecurityAttGroup
      // selanjutnya tambah derivativeClassificationRefId
      $dmlEntry->appendChild($security);
    }

    // validasi enterpriseName dan create responsiblePartnerCompany
    if (!$responsiblePartnerCompany[0]) return [false, "{$entryIdent} must have enterprise name as responsible partner company."];
    $rspc = $dml_dom->createElement('responsiblePartnerCompany');
    $enterpriseName = $dml_dom->createElement('enterpriseName');
    $enterpriseName->nodeValue = $responsiblePartnerCompany[0];
    if ($responsiblePartnerCompany[1]) $rspc->setAttribute('enterpriseCode', $responsiblePartnerCompany[1]);
    $rspc->appendChild($enterpriseName);
    $dmlEntry->appendChild($rspc);


    // #6. tambahkan element answer disini

    // #7. tambahkan element remarks disini
    $rmks = $dml_dom->createElement('remarks');
    foreach ($remarks as $text) {
      $smp = preg_split("/[\r\n]+/", $text);
      foreach ($smp as $txt) {
        $simplePara = $dml_dom->createElement('simplePara');
        $simplePara->nodeValue = $txt;
        $rmks->appendChild($simplePara);
      }
    }
    if ($rmks->firstElementChild) {
      $dmlEntry->appendChild($rmks);
    }

    // #5. Append dmlEntry to dmlContent
    $dmlContent->appendChild($dmlEntry);
    $dml_dom->saveXML();

    // #5. save file storage
    if ($this->direct_save) {
      $dml_dom->C14NFile(storage_path($this->path) . "/" . $this->filename);
    }
    return [true, $dml_dom];
  }

  /**
   * tidak support seqNumber yang ada letter nya 
   * element security belum bisa mengcover @commercialSecurityAttGroup dan @derivativeClassificationRefId
   * jika ingin menaruh <dmlRef> pada <dmlStatus>, maka gunakan otherOptions = ['dmlRef' = ['DML...', 'DML...]];
   * 
   */
  private function create_identAndStatusSection(string $modelIdentCode, string $originator, string $dmlType, string $securityClassification, string $brexDmRef, array $remarks = [], $otherOptions = [])
  {
    // $year = '2023';
    $year = date('Y');
    $dmlCode = [strtolower($dmlType) == 's' ? 'CSL' : 'DML', $modelIdentCode, $originator, $dmlType, $year, ''];
    $dmlCode = strtoupper(join('-', $dmlCode)); // DML-MALE-0001Z-P-2024-
    $seqNumber = function ($path) use ($dmlCode) {
      // $dir = array_diff(scandir($path));
      $dir = scandir($path);
      $collection = [];
      foreach ($dir as $file) {
        if (str_contains($file, $dmlCode)) {
          $collection[] = $file;
        }
      }
      $c = array_map(function ($v) {
        $v = preg_replace("/_.+/", '', $v); // menghilangkan issueInfo dan languange yang menempel di filename
        $v = explode("-", $v);
        return $v;
      }, $collection);
      if (!empty($c)) {
        $max_seqNumber = $c[0][5];
        foreach ($c as $dmlCode_array) {
          if ((int)$max_seqNumber < (int)$dmlCode_array[5]) {
            $max_seqNumber = $dmlCode_array[5];
          }
        }
        $max_seqNumber = str_pad(((int)$max_seqNumber) + 1, 5, '0', STR_PAD_LEFT);
      }
      return $max_seqNumber ?? str_pad(1, 5, '0', STR_PAD_LEFT);
      // inWork number pasti 01 jika buat BARU DML
      // $c = array_map(function($v){
      //   $v = preg_replace("/DML-[\w-]+_/", '',$v);
      //   $v = preg_replace("/.xml/", '',$v);
      //   $v = explode("-",$v);
      //   return $v;
      // }, $collection);
      // $iw_max = str_pad(max($iw) + 1, 2, '0', STR_PAD_LEFT);
      // if(!empty($c)){
      //   $iw = array_map((fn($v) => (int)($v[1])), $c);
      //   $iw_max = str_pad(max($iw) + 1, 2, '0', STR_PAD_LEFT);
      // }
      // return [$max_seqNumber ?? '00001', $iw_max ?? '01'];
    };
    $modelIdentCode = strtoupper($modelIdentCode);
    $originator = strtoupper($originator);
    $dmlType = strtolower($dmlType);
    // $search = $search(storage_path("app/csdb/" . strtoupper($modelIdentCode)));
    $seqNumber = $seqNumber(storage_path("csdb"));
    $inWork = '01';
    $day = date('d');
    $month = date('m');

    $getBrexDmRefIdent = function ($brexDmRef) {
      $brexDmRef = strtoupper($brexDmRef);
      $brexDmRef = preg_replace('/.XML|DMC-/', '', $brexDmRef);
      $brexDmRefIdent_array = explode('_', $brexDmRef);
      $dmCode = $brexDmRefIdent_array[0];
      $issueInfo = $brexDmRefIdent_array[1];
      $language = $brexDmRefIdent_array[2];

      $dmCode_array = explode('-', $dmCode);
      $issueInfo_array = explode('-', $issueInfo);
      $language_array = explode('-', $language);

      $ret = [
        "modelIdentCode" => $dmCode_array[0],
        "systemDiffCode" => $dmCode_array[1],
        "systemCode" => $dmCode_array[2],
        "subSystemCode" => $dmCode_array[3][0],
        "subSubSystemCode" => $dmCode_array[3][1],
        "assyCode" => $dmCode_array[4],
        "disassyCode" => substr($dmCode_array[5], 0, 2),
        "disassyCodeVariant" => substr($dmCode_array[5], 2),
        "infoCode" => substr($dmCode_array[6], 0, 3),
        "infoCodeVariant" => substr($dmCode_array[6], 3),
        "itemLocationCode" => $dmCode_array[7],
      ];
      if (isset($dmCode_array[8])) {
        $ret['learnCode'] = strtoupper(substr($dmCode_array[8], 0, 3));
        $ret['learnEventCode'] = strtoupper(substr($dmCode_array[8], 4));
      } else {
        $ret['learnCode'] = '';
        $ret['learnEventCode'] = '';
      }

      $ret['issueNumber'] = $issueInfo_array[0];
      $ret['inWork'] = $issueInfo_array[1];

      $ret['languageIsoCode'] = strtolower($language_array[0]);
      $ret['countryIsoCode'] = $language_array[1];

      return $ret;
    };

    $brexDmRef = $getBrexDmRefIdent($brexDmRef);

    $remarks = array_map((fn ($v) => "<simplePara>{$v}</simplePara>"), $remarks);
    $remarks = join("", $remarks);
    $remarks = (empty($remarks)) ? '' :
      <<<EOD
    <remarks>{$remarks}</remarks>
    EOD;

    $learnCode = ($brexDmRef['learnCode'] == '') ? '' : 'learnCode=' . '"' . $brexDmRef['learnCode'] . '"';
    $learnEventCode = ($brexDmRef['learnEventCode'] == '') ? '' : 'learnEventCode=' . '"' . $brexDmRef['learnEventCode'] . '"';

    $dmlRef = '';
    if (isset($otherOptions['dmlRef']) and is_array($otherOptions['dmlRef'])) {
      $dmlRef = array_map(function ($filename) {
        $filename = Helper::decode_dmlIdent($filename);
        return $filename = $filename['xml_string'];
      }, $otherOptions['dmlRef']);
      $dmlRef = join("", $dmlRef);
    }

    $identAndStatusSection = <<<EOL
      <identAndStatusSection>
        <dmlAddress>
          <dmlIdent>
            <dmlCode dmlType="{$dmlType}" modelIdentCode="{$modelIdentCode}" senderIdent="{$originator}" seqNumber="{$seqNumber}" yearOfDataIssue="{$year}"></dmlCode>
            <issueInfo inWork="{$inWork}" issueNumber="000"></issueInfo>
          </dmlIdent>
          <dmlAddressItems>
            <issueDate day="{$day}" month="{$month}" year="{$year}"></issueDate>
          </dmlAddressItems>
        </dmlAddress>
        <dmlStatus>
          <security securityClassification="{$securityClassification}"></security>
          {$dmlRef}
          <brexDmRef>
            <dmRef>
              <dmRefIdent>
                <dmCode assyCode="{$brexDmRef['assyCode']}" disassyCode="{$brexDmRef['disassyCode']}" disassyCodeVariant="{$brexDmRef['disassyCodeVariant']}" infoCode="{$brexDmRef['infoCode']}" infoCodeVariant="{$brexDmRef['infoCodeVariant']}" itemLocationCode="{$brexDmRef['itemLocationCode']}" modelIdentCode="{$brexDmRef['modelIdentCode']}" subSubSystemCode="{$brexDmRef['subSubSystemCode']}" subSystemCode="{$brexDmRef['subSystemCode']}" systemCode="{$brexDmRef['systemCode']}" systemDiffCode="{$brexDmRef['systemDiffCode']}" 
                  {$learnCode} {$learnEventCode}/>
                <issueInfo inWork="{$brexDmRef['inWork']}" issueNumber="{$brexDmRef['issueNumber']}"/>
                <language countryIsoCode="{$brexDmRef['countryIsoCode']}" languageIsoCode="{$brexDmRef['languageIsoCode']}"/>
              </dmRefIdent>
            </dmRef>
          </brexDmRef>
          {$remarks}
        </dmlStatus>
      </identAndStatusSection>
    EOL;

    $this->direct_save = false;
    $this->setRemarks('securityClassification', $securityClassification);

    $dom = new \DOMDocument();
    $identAndStatusSection = preg_replace("/\n\s+/m", '', $identAndStatusSection);
    $dom->loadXML(trim($identAndStatusSection));
    return $dom;
  }
}
