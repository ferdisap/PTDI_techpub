<?php

namespace App\Http\Controllers;

use App\Models\Csdb;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Ptdi\Mpub\CSDB as MpubCSDB;
use Ptdi\Mpub\Pdf2\Applicability;
use XSLTProcessor;

class CsdbServiceController extends CsdbController
{
  use Applicability;

  public function provide_csdb_transform(Request $request)
  {
    $filename = $request->filename;
    $csdb_model = Csdb::where('filename', $filename)->first(['path']);
    $csdb_dom = MpubCSDB::importDocument(storage_path("app/{$csdb_model->path}/"),$filename);
    $type = $csdb_dom->firstElementChild->tagName;

    if($type != 'dml'){
      $appl = (MpubCSDB::getApplicability($csdb_dom, storage_path("app/{$csdb_model->path}")));
      if($err = MpubCSDB::get_errors(true, 'getApplicability')){
        $appl = json_encode($err);
      }
      $appl = $this->getApplicability('','first', true, $appl);
    } else {
      $appl = '';
    }


    $utility = $request->get('utility');

    $xsl = MpubCSDB::importDocument(resource_path("views/csdb/{$utility}/{$type}.xsl"));
    $xsltproc = new XSLTProcessor;
    $xsltproc->importStylesheet($xsl);

    $xsltproc->registerPHPFunctions((function(){
      return array_map(fn($name) => MpubCSDB::class."::$name", get_class_methods(MpubCSDB::class));
    })());
    $xsltproc->setParameter('','filename', $filename);
    $xsltproc->setParameter('','applicability', $appl);
    $xsltproc->setParameter('','absolute_path_csdb_input', $appl);
    $transformed = $xsltproc->transformToDoc($csdb_dom);
    
    return Response::make($transformed->saveHTML(),200,['Content-Type' => 'text/html']);
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
}
