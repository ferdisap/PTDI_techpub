<?php

namespace App\Http\Controllers\Csdb;

use App\Http\Controllers\Controller;
use App\Http\Requests\Csdb\CommentCreate;
use App\Models\Csdb as ModelsCsdb;
use App\Models\Csdb\Comment;
use App\Models\Project;
use App\Models\User;
use App\Rules\Csdb\BrexDmRef as BrexDmRefRules;
use Carbon\Carbon;
use Closure;
use DOMElement;
use DOMNode;
use DOMXPath;
use Gumlet\ImageResize;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rules\File;
use PhpParser\Node\Expr\Cast\Object_;
use Ptdi\Mpub\CSDB;
use Ptdi\Mpub\ICNDocument;
use Ptdi\Mpub\Validation;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipStream\ZipStream;
use Illuminate\Support\Facades\Process;
use PrettyXml\Formatter;
use Ptdi\Mpub\Helper;
use Ptdi\Mpub\Main\CSDBStatic;

class CommentController extends Controller
{
  /**
   * selanjutnya buat attachment.
   * COM attachment diujung filename ditambah -attachmentNmber.extension see pdf page 1906/3503
   */
  public function create(CommentCreate $request)
  {
    $COMModel = new Comment();
    $csdb = new ModelsCsdb();
    $COMModel->setProtected([
      'table' => $csdb->getProtected('table'),
      'fillable'=> $csdb->getProtected('fillable'),
      'casts'=> $csdb->getProtected('casts'),
      'attributes'=> $csdb->getProtected('attributes'),
    ]);
    $isCreated = $COMModel->create_xml($request->user()->storage, $request->validated());
    if(!($isCreated)) return $this->ret2(400, ["fails to create DDN."]);    
    $ident = $COMModel->CSDBObject->document->getElementsByTagName('commentIdent')[0];
    $filename = CSDBStatic::resolve_commentIdent($ident);
    $COMModel->filename = $filename;
    $COMModel->path = 'csdb';
    $COMModel->available_storage = $request->user()->storage;
    $COMModel->initiator_id = $request->user()->id;    

    if($COMModel->saveDOMandModel($request->user()->storage)){
      $COMModel->initiator; // supaya ada initiator saat return
      // jalankan event untuk kirim email ke dispatchTo person
      return $this->ret2(200, ["{$COMModel->filename} has been created."], ['model' => $COMModel, 'infotype' => 'info']);
    } else {
      return $this->ret2(400, ["fail to create and save COM."]);
    }
  }
}