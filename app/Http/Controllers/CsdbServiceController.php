<?php

namespace App\Http\Controllers;

use App\Models\Csdb;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Vite;
use Illuminate\View\Compilers\BladeCompiler;
use PrettyXml\Formatter;
use Ptdi\Mpub\CSDB as MpubCSDB;
use Ptdi\Mpub\Fop\Fop;
use Ptdi\Mpub\Helper;
use Ptdi\Mpub\ICNDocument;
use Ptdi\Mpub\Main\CSDBError;
use Ptdi\Mpub\Main\CSDBObject;
use Ptdi\Mpub\Main\CSDBStatic;
use Ptdi\Mpub\Main\CSDBValidator;
use Ptdi\Mpub\Main\Helper as MainHelper;
use Ptdi\Mpub\Main\XSIValidator;
use Ptdi\Mpub\Pdf2\Applicability;
use Ptdi\Mpub\Pdf2\Fonts;
use Ptdi\Mpub\Pdf2\PMC_PDF;
use Symfony\Component\HttpFoundation\StreamedResponse;
use XSLTProcessor;
use ZipStream\ZipStream;
use Closure;

use function PHPUnit\Framework\callback;
use function Ptdi\Mpub\Pdf2\font_path;
use function Tes\tes;

class CsdbServiceController extends CsdbController
{
  use Applicability;

  ############### NEW for csdb4 ###############
  public function get_object_path(Request $request)
  {
    $model = Csdb::where('filename', $request->filename)->first();
    if($model){
      return $model->path;
    }
  }

  /**
   * @return array
   */
  public function change_object_path(Request $request, Csdb $csdb = null)
  {
    $path = $request->path;
    $re = '/[^a-zA-Z1-9\/\s]+/';
    preg_match($re, $path, $matches, PREG_OFFSET_CAPTURE, 0);
    if(!empty($matches)) return [false, 'Path must match to "/[^a-zA-Z1-9\/\s]+/"'];
    if(!$csdb AND $request->filename){
      $csdb = Csdb::where('filename', $request->filename)->first();
    } elseif($csdb){
      $csdb->path = $path;
      $csdb->save();
      return [true];
    } else return [false];
    // $model = null;
    // $request->merge(['filename' => $filename]);
    // $request->validate([
    //   'path' => 'required',
    //   'filename' => [function(string $attribute, mixed $value,  Closure $fail) use(&$model){
    //     $model = Csdb::where('filename', $value)->first();
    //     if(!$model) $fail("No such $value available in CSDB.");
    //   }]
    // ]);
    
    // if($model AND $model instanceof Csdb){
    //   $model->path = $request->path;
    //   if($model->save()){
    //     return $this->ret2(200, ['data' => $model], ["Path $filename has been updated."]);
    //   }
    // }
    // return $this->ret2(400, ["Failed to update option."]);
  }

