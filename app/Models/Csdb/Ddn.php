<?php

namespace App\Models\Csdb;

use App\Models\Csdb;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ptdi\Mpub\Main\CSDBStatic;
use Illuminate\Support\Facades\Auth;

class Ddn extends Csdb
{
  use HasFactory;

  protected $with = ['initiator'];

  public function create_xml(Array $params)
  {
    $this->CSDBObject->setPath(CSDB_STORAGE_PATH);
    $this->CSDBObject->setConfigXML(CSDB_VIEW_PATH . DIRECTORY_SEPARATOR . "xsl" . DIRECTORY_SEPARATOR . "Config.xml"); // nanti diubah mungkin berbeda antara pdf dan html meskupun harusnya SAMA. Nanti ConfigXML mungkin tidak diperlukan jika fitur BREX sudah siap sepenuhnya.
    $this->CSDBObject->createDDN($params);

    $ident = $this->CSDBObject->document->getElementsByTagName('ddnIdent')[0];
    $filename = CSDBStatic::resolve_ddnIdent($ident);

    $this->filename = $filename;
    $this->path = "csdb";
    $this->editable = 1;
    $this->initiator_id = Auth::user()->id;

    return $ident ? true : false;
  }
}
