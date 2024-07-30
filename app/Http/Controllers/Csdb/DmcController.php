<?php

namespace App\Http\Controllers\Csdb;

use App\Http\Controllers\Controller;
use App\Models\Csdb;
use App\Models\Csdb\Dmc;
use Illuminate\Http\Request;
use Ptdi\Mpub\Main\Helper;

class DmcController extends Controller
{
  public function searchModel(Request $request)
  {
    // $DMCModels = Csdb::getObjects(Dmc::class, ['exception' => ['CSDB-DELL', 'CSDB-PDEL']]);
    // if($request->limit){
    //   $DMCModels = ($DMCModels->limit($request->limit));
    // }    
    // return $this->ret2(200, ['result' => $DMCModels->get()->toArray()]);

    $CSDBModels = Csdb::getCsdbs(['exception' => ['CSDB-DELL', 'CSDB-PDEL']]);

    $query = Helper::generateWhereRawQueryString($request->get('sc'),'csdb');
    $CSDBModels->whereRaw($query[0],$query[1]);

    if($request->limit){
      $CSDBModels = ($CSDBModels->limit($request->limit));
    }    
    return $this->ret2(200, ['result' => $CSDBModels->get()->toArray()]);
  }
}
