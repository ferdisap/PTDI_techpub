<?php

namespace App\Http\Controllers;

use App\Models\Brdp;
use Illuminate\Http\Request;
use App\Http\Controllers\BrdpTes;
use DOMDocument;
use SimpleXMLElement;
use XMLParser;

class BrdpController extends Controller
{
  use Brdp;
  public function index()
  {
    if (request()->utility == 'getfile'){
      return $this->getFile(request()->path);
    } else {
      return view('brdp/brdp_index', [
        'title' => 'BRDP Index'
      ]);
    }
  }

  public function detail($aircraft)
  {
    $lists = $this->getList();

    return view('brdp/brdp_' . $aircraft .'_table', [
      'title' => 'brdp ' . $aircraft,
      'lists' => $this->brdpListToArray($lists)
    ]);
  }

  private function getFile($path){
    return response(file_get_contents($path, FILE_USE_INCLUDE_PATH), 200, [
      'Content-Type' => 'application/xml'
    ]);
  }

  private function brdpListToArray($xml_list = null)
  {
    $xml_doc = new DOMDocument();
    $xml_doc->loadXML($xml_list);

    $lists = $xml_doc->getElementsByTagName('list');
    $a = [];
    foreach ($lists as $li) {
      // dd($li->attributes->item(3));
      $a[$li->getAttribute('no')] = [
        'id' => $li->getAttribute('id'),
        'tr_onclick' => $li->getAttribute('tr_onclick'),
        'td_ident_onclick' => $li->getAttribute('td_ident_onclick'),
        'ident' => simplexml_import_dom($li->getElementsByTagName('ident')->item(0)->firstChild)->asXML(),
        'title' => $li->getElementsByTagName('title')->item(0)->textContent,
        'category' => $li->getElementsByTagName('category')->item(0)->textContent,
        'audit' => $li->getElementsByTagName('audit')->item(0)->textContent,
        'decision' => $li->getElementsByTagName('decision')->item(0)->textContent,
      ];
    }
    return $a;
  }
}
