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
use Illuminate\Support\Facades\Auth;
use Ptdi\Mpub\Main\CSDBStatic;

class DdnController extends Controller
{
  public function create(DdnCreate $request)
  {
    $DDNModel = new Ddn();
    $csdb = new Csdb();
    $DDNModel->setProtected([
      'table' => $csdb->getProtected('table'),
      'fillable'=> $csdb->getProtected('fillable'),
      'casts'=> $csdb->getProtected('casts'),
      'attributes'=> $csdb->getProtected('attributes'),
    ]);
    $isCreated = $DDNModel->create_xml($request->user()->storage, $request->validated());
    if(!($isCreated)) return $this->ret2(400, ["fails to create DDN."]);    
    
    $ident = $DDNModel->CSDBObject->document->getElementsByTagName('ddnIdent')[0];
    $filename = CSDBStatic::resolve_ddnIdent($ident);
    $DDNModel->filename = $filename;
    $DDNModel->path = 'csdb';
    $DDNModel->available_storage = $request->user()->storage;
    $DDNModel->initiator_id = $request->user()->id;
    
    if($DDNModel->saveDOMandModel($request->user()->storage)){
      $DDNModel->initiator; // supaya ada initiator saat return
      // jalankan event untuk kirim email ke dispatchTo person
      return $this->ret2(200, ["{$DDNModel->filename} has been created."], ['model' => $DDNModel, 'infotype' => 'info']);
    } else {
      return $this->ret2(400, ["fail to create and save DDN."]);
    }
  }
}
