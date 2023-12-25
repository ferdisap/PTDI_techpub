<?php

namespace App\Http\Controllers;

use App\Models\Csdb;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Ptdi\Mpub\CSDB as MpubCSDB;
use Ptdi\Mpub\ICNDocument;
use Ptdi\Mpub\Pdf2\Applicability;
use Ptdi\Mpub\Pdf2\Fonts;
use Ptdi\Mpub\Pdf2\PMC_PDF;
use Symfony\Component\HttpFoundation\StreamedResponse;
use XSLTProcessor;
use ZipStream\ZipStream;

use function Ptdi\Mpub\Pdf2\font_path;
use function Tes\tes;

class CsdbServiceController extends CsdbController
{
  use Applicability;

  ############### NEW by VUE ###############
  public function provide_csdb_transform2(Request $request)
  {
    $projectName = $request->get('projectName') ?? $request->route('projectName');
    $filename = $request->get('filename') ?? $request->route('filename');
    if(!$projectName OR !$filename) return $this->ret(400, ['Project name or object filename must be true provided.']);

    $csdb_model = Csdb::where('filename', $filename)->first(['path']);
    $csdb_dom = MpubCSDB::importDocument(storage_path("app/{$csdb_model->path}/"),$filename);
    
    // jika ICN Document
    if($csdb_dom instanceof ICNDocument){
      // saat ini belum bisa baca file 3D (step,igs,stl,etc)karena mime nya tidak dikenal
      $mime = $csdb_dom->getFileinfo()['mime_type'];
      $file = $csdb_dom->getFile();
      return Response::make($file, 200, ['Content-Type' => $mime]);
    }
    // $type = $csdb_dom->firstElementChild->tagName;

    $object = new Csdb();
    $object->DOMDocument = $csdb_dom;
    $object->repoName = $projectName;
    $object->objectpath = "/api/csdb";
    $transformed = $object->transform_to_xml(resource_path("views/ietm/xsl/"));
    
    if($error = MpubCSDB::get_errors(false)){
      return $this->ret(400, [$error]);
    }

    return Response::make($transformed,200,['Content-Type' => 'text/html']);

    dd($transformed);

    // $xsl = MpubCSDB::importDocument(resource_path("views/ietm/xsl/"), "{$type}.xsl");
    // dd($xsl);

    $utility = $request->get('utility');
    $xsl = MpubCSDB::importDocument(resource_path("views/csdb/{$utility}/"), "{$type}.xsl");
    $xsltproc = new XSLTProcessor;
    $xsltproc->importStylesheet($xsl);
    $xsltproc->registerPHPFunctions((fn() => array_map(fn($name) => MpubCSDB::class."::$name", get_class_methods(MpubCSDB::class)))());
    $xsltproc->registerPHPFunctions([CsdbServiceController::class."::getLastPositionCrewDrillStep", CsdbServiceController::class."::setLastPositionCrewDrillStep"]);
    
    $xsltproc->registerPHPFunctions();
    $xsltproc->setParameter('','filename', $filename);
    // $xsltproc->setParameter('','applicability', $appl);
    $xsltproc->setParameter('','absolute_path_csdbInput', storage_path("app/{$csdb_model->path}/"));
    $xsltproc->setParameter('','dmOwner', preg_replace("/.xml/",'',$filename));
    $transformed = $xsltproc->transformToDoc($csdb_dom);
    $transformed = str_replace('#ln;', "<br/>", $transformed->C14N());
    return Response::make($transformed,200,['Content-Type' => 'text/html']);
  }
  public function provide_csdb_pdf(Request $request)
  {
    $filename = $request->get('filename') ?? $request->route('filename');
    $csdb_model = Csdb::where('filename', $filename)->first(['path']);
    $csdb_dom = MpubCSDB::importDocument(storage_path("app/{$csdb_model->path}/"), $filename);
    
    $schema = MpubCSDB::getSchemaUsed($csdb_dom, 'filename');
    if(in_array($schema, ['crew.xsd', 'comrep.xsd', 'descript.xsd', 'frontmatter.xsd'])){
      return $this->transform_pdf_dmodule($request, storage_path("app/{$csdb_model->path}"), [$filename]);
    }
    elseif($schema == 'pm.xsd'){
      $modelIdentCode = $csdb_dom->getElementsByTagName('pmCode')[0]->getAttribute('modelIdentCode');
      return $this->transform_pdf_pm($request, $modelIdentCode, storage_path("app/{$csdb_model->path}"), $filename,);
    }
    else{
      abort(500);
    }
  }



