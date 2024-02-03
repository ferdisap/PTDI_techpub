<?php

namespace App\Http\Controllers;

use App\Models\Csdb;
use Illuminate\Http\Request;
use Ptdi\Mpub\CSDB as MpubCSDB;
use Illuminate\Support\Facades\Response;

class BrexController extends Controller
{
  ######## NEW for csdb3 ########
  public function get()
  {
    $brexs = Csdb::where('filename', 'like' ,"DMC-%-%-%-%-%-022%")->get();
    // $brexs = Csdb::where('filename', 'like' ,"DML-%-%-%-%24%-%16%")->get();
    return $brexs;
  }

  public function create(Request $request)
  {
    // sementara pakai CSdbController@create dulu
  }

  ######## new by VUE ########
  /**
   * tujuannya sama dengan fungsi table, yaitu menampilkan list number of brdp
   */
  public function app()
  {
    return view('brex/app');
  }

  public function transform(Request $request)
  {
    $project_name = $request->route('project_name');
    $filename = $request->route('filename');
    $type = $request->get('type');
    if(!$type) return $this->ret(400, ['You must choose between SNS Rules, Context Rules, or Non-Context Rules']);

    $csdb_model = Csdb::where('filename', $filename)->first(['path']);
    $csdb_dom = MpubCSDB::importDocument(storage_path("app/{$csdb_model->path}/"),$filename);

    $object = new Csdb();
    $object->DOMDocument = $csdb_dom;
    $transformed = $object->transform_to_xml(resource_path("views/brex/xsl/"), "{$type}.xsl");

    if($error = MpubCSDB::get_errors(false)){
      return $this->ret(400, [$error]);
    }

    return Response::make($transformed,200,['Content-Type' => 'text/html']);

    $html = <<<EOL
    <div>Foo bar</div>
    EOL;
    return $html;
    return $project_name . $filename;
  }










  ######## old by Blade ########
  
  public function indexBrex()
  {
    if (request()->utility == 'getfile'){
      return $this->getFile(request()->path, request()->ct);
    } else {
      return view('brex/brex_index', [
        'title' => 'BREX Index'
      ]);
    }
  }

  public function table($aircraft)
  {
    // Blade::setPath(base_path() . DIRECTORY_SEPARATOR . 'ietp_n219' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR);
    // dd(Blade::getPath());
    // dd(__DIR__.'/../components');
    return view('brex/brex_' . $aircraft .'_index', [
      'title' => 'brex ' . $aircraft,
      // 'lists' => $this->brdpListToArray($lists)
    ]);
  }

  // private function getFile($path, $ct){    
  //   switch($ct){
  //     case 'xml':
  //       return response(file_get_contents($path, FILE_USE_INCLUDE_PATH), 200, [
  //         'Content-Type' => 'application/xml'
  //       ]);
  //     case 'jpg':
  //       return;
  //     case 'html':
  //       return view('brex/index');
  //     default:
  //       return false;        
  //   }
  // }
}