  public function get_transformed_identstatus(Request $request, string $filename)
  {
    // $dom = MpubCSDB::importDocument(storage_path('csdb'), 'DMC-MALE-A-00-00-00-00A-001A-A_000-02_EN-EN.xml');
    // $dom = MpubCSDB::importDocument(storage_path('csdb'), 'DMC-MALE-A-00-00-00-00A-002A-A_000-01_EN-EN.xml');
    // $dom = MpubCSDB::importDocument(storage_path('csdb'), 'DMC-MALE-A-00-00-00-00A-003A-A_000-01_EN-EN.xml');
    // $dom = MpubCSDB::importDocument(storage_path('csdb'), 'DMC-MALE-A-00-00-00-00A-003B-A_000-01_EN-EN.xml');
    // $dom = MpubCSDB::importDocument(storage_path('csdb'), 'DMC-MALE-A-15-00-01-00A-018A-A_000-01_EN-EN.xml');
    // $dom = MpubCSDB::importDocument(storage_path('csdb'), 'DMC-MALE-A-15-20-01-00A-043A-A_000-01_EN-EN.xml');
    // $dom = MpubCSDB::importDocument(storage_path('csdb'), 'DMC-MALE-A-15-20-02-00A-034A-A_000-01_EN-EN.xml');
    // $dom = MpubCSDB::importDocument(storage_path('csdb'), 'DMC-MALE-A-15-30-01-00A-141A-A_000-01_EN-EN.xml');
    // $dom = MpubCSDB::importDocument(storage_path('csdb'), 'DMC-MALE-A-15-30-02-00A-141A-A_000-01_EN-EN.xml');
    // $dom = MpubCSDB::importDocument(storage_path('csdb'), 'DMC-MALE-A-15-30-03-00A-141A-A_000-01_EN-EN.xml');
    // $dom = MpubCSDB::importDocument(storage_path('csdb'), 'DMC-MALE-A-15-30-04-00A-141A-A_000-01_EN-EN.xml');
    // $dom = MpubCSDB::importDocument(storage_path('csdb'), 'DMC-MALE-A-15-30-05-00A-141A-A_000-01_EN-EN.xml');
    // $dom = MpubCSDB::importDocument(storage_path('csdb'), 'DMC-MALE-A-15-30-06-00A-141A-A_000-01_EN-EN.xml');
    // $dom = MpubCSDB::importDocument(storage_path('csdb'), 'DMC-MALE-A-15-30-07-00A-028A-A_000-01_EN-EN.xml');
    // $dom = MpubCSDB::importDocument(storage_path('csdb'), 'DMC-MALE-A-00-00-00-00A-00QA-D_000-01_EN-EN.xml');
    $model = Csdb::where('filename', $filename)->first();
    $model->CSDBObject->load(storage_path("csdb/$model->filename"));
    $model->CSDBObject->setConfigXML(CSDB_VIEW_PATH . DIRECTORY_SEPARATOR . "xsl" . DIRECTORY_SEPARATOR . "Config.xml"); // nanti diubah mungkin berbeda antara pdf dan html meskupun harusnya SAMA. Nanti ConfigXML mungkin tidak diperlukan jika fitur BREX sudah siap sepenuhnya.
    $transformed = $model->CSDBObject->transform_to_xml(CSDB_VIEW_PATH."/xsl/Container.xsl", ["configuration" => 'ForIdentStatusVue']);
    
    if($error = CSDBError::getErrors(false) AND (int)$request->get('ignoreError')){
      return $this->ret2(200, [$error], ['transformed' => $transformed, 'mime' => 'text/html']); // ini yang dipakai vue
    }
    return $this->ret2(200, null, ['transformed' => $transformed, 'mime' => 'text/html']); // ini yang dipakai vue
  }

  /**
   * DEPRECIATED, diganti oleh Csdb/CsdbController@read_html_object
   */
  public function get_transformed_contentpreview(Request $request, Csdb $csdb)
  {
    $csdb->CSDBObject->load(storage_path("csdb/$csdb->filename"));

    // $modelIdentCode = MainHelper::get_attribute_from_filename($csdb->filename, 'modelIdentCode');  
    $modelIdentCode = 'CN235';
    $pathxsl = $this->getPathXSL('html', $modelIdentCode);
    if(!$pathxsl) return Response::make('', 200, ['Content-Type' => 'application/html']);

    $transformed = $csdb->CSDBObject->transform_to_xml(CSDB_VIEW_PATH."/xsl/html/Main.xsl", [
      "configuration" => 'ContentPreview',
      'csrf_token' => csrf_token(),
      // 'Expires' => now()->add('day', 1),
      // 'Last-Modified' => $csdb->updated_at
    ]);

    if($error = CSDBError::getErrors(false) AND (int)$request->get('ignoreError')){
      return $this->ret2(200, [$error], ['transformed' => $transformed, 'mime' => 'text/html']); // ini yang dipakai vue
    }
    $transformed = Blade::render($transformed);
    // return $this->ret2(200, null, ['transformed' => $transformed, 'mime' => 'text/html']); // ini yang dipakai vue
    return Response::make($transformed,200,['content-type' => 'text/html']);
  }

  private function getPathXSL(string $type, string $productName = '', string $xpathString = '') : string
  {
    $config = new \DOMDocument();
    $config->validateOnParse = true;
    @$config->load(CSDB_VIEW_PATH."/xsl/Config.xml");

    $xpath = new \DOMXPath($config);
    if(!$xpathString) $xpathString = "string(//config/output/method[@type='$type']/path[@product-name='$productName' or @product-name='*'])";
    $path = $xpath->evaluate($xpathString);
    return !empty($path) ? (CSDB_VIEW_PATH."/xsl/$path") : '';
  }

