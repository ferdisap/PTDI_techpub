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

  public function tes()
  {
    return 'foo';
  }
  
  public function create_xml(string $modelIdentCode, string $originator, string $dmlType, string $securityClassification, string $brexDmRef, array $remarks = [],$extension = 'xml')
  {
    $identAndStatusSection = $this->create_string_identAndStatusSection($modelIdentCode, $originator, $dmlType, $securityClassification, $brexDmRef, $remarks, $extension);
    
    $dom = new \DOMDocument('1.0','UTF-8');
    $dml = $dom->createElement('dml');
    // kalo mau save, tambahkan attribute xsi. Ini tidak ditambahkan karena jika ingin di validasi, harus ada document URI;
    $dml->setAttribute('xmlns:xsi',"http://www.w3.org/2001/XMLSchema-instance");
    $dml->setAttribute('xsi:noNamespaceSchemaLocation', './dml.xsd');
    $dom->appendChild($dml);
    
    $identAndStatusSection = $dom->importNode($identAndStatusSection->documentElement, true);
    $dml->appendChild($identAndStatusSection);
    
    $dmlContent = $dom->createElement('dmlContent');
    $dom->documentElement->appendChild($dmlContent);
    $dom->saveXML();

    $filename = CSDB::resolve_DocIdent($dom);
    $project_name = $modelIdentCode;
    $save = $dom->C14NFile(storage_path("app/csdb/{$project_name}/{$filename}"));
    if($save){
      $csdb = new parent();
      // $fillable = ['filename', 'path', 'status', 'description', 'initiator_id', 'project_name', 'remarks'];
      $csdb->filename = $filename;
      $csdb->path = "csdb/{$project_name}";
      $csdb->status = 'new';
      $csdb->description = '';
      $csdb->initiator_id = Auth::user()->id;
      $csdb->project_name = $project_name;
      $csdb->save();
    }
    return $csdb;
  }

  /**
   * element security belum bisa mengcover @commercialSecurityAttGroup dan @derivativeClassificationRefId
   * @param Array $responsiblePartnerCompany; #0:enterpriseName, #1:enterpriseCode
   * @return Array index#1 = result boolean
   */
  public function add_dmlEntry(string $issueType, string $entryIdent, string $securityClassification = '', array $responsiblePartnerCompany = ['', ''], $remarks = [])
  {
    
    $dom = CSDB::importDocument(storage_path("app/{$this->path}"), $this->filename);
    $domxpath = new \DOMXPath($dom);

    // #1. validasi dmlType
    $dmlType = $domxpath->evaluate("string(//dmlAddress/descendant::dmlCode/@dmlType)");
    if($dmlType == 'c') return [false, "Only the DML Type with 'p' or 's' can be add entry."];

    $dmlContent = $domxpath->evaluate("//dmlContent")[0];

    // #2. decode string filename entry into array
    $ident = Helper::decode_ident($entryIdent);
    if(!$ident) return [false, "{$entryIdent} cannot be decoded."];

    // #3. checking if duplicate dmlEntry
    $check = function($data){
      $check = [];
      array_walk($data, function($value, $name) use(&$check){
        if($value != ''){
          $check[] = "@{$name} = '{$value}'";
        }
      });
      $check = join(" and ", $check);
      $xpath = "//dmlEntry/descendant::dmCode[$check]";
      return $xpath;
    };
    $xpath = $check($ident['dmCode']);
    $check = $domxpath->evaluate($xpath);
    if($dmlType == 's' AND $check->length > 0){
      $xpath_issueInfo = "/issueInfo[@inWork = '{$ident['issueInfo']['inWork']}' AND @issueNumber='{$ident['issueInfo']['issueNumber']}']";
      $xpath_language = "/language[@countryIsoCode = '{$ident['language']['countryIsoCode']}' AND @languageIsoCode='{$ident['language']['languageIsoCode']}']";
      foreach($check as $dmCode){
        $check_issueInfo = $domxpath->evaluate($xpath_issueInfo, $dmCode->parentNode);
        $check_language = $domxpath->evaluate($xpath_language, $dmCode->parentNode);
        if($check_issueInfo->length > 0 OR $check_language->length > 0){
          return [false, "Entry Ident {$entryIdent} has been already listed in DML."];
        }
      }
    } else {
      if($check->length > 0) return [false, "Entry Ident {$entryIdent} has been already listed in DML"];
    }

    // #4. create dmlEntry element 
    if($dmlType == 'c' OR $dmlType == 'p'){
      $ident['xml_string'] = preg_replace('/<(language|issueInfo)[\w\d\s="]+\/>/m','',$ident['xml_string']);
    }
    $dmlEntry_string = <<<EOL
    <dmlEntry issueType="{$issueType}">
    {$ident['xml_string']}
    </dmlEntry>\n
    EOL;
    $dmlEntry_string = preg_replace('/\s{2,}|\n/m','',$dmlEntry_string);
    $dmlEntry = new DOMDocument('1.0','UTF-8');
    $dmlEntry->loadXML($dmlEntry_string);
    $dmlEntry = $dom->importNode($dmlEntry->documentElement, true);

    // #5. add securityClassification
    if($securityClassification){
      $security = $dom->createElement('security');
      $security->setAttribute('securityClassification', $securityClassification);
      // selanjutnya tambah commercialSecurityAttGroup
      // selanjutnya tambah derivativeClassificationRefId
      $dmlEntry->appendChild($security);
    }

    $rspc = $dom->createElement('responsiblePartnerCompany');
    $enterpriseName = $dom->createElement('enterpriseName');
    $enterpriseName->nodeValue = $responsiblePartnerCompany[0];
    if($responsiblePartnerCompany[1]) $rspc->setAttribute('enterpriseCode', $responsiblePartnerCompany[1]);
    $rspc->appendChild($enterpriseName);
    $dmlEntry->appendChild($rspc);


    // #6. tambahkan element answer disini

    // #7. tambahkan element remarks disini
    $rmks = $dom->createElement('remarks');
    foreach($remarks as $text){
      $simplePara = $dom->createElement('simplePara');
      $simplePara->nodeValue = $text;
      $rmks->appendChild($simplePara);
    }
    $dmlEntry->appendChild($rmks);

    // #5. Append dmlEntry to dmlContent
    $dmlContent->appendChild($dmlEntry);
    $dom->saveXML();

    // #5. save file storage
    $dom->C14NFile(storage_path("app/{$this->path}")."/".$this->filename);

    return [true, $dom];
  }

  /**
   * tidak support seqNumber yang ada letter nya 
   */
  private function create_string_identAndStatusSection(string $modelIdentCode, string $originator, string $dmlType, string $securityClassification, string $brexDmRef, array $remarks = [],$extension = 'xml')
  {
    // $year = '2023';
    $year = date('Y');
    $dmlCode = ['DML', $modelIdentCode, $originator, $dmlType, $year, ''];
    $dmlCode = strtoupper(join('-', $dmlCode)); // DML-MALE-0001Z-P-2024-
    $search = function($path) use($dmlCode) {
      $dir = array_diff(scandir($path));
      $collection = [];
      foreach($dir as $file){
        if(str_contains($file, $dmlCode)){
          $collection[] = $file;
        }
      }
      $c = array_map(function($v){
        $v = preg_replace("/_.+/", '',$v); // menghilangkan issueInfo dan languange yang menempel di filename
        $v = explode("-",$v);
        return $v;
      }, $collection);
      if(!empty($c)){
        $max_seqNumber = $c[0][5];
        foreach ($c as $dmlCode_array) {
          if((int)$max_seqNumber < (int)$dmlCode_array[5]){
            $max_seqNumber = $dmlCode_array[5];
          }
        }
        $max_seqNumber = str_pad(((int)$max_seqNumber) + 1, 5, '0', STR_PAD_LEFT);
      }

      $c = array_map(function($v){
        $v = preg_replace("/DML-[\w-]+_/", '',$v);
        $v = preg_replace("/.xml/", '',$v);
        $v = explode("-",$v);
        return $v;
      }, $collection);

      if(!empty($c)){
        $iw = array_map((fn($v) => (int)($v[1])), $c);
        $iw_max = str_pad(max($iw) + 1, 2, '0', STR_PAD_LEFT);
      }

      return [$max_seqNumber ?? '00001', $iw_max ?? '01'];
    };
    $modelIdentCode = strtoupper($modelIdentCode);
    $originator = strtoupper($originator);
    $dmlType = strtolower($dmlType);
    $search = $search(storage_path("app/csdb/" . strtoupper($modelIdentCode)));
    $seqNumber = $search[0];
    $inWork = $search[1];
    $day = date('d');
    $month = date('m');

    $getBrexDmRefIdent = function($brexDmRef){
      $brexDmRef = preg_replace('/.xml|DMC-/','',$brexDmRef);
      $brexDmRefIdent_array = explode('_',$brexDmRef);
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
        "disassyCode" => substr($dmCode_array[5],0,2),
        "disassyCodeVariant" => substr($dmCode_array[5],2),
        "infoCode" => substr($dmCode_array[6],0,3),
        "infoCodeVariant" => substr($dmCode_array[6],3),
        "itemLocationCode" => $dmCode_array[7],
      ];
      if(isset($dmCode_array[8])){
        $ret['learnCode'] = strtoupper(substr($dmCode_array[8],0,3));
        $ret['learnEventCode'] = strtoupper(substr($dmCode_array[8],4));
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

    $brexDmRef = $getBrexDmRefIdent(strtoupper($brexDmRef)); 

    $remarks = array_map((fn($v) => "<simplePara>{$v}</simplePara>"),$remarks);
    $remarks = join("",$remarks);

    $learnCode = ($brexDmRef['learnCode'] == '') ? '' : 'learnCode=' . '"' . $brexDmRef['learnCode'] . '"';
    $learnEventCode = ($brexDmRef['learnEventCode'] == '') ? '' : 'learnEventCode=' . '"' . $brexDmRef['learnEventCode'] . '"';

    $identAndStatusSection = <<<EOL
      <identAndStatusSection>
        <dmlAddress>
          <dmlIdent>
            <dmlCode dmlType="{$dmlType}" modelIdentCode="{$modelIdentCode}" senderIdent="{$originator}" seqNumber="{$seqNumber}" yearOfDataIssue="{$year}"></dmlCode>
            <issueInfo inWork="{$inWork}" issueNumber="001"></issueInfo>
          </dmlIdent>
          <dmlAddressItems>
            <issueDate day="{$day}" month="{$month}" year="{$year}"></issueDate>
          </dmlAddressItems>
        </dmlAddress>
        <dmlStatus>
          <security securityClassification="{$securityClassification}"></security>
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
          <remarks>
            {$remarks}
          </remarks>
        </dmlStatus>
      </identAndStatusSection>
    EOL;

    $dom = new \DOMDocument();
    $dom->loadXML($identAndStatusSection);
    return $dom;
  }
}
