<?php

namespace App\Http\Controllers;

use App\Models\Csdb;
use Illuminate\Http\Request;

class BrController extends Controller
{
  
  public function get_br_list(Request $request)
  {
    $this->model = Csdb::where('filename', 'like', "DMC-%-022%")->orWhere('filename', 'like', "DMC-%-024%");
    $this->search($request->get('filenameSearch'));
    $ret = $this->model->paginate(15);
    $ret->setPath($request->getUri());
    return $ret;
  }

  public function get_brex_list(Request $request)
  {
    $this->model = Csdb::where('filename', 'like' ,"DMC-%-022%");
    // $this->model = Csdb::with('initiator')->where('filename', 'like' ,"DMC-%");
    $this->search($request->get('filenameSearch'));
    $ret = $this->model->paginate(15);
    $ret->setPath($request->getUri());
    return $ret;
  }

  public function get_brdp_list(Request $request)
  {
    $this->model = Csdb::where('filename', 'like' ,"DMC-%-022%");
    // $this->model = Csdb::with('initiator')->where('filename', 'like' ,"DMC-%");
    $this->search($request->get('filenameSearch'));
    $ret = $this->model->paginate(15);
    $ret->setPath($request->getUri());
    return $ret;
  }

  public function create_br(Request $request)
  {
    // sementara pakai CsdbController@create dulu
    return (new CsdbController())->create($request);
  }
}
