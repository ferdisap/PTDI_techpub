<?php

namespace App\Http\Controllers;

use App\Models\Brdp;
use Illuminate\Http\Request;
use App\Http\Controllers\BrdpTes;
use App\Models\Csdb;
use DOMDocument;
use DOMXPath;
use Ptdi\Mpub\CSDB as MpubCSDB;
use SimpleXMLElement;
use XMLParser;
use Illuminate\Support\Facades\Response;

class BrdpController extends Controller
{
  ######## new by VUE ########
  /**
   * tujuannya sama dengan fungsi table, yaitu menampilkan list number of brdp
   */
  public function app()
  {
    return view('brdp/app');
  }

  public function transform(Request $request)
  {
    $projectName = $request->get('project_name') ?? $request->route('project_name');
    $filename = $request->get('filename') ?? $request->route('filename');
    if(!$projectName OR !$filename) return $this->ret(400, ['Project name or object filename must be true provided.']);

    $csdb_model = Csdb::where('filename', $filename)->first(['path']);
    $csdb_dom = MpubCSDB::importDocument(storage_path("app/{$csdb_model->path}/"),$filename);
    
    $object = new Csdb();
    $object->DOMDocument = $csdb_dom;
    $transformed = $object->transform_to_xml(resource_path("views/brdp/xsl/"), 'brdp.xsl');
    
    if($error = MpubCSDB::get_errors(false)){
      return $this->ret(400, [$error]);
    }

    return Response::make($transformed,200,['Content-Type' => 'text/html']);
  }

  public function transformBrPara(Request $request)
  {
    $projectName = $request->route('project_name');
    $filename = $request->route('filename');
    $brParaId = $request->route('brParaId');

    $csdb_model = Csdb::where('filename', $filename)->first(['path']);
    $csdb_dom = MpubCSDB::importDocument(storage_path("app/{$csdb_model->path}/"),$filename);

    $domXpath = new \DOMXPath($csdb_dom);
    $brPara = $domXpath->evaluate("//brPara[@id = '{$brParaId}']");
    if($brPara->length < 0){
      return $this->ret(400, ['No such brPara']);
    }    
    $csdb_dom->documentElement->replaceWith($brPara[0]);
    
    $object = new Csdb();
    $object->DOMDocument = $csdb_dom;
    $transformed = $object->transform_to_xml(resource_path("views/brdp/xsl/"), 'detail.xsl');

    if($error = MpubCSDB::get_errors(false)){
      return $this->ret(400, [$error]);
    }

    return Response::make($transformed,200,['Content-Type' => 'text/html']);
  }

