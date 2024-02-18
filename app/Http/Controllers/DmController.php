<?php

namespace App\Http\Controllers;

use App\Models\Csdb;
use Illuminate\Http\Request;

class DmController extends Controller
{
  
  /**
   * get dmc where stage status is 'staged'
   * this will get as initator_id
   */
  public function get_dmc_staged_list(Request $request)
  {
    $this->model = Csdb::with('initiator');
    $this->model->where('initiator_id', $request->user()->id);
    $this->model->where('remarks', 'like' ,'%"stage":"staged"%');
    $this->search($request->get('filenameSearch'));
    $this->model->where('filename', 'like', 'DMC%');
    $ret = $this->model->paginate(15);
    $ret->setPath($request->getUri());
    return $ret;
  }

  
  /**
   * get dmc where stage status is 'unstaged'
   * this will get as initator_id
   */
  public function get_dmc_unstaged_list(Request $request)
  {
    $this->model = Csdb::with('initiator');
    $this->model->where('initiator_id', $request->user()->id);
    $this->model->where('remarks', 'not like' ,'%"stage":"staged"%');
    $this->search($request->get('filenameSearch'));
    $this->model->where('filename', 'like', 'DMC%');
    $ret = $this->model->paginate(15);
    $ret->setPath($request->getUri());
    return $ret;
  }
}
