<?php

namespace App\Http\Controllers\Csdb;

use App\Http\Controllers\Controller;
use App\Models\Csdb\Dmc;
use Illuminate\Http\Request;

class DmcController extends Controller
{
  public function searchModel(Request $request)
  {
    $this->model = new Dmc();
    $sc = $this->generateWhereRawQueryString($request->get('sc'));

    if($request->limit){
      $result = ($this->model->whereRaw($sc[0])->limit($request->limit)->get()->toArray());
    } else {
      $result = ($this->model->whereRaw($sc[0])->get()->toArray());
    }

    return $this->ret2(200, ['result' => $result]);
  }
}