  public function search(Request $request)
  {
    $projectName = $request->route('project_name');
    $filename = $request->route('filename');
    $applSchema = $applSchema = $request->get('applSchema'); // "brexXsd,crewXsd,descriptXsd"
    
    $csdb_model = Csdb::where('filename', $filename)->first(['path']);
    $csdb_dom = MpubCSDB::importDocument(storage_path("app/{$csdb_model->path}/"),$filename);

    // 0. lakukan validasi XSI dan BREX disini

    // 1. prepare filterby
    $filterby = array_filter($request->all(), (fn($key) => in_array($key[0],[0,1,2,3,4,5,9])),ARRAY_FILTER_USE_KEY);
    ksort($filterby);

    // 2. change value filterBy into xpath
    $get_xpath = function ($text, $filter) use($applSchema) {

      //tambahkan disini jika pakai filter schema. 
      if($applSchema){
        $applSchema = preg_replace("/(\w+)+,{0,1}/",".//@$1='1' and ",$applSchema);
        $applSchema = rtrim($applSchema, " and "); // di akhir string ada " and " dan harus dihapus

        $all = "//*[contains(.,'{$text}')]/ancestor::brPara[{$applSchema}] | //@*[contains(.,'{$text}')]/ancestor::brPara[{$applSchema}]";
      } else {
        $all = "//*[contains(.,'{$text}')]/ancestor::brPara | //@*[contains(.,'{$text}')]/ancestor::brPara";
      }

      $xpath_collection = [
        'ident' => "(//@brDecisionPointUniqueIdent[contains(.,'{$text}')]/ancestor::brPara)",
        'title' => "(//brDecisionPointContent/title[contains(.,'{$text}')]/ancestor::brPara)",
        'category' => "//@brCategoryNumber[contains(.,'{$text}')]/ancestor::brPara | //brCategory[contains(.,'{$text}')]/ancestor::brPara",
        'decision' => "//brDecision[contains(.,'{$text}')]/ancestor::brPara | //brDecision/@brDecisionIdentNumber[contains(.,'{$text}')]/ancestor::brPara",
        'audit' => "//brAudit[contains(.,'{$text}')]/ancestor::brPara | //brCurrentStatus[@brStatus = '{$text}']/ancestor::brPara",
        'all' => $all,
      ];
      return $xpath_collection[$filter];
    };
    foreach ($filterby as $key => $value) {
      $k = preg_replace('/[0-9]+/','',$key);
      $values = explode(';', $value);
      $subXpath = [];
      foreach($values as $text){
        $text = trim($text);
        $xpath = $get_xpath($text, $k);
        $subXpath[] = $xpath;
      }
      $filterby[$key] = join(' | ', $subXpath);
    }

    $DomXpath = new \DOMXPath($csdb_dom);

    // 3. menggabungkan semua brLevelledPara menjadi satu brLevelledPara
    $all_brLevelledPara = $DomXpath->evaluate("//brLevelledPara");
    for ($i=$all_brLevelledPara->length-1; $i <= 1 ; $i++) { 
      $brLevelledPara = $all_brLevelledPara[$i];
      while ($brLevelledPara->hasChildNodes()) {
        $brLevelledPara->parentNode->appendChild($brLevelledPara->firstChild);
      }
      $brLevelledPara->remove();
    }
    $brLevelledPara = $all_brLevelledPara[0];
    unset($all_brLevelledPara);

    // 4. evaluase berdasarkan xpath filterby
    foreach($filterby as $xpath){
      $res = $DomXpath->evaluate($xpath);
      if($res->length > 0){
        while ($brLevelledPara->hasChildNodes()) {
          $brLevelledPara->removeChild($brLevelledPara->firstChild);
        }
        foreach ($res as $key => $brPara) {
          $brLevelledPara->appendChild($brPara);
        }
      } 
    }

    // 5. mendapatkan nama brPara@brDecisionPointUniqueIdent nya atau brPara@Id dan di return
    $brParaId = [];
    $brPara = $DomXpath->evaluate("//brPara/@id");
    foreach($brPara as $br){
      $brParaId[] = $br->nodeValue;
    }
    return Response::make($brParaId, 200, ['Content-Type' => 'application/json']);
  }















  ######## old by Blade ########
  // use Brdp;
  public function indexBrdp()
  {
    if (request()->utility == 'getfile'){
      return $this->getFile(request()->path, request()->ct);
    } else {
      return view('brdp/brdp_index', [
        'title' => 'BRDP Index'
      ]);
    }
  }

  public function table($aircraft)
  {
    // $lists = $this->getList();

    return view('brdp/brdp_' . $aircraft .'_table', [
      'title' => 'brdp ' . $aircraft,
      // 'lists' => $this->brdpListToArray($lists)
    ]);
  }

  // private function getFile($path){
  //   return response(file_get_contents($path, FILE_USE_INCLUDE_PATH), 200, [
  //     'Content-Type' => 'application/xml'
  //   ]);
  // }

  // private function brdpListToArray($xml_list = null)
  // {
  //   $xml_doc = new DOMDocument();
  //   $xml_doc->loadXML($xml_list);
    
  //   dd($xml_doc);
  //   $lists = $xml_doc->getElementsByTagName('list');
  //   $a = [];
  //   foreach ($lists as $li) {
  //     // dd($li->attributes->item(3));
  //     $a[$li->getAttribute('no')] = [
  //       'id' => $li->getAttribute('id'),
  //       'tr_onclick' => $li->getAttribute('tr_onclick'),
  //       'td_ident_onclick' => $li->getAttribute('td_ident_onclick'),
  //       'ident' => simplexml_import_dom($li->getElementsByTagName('ident')->item(0)->firstChild)->asXML(),
  //       'title' => $li->getElementsByTagName('title')->item(0)->textContent,
  //       'category' => $li->getElementsByTagName('category')->item(0)->textContent,
  //       'audit' => $li->getElementsByTagName('audit')->item(0)->textContent,
  //       'decision' => $li->getElementsByTagName('decision')->item(0)->textContent,
  //     ];
  //   }
  //   return $a;
  // }
}
