<?php

namespace App\Models\Csdb;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Csdb;
use Illuminate\Support\Facades\Auth;
use Ptdi\Mpub\Main\CSDBObject;
use Ptdi\Mpub\Main\CSDBStatic;

class Comment extends Csdb
{
  use HasFactory;

  public function create_xml(Array $params)
  {
    $this->CSDBObject->setPath(CSDB_STORAGE_PATH);
    $this->CSDBObject->setConfigXML(CSDB_VIEW_PATH . DIRECTORY_SEPARATOR . "xsl" . DIRECTORY_SEPARATOR . "Config.xml"); // nanti diubah mungkin berbeda antara pdf dan html meskupun harusnya SAMA. Nanti ConfigXML mungkin tidak diperlukan jika fitur BREX sudah siap sepenuhnya.
    $this->CSDBObject->createCOM($params);

    $ident = $this->CSDBObject->document->getElementsByTagName('commentIdent')[0];
    $filename = CSDBStatic::resolve_commentIdent($ident);

    $this->filename = $filename;
    $this->path = "csdb";
    $this->editable = 1;
    $this->initiator_id = Auth::user()->id;

    return $ident ? true : false;
  }
}
