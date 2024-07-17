<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
  public function searchModel(Request $request)
  {
    $this->model = new User();
    $sc = $this->generateWhereRawQueryString($request->get('sc'));

    if($request->limit){
      $result = ($this->model->whereRaw($sc[0])->limit($request->limit)->get()->toArray());
    } else {
      $result = ($this->model->whereRaw($sc[0])->get()->toArray());
    }

    return $this->ret2(200, ['result' => $result]);
  }
}