  ############### OLD by blade ###############
  /**
   * helper function untuk crew.xsl
   * ini tidak bisa di pindah karena bukan static method
   * * sepertinya bisa dijadikan static, sehingga fungsinya lebih baik ditaruh di CsdbModel saja
   */
  public function setLastPositionCrewDrillStep(int $num)
  {
    $this->lastPositionCrewDrillStep = $num;
  }

  /**
   * helper function untuk crew.xsl
   * ini tidak bisa di pindah karena bukan static method
   * sepertinya bisa dijadikan static, sehingga fungsinya lebih baik ditaruh di CsdbModel saja
   */
  public function getLastPositionCrewDrillStep()
  {
    return $this->lastPositionCrewDrillStep ?? 0;
  }

  private function provide_csdb_zip($obj = [])
  {
    // $zip = new ZipStream(
    //   outputName: 'example.zip',
    // );
    // # add content here to zip based on the csdb object.
    // $zip->finish();
    // return new StreamedResponse(fn() => $zip,200);
  }

  public function provide_csdb_transform(Request $request)
  {
    $filename = $request->filename;
    $csdb_model = Csdb::where('filename', $filename)->first(['path']);
    $csdb_dom = MpubCSDB::importDocument(storage_path("app/{$csdb_model->path}/"),$filename);
    if($csdb_dom instanceof ICNDocument){
      // saat ini belum bisa baca file 3D (step,igs,stl,etc)karena mime nya tidak dikenal
      $mime = $csdb_dom->getFileinfo()['mime_type'];
      $file = $csdb_dom->getFile();
      return Response::make($file, 200, ['Content-Type' => $mime]);
    }
    if(!Auth::check()){
      return redirect(route('login'));
    }
    $type = $csdb_dom->firstElementChild->tagName;

    $utility = $request->get('utility');

    $xsl = MpubCSDB::importDocument(resource_path("views/csdb/{$utility}/"), "{$type}.xsl");
    $xsltproc = new XSLTProcessor;
    $xsltproc->importStylesheet($xsl);
    $xsltproc->registerPHPFunctions((fn() => array_map(fn($name) => MpubCSDB::class."::$name", get_class_methods(MpubCSDB::class)))());
    $xsltproc->registerPHPFunctions([CsdbServiceController::class."::getLastPositionCrewDrillStep", CsdbServiceController::class."::setLastPositionCrewDrillStep"]);
    
    $xsltproc->registerPHPFunctions();
    $xsltproc->setParameter('','filename', $filename);
    // $xsltproc->setParameter('','applicability', $appl);
    $xsltproc->setParameter('','absolute_path_csdbInput', storage_path("app/{$csdb_model->path}/"));
    $xsltproc->setParameter('','dmOwner', preg_replace("/.xml/",'',$filename));
    $transformed = $xsltproc->transformToDoc($csdb_dom);
    $transformed = str_replace('#ln;', "<br/>", $transformed->C14N());
    return Response::make($transformed,200,['Content-Type' => 'text/html']);
  }

  public function provide_csdb_xsl(Request $request)
  {
    if(!($filename = $request->get('filename'))){
      abort(400, 'filename is required');
    }

    $xsl = Controller::searchFile(resource_path("views/csdb"), $filename);
    $txt = file_get_contents(resource_path("views/csdb").DIRECTORY_SEPARATOR.$xsl);
    $mime = mime_content_type(resource_path("views/csdb").DIRECTORY_SEPARATOR.$xsl);

    return Response::make($txt, 200, ['Content-Type' => $mime]);
  }

  public function provide_csdb_js(Request $request)
  {
    if(!($filename = $request->get('filename'))){
      abort(400, 'filename is required');
    }

    $file = file_get_contents(resource_path("js/csdb/{$filename}"));
    $r = Response::make($file,200,['Content-Type' => 'application/javascript']);
    return $r;

  }

  /**
   * required: filename,
   * optional: mime
   */
  public function provide_csdb_object(Request $request)
  {
    // check filename
    if(!($filename = $request->get('filename')) OR !($csdb_object = Csdb::where('filename',$filename)->first(['filename','path']))){
      abort(400, 'filename is required');
    }
    $file = Storage::get("{$csdb_object->path}/{$csdb_object->filename}");
    $mime = $request->get('mime') ?? Storage::mimeType("{$csdb_object->path}/{$csdb_object->filename}");
    $r = Response::make($file,200,['Content-Type' => $mime]);
    return $r;
  }

