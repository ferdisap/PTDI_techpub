<?php

namespace App\Http\Controllers\Csdb;

use App\Http\Controllers\Controller;
use App\Http\Requests\Csdb\DdnCreate;
use App\Models\Csdb;
use App\Models\Csdb\Ddn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Rules\Csdb\BrexDmRef as BrexDmRefRules;
use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Ptdi\Mpub\Main\CSDBStatic;

class DdnController extends Controller
{
  public function create(DdnCreate $request)
  {
    $CSDBModel = new Csdb();
    $CSDBModel->CSDBObject = $request->CSDBObject[0];
    $CSDBModel->filename = $CSDBModel->CSDBObject->filename;
    $CSDBModel->path = $request->validated()['path'];
    $CSDBModel->storage_id = $request->user()->id;
    $CSDBModel->initiator_id = $request->user()->id;

    if ($CSDBModel->saveDOMandModel($request->user()->storage, [
      ['MAKE_CSDB_CRBT_History', [Csdb::class]],
      ['MAKE_USER_CRBT_History', [$request->user(), '', $CSDBModel->filename]]
    ])) {
      return $this->ret2(200, ["{$CSDBModel->filename} has been created."], ['csdb' => $CSDBModel, 'infotype' => 'info']);
    }
    return $this->ret2(400, ["fail to create and save DDN."]);
  }

  public function list(Request $request)
  {
    $DDNModels = Ddn::with(['csdb' => function(Builder $query){
      $query->select(['id', 'filename', 'storage_id']);
      $query->with(['owner' => function(Builder $query){
        $query->select(['id', 'first_name', 'middle_name', 'last_name', 'job_title', 'email', 'work_enterprise_id']);
        $query->with(['work_enterprise' => function(Builder $query){
          $query->without('code');
          $query->select(['id', 'name']);
        }]);
      }]);
    }])->where('dispatchTo_id', 2)->orderBy('id', 'desc')->paginate(100);
    return $DDNModels;
  }
}