  public function get_pdf_object(Request $request, Csdb $csdb)
  {
    if(!$csdb) abort(400);
    // $modelIdentCode = MainHelper::get_attribute_from_filename($csdb->filename, 'modelIdentCode');  
    $modelIdentCode = 'CN235';
    $pathxsl = $this->getPathXSL('pdf', $modelIdentCode);
    if(!$pathxsl) return Response::make('', 200, ['Content-Type' => 'application/pdf']);

    $model = Csdb::where('filename', $csdb->filename)->first();
    // $model->CSDBObject->load(storage_path("csdb/$model->filename"));
    $model->CSDBObject->load(storage_path("csdb/{$request->user()->storage}/$model->filename"));
    $model->CSDBObject->setConfigXML(CSDB_VIEW_PATH . DIRECTORY_SEPARATOR . "xsl" . DIRECTORY_SEPARATOR . "Config.xml"); // nanti diubah mungkin berbeda antara pdf dan html meskupun harusnya SAMA. Nanti ConfigXML mungkin tidak diperlukan jika fitur BREX sudah siap sepenuhnya.
    // $model->showCGMArkElement();

    CSDBStatic::$footnotePositionStore[$model->filename] = [];    

    $transformed = $model->CSDBObject->transform_to_xml( $pathxsl, [
      "filename" => $model->filename,
      // "alertPathBackground" => "file:///".str_replace("\\","/",storage_path('csdb')),
      "alertPathBackground" => "file:///".str_replace("\\","/", CSDB_VIEW_PATH."/xsl/pdf/assets"),
    ]);

    $fo = $this->getPathXSL('','',"string(//method[@type='pdf']/pathCache)")."/".$csdb->filename.".fo";
    file_put_contents($fo, $transformed);
    if($pdf = Fop::FO_to_PDF($fo)){
      return Response::make($pdf,200,[
        'Content-Type' => 'application/pdf', 
        'Cache-Control' => 'public',
        'Expires' => now()->add('day', 1),
        'Last-Modified' => $csdb->updated_at
      ]);
    } else {
      return abort(400);
    }

    $fo = storage_path('examples/fo/helloworld.fo');
    if($pdf = Fop::FO_to_PDF($fo)){
      return Response::make($pdf,200,[
        'Content-Type' => 'application/pdf', 
        'Cache-Control' => 'public',
        'Expires' => now()->add('day', 1),
        'Last-Modified' => $csdb->updated_at
      ]);
    } else {
      return abort(400);
    }
  }

  public function request_icn_object(Request $request, Csdb $csdb)
  {
    // otomatis abort 404 jika $csdb null
    $icn = new CSDBObject("5.0");
    // $icn->load(storage_path("csdb/$csdb->filename"));
    $icn->load(storage_path("csdb/{$request->user()->storage}/$csdb->filename"));
    dd($icn->document->getFile());
    return Response::make($icn->document->getFile(),200, [
      'Content-Type' => $icn->document->getFileinfo()['mime_type'],
    ]);
  }

  /**
   * DEPRECIATED, diganti oleh Csdb\CsdbController@get_object_raw
   * $request->get('output') = "'model'|default:''";
   * @return Illuminate\Support\Facades\Response;
   */
  public function request_csdb_object(Request $request, string $filename)
  {
    $model = Csdb::where('filename', $filename)->first();
    // $model = true;
    if($model){
      // $dom = MpubCSDB::importDocument(storage_path('csdb'), 'DMC-MALE-A-15-30-07-00A-028A-A_000-01_EN-EN.xml');
      // $dom = MpubCSDB::importDocument(storage_path('csdb'), 'DMC-MALE-A-00-00-00-00A-00QA-D_000-01_EN-EN.xml');
      $CSDBObject = new CSDBObject("5.0");
      $CSDBObject->load(storage_path("csdb/$filename"));
      $formatter = new Formatter();
      return Response::make($formatter->format($CSDBObject->document->saveXML()), 200, ['Content-Type' => 'text/xml']);
    }
  }

  
  ############### NEW for csdb3 ###############