  /**
   * sementara ini belum dipakai
   */
  public function CSDB(Request $request)
  {
    $functions = explode(",",$request->get('functions'));
    $filename = $request->get('filename');
    $mime = $request->get('mime');

    $csdb_model = Csdb::where('filename', $filename)->first(['id', 'path']);
    $xmlDoc = MpubCSDB::importDocument(storage_path("app/{$csdb_model->path}/{$filename}"));

    $res = [];
    foreach ($functions as $function) {
      if($mime == 'text/xml'){
        $params = [];
        $functionAlias = $function;
        switch ($function) {
          case 'title':
            $type = $xmlDoc->firstElementChild->tagName;
            $function = "resolve_{$type}Title";
            $params[] = $xmlDoc->getElementsByTagName("{$type}Title")[0];
          case 'resolve_issueDate':
            $params[] = $xmlDoc->getElementsByTagName('issueDate')[0];
            break;
          case 'resolve_issueType':
            $params[] = $xmlDoc->getElementsByTagName('issueType')[0];
            break;
          case 'resolve_responsibleParnerCompany':
            $params[] = $xmlDoc->getElementsByTagName('responsiblePartnerCompany')[0];
            $params[] = 'both';
            break;
          case 'resolve_originator':
            $params[] = $xmlDoc->getElementsByTagName('originator')[0];
            $params[] = 'both';
            $function = 'resolve_responsibleParnerCompany';
            break;
          case 'getApplicability':
            $params[] = $xmlDoc;
            $params[] = __DIR__."/".$csdb_model->path;
            break;
          case "resolve_brexDmRef":
            $params[] = $xmlDoc->getElementsByTagName('brexDmRef')[0]->getElementsByTagName('dmRefIdent')[0];
            $function = 'resolve_dmIdent';
            break;
          case "resolve_qualityAssurance":
            $function = 'getStatus';
            $params[] = ['qualityAssurance'];
            $params[] = $xmlDoc;
        }
        $res[$functionAlias] = call_user_func_array(MpubCSDB::class."::{$function}",$params);
        $params = [];
      }
    }

    return response()->json(['return' => $res],200);
  }

  public function provide_csdb_export(Request $request)
  {
    if($request->get('type') == 'pdf'){
      $pmEntryType = '';
      $filename = $request->get('filename');
      $csdb_model = Csdb::where('filename', $filename)->first(['path']);
      $csdb_dom = MpubCSDB::importDocument(storage_path("app/{$csdb_model->path}/"), $filename);
  
      $schema = MpubCSDB::getSchemaUsed($csdb_dom, 'filename');
      if(in_array($schema, ['crew.xsd', 'comrep.xsd', 'descript.xsd', 'frontmatter.xsd'])){
        return $this->transform_pdf_dmodule($request, storage_path("app/{$csdb_model->path}"), [$filename], '' ,$pmEntryType);
      }
      elseif($schema == 'pm.xsd'){
        // if(!$request->get('pmType')){
        //   $request->replace(array_merge($request->all(), ['pmType' => $csdb_dom->documentElement->getAttribute('pmType')]));
        // }
        $modelIdentCode = $csdb_dom->getElementsByTagName('pmCode')[0]->getAttribute('modelIdentCode');
        return $this->transform_pdf_pm($request, $modelIdentCode, storage_path("app/{$csdb_model->path}"), $filename, '' ,$pmEntryType);
      }
      else{
        abort(500);
      }
    }
    elseif($request->get('type') == 'package'){
      
    }
  }

  private function transform_pdf_pm(Request $request, $modelIdentCode ,$absolute_path, string $filename, $pmType = 'pt99')
  {
    $modelIdentCode = strtolower($modelIdentCode);    
    $pmc = PMC_PDF::instance($absolute_path,$modelIdentCode);
    $pmc->setAA_Approved("DGCA approved", " DD MMM YYYY");
    $pmc->importDocument($absolute_path."/", $filename,'');
    $params = [];
    if($request->get('pmType')){
      $params['pmType'] = $request->get('pmType');
      $pmc::$static_pmType_config = [];
    }
    if(!empty($params)){
      $pmc->setConfig($pmc->getDOMDocument(), $params);
    }
    
    try {
      $pmc->render();
    } catch (\Throwable $th) {
      $err = MpubCSDB::get_errors();
      foreach($err as &$v){
        $v = array_merge($v, $v);
      }
      array_unshift($v, 'check the available object required by the pmc.');
      // return back()->withInput()->with(['result' => 'fail'])->withErrors($v, 'info');
      return abort(400,join(", ", $v)); // erro. tidak perlu back agar kalau pake vue bisa
    }
    dd('aaa');
    $pmc->getPDF();
  }
  private function transform_pdf_dmodule(Request $request, $absolute_path, $filenames = [])
  {
    $pmc = new PMC_PDF($absolute_path);
    $pmc->importDocument_dump([
      'pmType' => $request->pmType,
      'pmEntryType' => $request->pmEntryType,
      'objectRef' => $filenames,
      'use_DMC_modelIdentCode' => true,
    ]);
    $pmc->render();
    $pmc->getPDF();
  }
}
