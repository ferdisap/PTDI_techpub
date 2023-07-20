<?php

namespace App\Models;

use DOMDocument;
use DOMXPath;
use XSLTProcessor;

trait Brdp
{
  private $cache = null;
  private $last_update = null;
  // private $xml_string;
  // private $xsl_string;

  public function get_xml_string($path = null, )
  {
    if($this->cache != null){
      if($this->last_update != null){
        //update dulu cache nya
      }
      // set $xml_string by cache
    }
    else {
      $xml_string = file_get_contents($path, FILE_USE_INCLUDE_PATH);
      // $cached = $xml_string;
      // $update = now();
    }
    // dd($xml_string);
    return $xml_string;
  }

  public function getList()
  {
    $xml_string = $this->get_xml_string('brdp/dmodule/br/tes.xml');
    $xml_doc = new DOMDocument();
    $xml_doc->loadXML($xml_string);

    // $xpath = new DOMXPath($xml_doc);

    // $collection = $xml_doc->getElementsByTagName('brDecisionText')->item(0);
    // $query = "//brPara";
    // $entries = $xpath->evaluate($query, $collection);
    // dd($entries->item(0));

    $xsl_string = $this->get_xml_string('brdp/style/php/brList.xsl');
    $xsl_doc = new DOMDocument();
    $xsl_doc->loadXML($xsl_string);

    $proc = new XSLTProcessor();
    $proc->importStylesheet($xsl_doc);

    $result = $proc->transformToDoc($xml_doc);
    return $result->saveXML();
  }

  
}
