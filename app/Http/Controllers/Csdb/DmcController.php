<?php

namespace App\Http\Controllers\Csdb;

use App\Http\Controllers\Controller;
use DOMDocument;
use Illuminate\Http\Request;
use XSLTProcessor;
use Illuminate\Support\Str;
use \Spipu\Html2Pdf\Html2Pdf;

class DmcController extends Controller
{
  public function indexDMC()
  {
   return view('csdb/dmc/dmc_index', [
      'title' => 'DMC Index',
    ]); 
  }

  public function transform($aircraft)
  {
    return view('csdb/dmc/transform', [
      'title' => "DMC Transform",
      'aircraft' => $aircraft
    ]);
  }

  public function transforming(Request $request, $aircraft)
  {
    if($request->outputType == 'pdf'){
      $dmc = new DOMDocument();
      $dmc->loadXML($request->xmlstring);
      
      $xsl = new DOMDocument();
      $xsl->load(base_path()."/ietp_n219/view/pm/default_pdf.xsl");
  
      $xsltproc = new XSLTProcessor();
      $xsltproc->importStylesheet($xsl);
      $xsltproc->setParameter('', 'csdb_path', 'D:\Kerja\PT Dirgantara Indonesia (Persero)\KFX\S1000D\S1000D_5.0\01.PTDI_techpub\ietp_n219');
  
      $result = $xsltproc->transformToXml($dmc);
      
      // include dirname(__FILE__).'/res/example00.php';
      // ob_start();
      // include base_path(). DIRECTORY_SEPARATOR. 'public' . DIRECTORY_SEPARATOR. 'examples'. DIRECTORY_SEPARATOR. 'res'. DIRECTORY_SEPARATOR .'example00.php';
      // $content = ob_get_clean();
      
      $content = $result;
      // $html2pdf = new Html2Pdf('P', 'A4', 'fr');
      $html2pdf = new Html2Pdf('P', 'A5', 'en', false, 'UTF-8', array(20,5,5,5));
      $html2pdf->writeHTML($content);
  
      $filename = Str::random(10).'.pdf';
      $URI = base_path().DIRECTORY_SEPARATOR. "ietp_{$aircraft}". DIRECTORY_SEPARATOR. "dump". DIRECTORY_SEPARATOR. $filename;
      $html2pdf->output($URI, 'F');
      return response()->json([
        'url' => $request->getSchemeAndHttpHost()."/requestFile/n219/{$filename}?dataType=dump",
      ],200);
    }

    elseif($request->outputType == 'html'){
      $dmc = new DOMDocument();
      $dmc->loadXML($request->xmlstring);

      // transforming
      $xsl = new DOMDocument();
      $xsl->load(base_path()."/ietp_n219/view/pm/default_web.xsl");

      $xsltproc = new XSLTProcessor();
      $xsltproc->importStylesheet($xsl);
      $filename = Str::random(10).".xml";
      $relativePath = "/ietp_{$aircraft}/dump/{$filename}";

      $result = $xsltproc->transformToUri($dmc, base_path().$relativePath);
      if($result){
        return response()->json([
          'url' => $request->getSchemeAndHttpHost()."/requestFile/n219/{$filename}?dataType=dump",
        ],200);
      } else {
        return response('',500);
      }
    }

    // if($request->xmlstring){
    //   try {
    //     // create DOM
    //     $dmc = new DOMDocument();
    //     $dmc->loadXML($request->xmlstring);

    //     // transforming
    //     $xsl = new DOMDocument();
    //     $xsl->load(base_path()."/ietp_n219/view/pm/default_web.xsl");

    //     $xsltproc = new XSLTProcessor();
    //     $xsltproc->importStylesheet($xsl);
    //     $filename = Str::random(10).".xml";
    //     $relativePath = "/ietp_{$aircraft}/dump/{$filename}";

    //     $result = $xsltproc->transformToUri($dmc, base_path().$relativePath);
    //     if($result){
    //       return response()->json([
    //         'url' => $request->getSchemeAndHttpHost()."/requestFile/n219/{$filename}?dataType=dump",
    //       ],200);
    //     } else {
    //       return response('',500);
    //     }
    //   } catch (\Throwable $th) {
    //     return response('',500);
    //   }
    //   return response('',500);      
    // }
  }
}