  public function export(Request $request, string $filename)
  {
    $model = Csdb::where('filename', $filename)->first();
    if(!$model) return $this->ret2(400, "{$filename} is not found.");

    $doc = MpubCSDB::importDocument(storage_path($model->path), $model->filename);
    if($doc instanceof \DOMDocument){
      $model->DOMDocument = $doc;
      $scanned = Helper::scanObjectRef($model->DOMDocument);
      $an = Helper::analyzeURI($model->DOMDocument->baseURI); 
      
      // ini untuk read zip file, jika kedepannya nanti kita perlu membaca repository
      // $zipper = new \Madnest\Madzipper\Madzipper;
      // $zipper->zip(storage_path('test.zip'))->getFilePath('test/DML-MALE-0001Z-P-2024-00002_000-01.xml');
      // $zipper->zip(storage_path('test.zip'));
      // $zipper->getFileContent('test/DML-MALE-0001Z-P-2024-00002_000-01.xml');
      // $zipper->getFileContent(storage_path('test.zip'));
      // dd($zipper);
  
      // $zipper->make('test.zip')->folder('test')->add('tes.txt');
      // $zipper->make(storage_path('test.zip'))->folder('test')->add($an['path'] . DIRECTORY_SEPARATOR . $filename);
      // $zipper->zip('test.zip')->folder('test')->add('tes2.txt','foo');
      // return $zipper->close();    
      $zip = new ZipStream(
        outputName: (preg_replace("/\.\w+$/",'',$filename)) . '.zip',
        sendHttpHeaders: true,
      );      
      foreach($scanned['found'] as $k => $name){
        $zip->addFileFromPath(
          fileName: $name, 
          path: $an['path'] . DIRECTORY_SEPARATOR . $name
        );
      }  
      return $zip->finish();
    } 
    else {
      return Response::download($doc->getFile('SplFileInfo'), $doc->getFilename(), [
        'content-type' => $doc->getFileinfo()['mime_type'],
      ]);
    }
  }

  /**
   * DEPRECIATED diganti oleh @get_transformed_contentpreview
   * $ignoreError=1 is needed when you want to send message if any error exist while transforming is success
   */
  public function provide_csdb_transform3(Request $request, string $filename)
  {
    if(!($csdb_model = Csdb::where('filename', $filename)->first())) return $this->ret2(400, ["{$filename} cannot transformed."]);
    $csdb_dom = MpubCSDB::importDocument(storage_path($csdb_model->path),$filename);

    if($csdb_dom instanceof ICNDocument){
      // saat ini belum bisa baca file 3D (step,igs,stl,etc)karena mime nya tidak dikenal
      // $mime = $csdb_dom->getFileinfo()['mime_type'];
      // $file = $csdb_dom->getFile();
      // return Storage::disk('csdb')->download($filename, null, [
      //   "Content-Type" => $mime,
      // ]);
      // return $file;
      // return $this->ret2(200,["Transform Success"], ['file' => $file, 'mime' => $mime]);
      // $ret = Response::make($file,200,[
      //   // 'Content-Type' => $mime, // untuk display
      //   'Content-Type' => 'plain/text', // untuk download dan display
      // ]);
      // return $ret;
      // return $this->ret2(200,["Transform Success"], ['file' => base64_encode($file), 'mime' => $mime]);
      return;
    }
    if(!$csdb_dom) return $this->ret2(400, ["failed to transform {$filename}."]);
    $csdb_model->DOMDocument = $csdb_dom;
    $csdb_model->objectpath = "/api/csdb";
    $transformed = $csdb_model->transform_to_xml(resource_path("views/ietm/xsl/"));

    if($error = MpubCSDB::get_errors(false) AND (int)$request->get('ignoreError')){
      return $this->ret2(200, [$error], ['file' => $transformed, 'mime' => 'text/html']); // ini yang dipakai vue
    }
    // return Response::make($transformed,200,['Content-Type' => 'text/html']);
    return $this->ret2(200, null, ['file' => $transformed, 'mime' => 'text/html']); // ini yang dipakai vue
  }


  ############### NEW by VUE ###############
  public function provide_csdb_transform2(Request $request)
  {
    $projectName = $request->route('project_name');
    $filename = $request->route('filename');
    if(!$projectName OR !$filename) return $this->ret(400, ['Project name or object filename must be true provided.']);

    if(!($csdb_model = Csdb::where('filename', $filename)->first())){
      return Response::make('', 404);
    }
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
    // dd($csdb_dom, MpubCSDB::get_errors(), $csdb_model->path, $csdb_model->status);
    $object->DOMDocument = $csdb_dom;
    $object->repoName = $projectName;
    $object->objectpath = "/api/csdb";
    $object->absolute_objectpath = storage_path("app/{$csdb_model->path}");
    $transformed = $object->transform_to_xml(resource_path("views/ietm/xsl/"));
    
    if($error = MpubCSDB::get_errors(false)){
      return $this->ret(400, [$error]);
    }

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

  // public function search(Request $request)
  // {
  //   return $request->all();
  //   $search_key = $request->get('search-obj');
  //   $filename = $request->get('filename');
  //   $projectName = $request->get('project_name');

  //   $csdb_object_model = Csdb::where('filename', $filename)->where('project_name', $projectName)->first();
  //   if($csdb_object_model OR $search_key) return $this->ret(400, []);
  //   dd($search_key, $csdb_object_model);
  // }



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
