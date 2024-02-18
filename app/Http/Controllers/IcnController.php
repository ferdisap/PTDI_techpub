<?php

namespace App\Http\Controllers;

use App\Models\Csdb;
use Illuminate\Http\Request;

class IcnController extends Controller
{
  /**
   * fungsi ini nanti bisa dipindahkan ke DmcController
   * get dmc where stage status is 'staged'
   * this will get as initator_id
   */
  public function get_icn_staged_list(Request $request)
  {
    $this->model = Csdb::with('initiator');
    $this->model->where('initiator_id', $request->user()->id);
    $this->model->where('remarks', '%"stage":"staged"%');
    $this->search($request->get('filenameSearch'));
    $this->model->where('filename', 'like', 'ICN%');
    $ret = $this->model->paginate(15);
    $ret->setPath($request->getUri());
    return $ret;
  }
}
