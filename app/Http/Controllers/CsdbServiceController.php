<?php

namespace App\Http\Controllers;

use App\Models\Csdb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Ptdi\Mpub\CSDB as MpubCSDB;

class CsdbServiceController extends CsdbController
{

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
