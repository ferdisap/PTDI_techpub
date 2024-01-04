<?php

namespace App\Http\Controllers;

use App\Models\Csdb as ModelsCsdb;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use DOMElement;
use DOMNode;
use DOMXPath;
use Gumlet\ImageResize;
use Illuminate\Http\Request;
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
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipStream\ZipStream;

class CsdbController extends Controller
{
  ################# NEW by VUE #################
  public function general_index(Request $request)
  {
    return view('csdb2.app');
  }
  public function getcsdbdata(Request $request)
  {
    if(!$request->get('project_name')){
      Project::setFailMessage(['There is no such project name'], 'project_name');
      return response()->json(Project::getFailMessage(true, 'project_name'),400);
    }
    $csdb = ModelsCsdb::with('initiator')->where('project_name', $request->get('project_name'))->orderBy('filename','asc');
    if($request->get('filename')){
      $csdb = $csdb->where('filename', $request->get('filename'));
    }
    $csdb = $csdb->get(['filename', 'status','initiator_id','description','created_at','updated_at']); 
    return $csdb;
  }
  public function postupdate2(Request $request)
  {
    // return $this->ret(200,['bla bla bla has been updated.']);

    $csdb_object = ModelsCsdb::where('filename', $request->get('filename'))->first();
  
    // validasi existing and editable
    if (!$csdb_object) return $this->ret(400,["The object filename is not exist. You may to go to CREATE page to build one."]);
    if (!$csdb_object->editable) return $this->ret(400,["The {$csdb_object->filename} is not enable to edit"]);
    if (str_contains($csdb_object->path, "__")) $this->ret(400,["{$csdb_object->filename} with status {$csdb_object->status} cannot be modified. It must be returned to used csdb."]);
  
    $path = "csdb/{$csdb_object->project->name}";
  
    // validate initiator
    if ($request->user()->id != $csdb_object->initiator_id) {
      return $this->ret(400,["{$csdb_object->path} Only Initator which can be update this CSDB object."]);
    }
  
    $entity = $request->file('entity');
    if ($entity and str_contains($entity->getMimeType(), 'text')) {
      $request->replace(['xmleditor' => file_get_contents($entity->getPathname())]);
      $entity = false;
    }
    // update by xmleditor
    if ($xmlstring = $request->get('xmleditor')) {
      $proccessid = CSDB::$processid = self::class . "::update";
      $dom = CSDB::importDocument('', '', trim($xmlstring));
      if (!$dom) {
        return $this->ret(400,[['xmleditor' => CSDB::get_errors(true, $proccessid)]]);
      }
  
      // validate rootname pm or dm, sekaligus mendapatkan filename nya juga
      if (!($validateRootname = CSDB::validateRootname($dom))) {
        // return back()->withInput()->with(['result' => 'fail'])->withErrors(CSDB::get_errors(true, 'validateRootname'), 'info');
        return $this->ret(400,[['xmleditor' => CSDB::get_errors(true, 'validateRootname')]]);
      }
      $csdb_filename = $validateRootname[1];
  
      // validate sequencial attribbute inWork
      $old_issueInfo = CSDB::importDocument(storage_path("app" . DIRECTORY_SEPARATOR . $csdb_object->path . DIRECTORY_SEPARATOR), $csdb_object->filename)->getElementsByTagName('issueInfo')[0];
      $old_inwork = $old_issueInfo->getAttribute('inWork');
      $old_issueNumber = $old_issueInfo->getAttribute('issueNumber');
      $new_issueInfo = $dom->getElementsByTagName('issueInfo')[0];
      $new_inwork = $new_issueInfo->getAttribute('inWork');
      $new_issueNumber = $new_issueInfo->getAttribute('issueNumber');
      if ($old_issueNumber != $new_issueNumber) {
        // fail, m: you cannot update issueNumber at here.
        // return $this->ret(400,['you cannot update issueNumber of data module identification here.']);
        return $this->ret(400,[['xmleditor' => ['you cannot update issueNumber of data module identification here.']]]);
      }
      if ($old_inwork != $new_inwork) {
        if ($new_inwork - $old_inwork != 1) {
          // return $this->ret(400,['the inwork number of data module identification must be increment of 1.']);
          return $this->ret(400,[['xmleditor' => ['the inwork number of data module identification must be increment of 1.']]]);
        }
      }
      
      // validate Schema
      if ($request->get('xsi_validate')) {
        CSDB::validate('XSI', $dom);
        if (CSDB::get_errors(false, 'validateBySchema')) {
          // return $this->ret(400,CSDB::get_errors(true, 'validateBySchema'));
          return $this->ret(400,[['xmleditor' => CSDB::get_errors(true, 'validateBySchema')]]);
        }
      }
  
      // validate Brex
      if ($request->get('brex_validate') == 'on') {
        CSDB::validate('BREX', $dom, null, storage_path("app/{$path}"));
        if (CSDB::get_errors(false, 'validateByBrex')) {
          // return $this->ret(400,CSDB::get_errors(true, 'validateByBrex'));
          return $this->ret(400,[['xmleditor' => CSDB::get_errors(true, 'validateByBrex')]]);
        }
      }
  
      // saving to database
      $old_name = $csdb_object->filename;
      $old_path = $csdb_object->path;
      $old_status = $csdb_object->status;
      // dd($old_path, $path, $old_name, $csdb_filename);
      if ($old_path == $path and $old_name == $csdb_filename) {
        $csdb_object->update(array_merge($request->all(), ['status' => 'modified']));
        Storage::disk('local')->put($path . DIRECTORY_SEPARATOR . $csdb_filename, $xmlstring);
      } else {
        // validate referenced dmrl, dilakukan jika filename (tidak termasuk issueNumber dan inWork) nya berbeda dengan yang lama
        if (preg_replace("/_\d{3,5}-\d{2}|_[A-Za-z]{2,3}-[A-Z]{2}/", '', $old_name) != preg_replace("/_\d{3,5}-\d{2}|_[A-Za-z]{2,3}-[A-Z]{2}/", '', $csdb_filename)) { // untuk membersihkan inwork dan issue number pada filename
          $ident = $validateRootname[2];
          if (!(($r = $this->validateByDMRL($request->get('dmrl'), $csdb_filename, $ident))[0])) {
            // return $this->ret(400,$r[1]);
            // return back()->withInput()->with(['result' => 'fail'])->withErrors($r[1], $r[2] ?? 'default');
            return $this->ret(400,[['xmleditor' => $r[1]]]);
          }
        }
  
        ModelsCsdb::create([
          'filename' => $csdb_filename,
          'path' => $path,
          'status' => 'new',
          'description' => '',
          'editable' => 1,
          'initiator_id' => $request->user()->id,
          'project_name' => $csdb_object->project->name,
        ]);
  
        // saving to local
        // moving old file
        $csdb_object->update(['path' => $old_path . "/__unused", 'status' => 'unused']);
        Storage::disk('local')->move($old_path . DIRECTORY_SEPARATOR . $old_name, $old_path . "/__unused" . DIRECTORY_SEPARATOR . $old_name);
        // create new file
        Storage::disk('local')->put($path . DIRECTORY_SEPARATOR . $csdb_filename, $xmlstring);
      }
      return $this->ret(200,["saved with filename: {$csdb_filename}"]);
    }
    // update by entity jika mime bukan text
    elseif ($entity) {
      // tidak perlu validate referenced dmrl karena ini update entity sehingga pasti filename nya sama
  
      // validate Brex
      if ($request->get('brex_validate') == 'on') {
        CSDB::validate('BREX-NONCONTEXT', $csdb_object->path . DIRECTORY_SEPARATOR . $csdb_object->filename, null, storage_path("app"));
        if (CSDB::get_errors(false, 'validateByBrex')) {
          // return $this->ret(400,CSDB::get_errors(true, 'validateByBrex'));
          return $this->ret(400,[['entity' => CSDB::get_errors(true, 'validateByBrex')]]);
        }
      }
      // saving to database
      $csdb_object->update(array_merge($request->all(), ['status' => 'modified']));
  
      // saving to local
      $entity->storeAs($path, $csdb_object->filename);
      return $this->ret(200,["{$csdb_object->filename} has been deleted from local disk."]);
    }
    // update ex: description or others if entity or xmleditor is empty
    else {
      $csdb_object->update(array_merge($request->all(), ['status' => 'modified']));
      return $this->ret(200,["{$csdb_object->filename} has been updated."]);
    }
  }

  /**
   * bisa provide csdb string atau sesuai mimenya
   */
  public function getcsdb(Request $request)
  {
    if(!$request->get('filename') AND !$request->get('project_name')){
      Project::setFailMessage(['Object filename and Project Name must be provided.'], 'messages');
      return response()->json(Project::getFailMessage(true, 'messages'),400);
    }
    if(!$request->mime){
      $csdb = ModelsCsdb::where('filename', $request->get('filename'))->first();
      $file = Storage::get($csdb->path . DIRECTORY_SEPARATOR . $csdb->filename);
      $mime = Storage::mimeType($csdb->path . DIRECTORY_SEPARATOR . $csdb->filename);
      return response($file,200, [
        'Content-Type' => $mime
      ]);
    }
  }

  /**
   * mendahulukan uploaded file, baru editor
   */
  public function postcreate2(Request $request)
  {
    // validate form
    if (!($project = Project::find($request->get('project_name')))) {
      Project::setFailMessage(['There is no such project name'], 'project_name');
      Project::setFailMessage(['check the input of Project Name.'], 'INFO');
      return $this->ret(400, [Project::getFailMessage(true, 'project_name')]);
    }
    $dom = null;
    $proccessid = CSDB::$processid = self::class . "::create";

    // set type and get dom if xml, by the string or file upload
    $type = '';
    $file = $request->file('entity');
    if ($file and in_array($file->getMimeType(), array('text/xml', 'text/plain'))) {
      $type = 'xml';
      $xmlstring = file_get_contents($file->getPathname());
      $dom = CSDB::importDocument('', '', $xmlstring);
    } elseif ($file and in_array($file->getMimeType(), array('image/jpg', 'image/jpeg', 'image/png', 'image/svg'))) {
      $type = 'multimedia';
    } else {
      $type = 'xml';
      $xmlstring = $request->get('xmleditor');
      $dom = CSDB::importDocument('', '', trim($xmlstring), '', 'tes'); // akan false jika tidak bisa jad DOM
    }

    // return false jika dom tidak bisa di buat
    if ($type == 'xml' and !$dom) {
      return $this->ret(400, [["xmleditor" => CSDB::get_errors(true, $proccessid)]]);
    } 
    elseif ($dom) {
      // validation: return false jika rootname buka dmodule, pm, dml atau icnMetadataFile
      if (!($validateRootname = CSDB::validateRootname($dom))) {
        return $this->ret(400, [["xmleditor" => CSDB::get_errors(true, 'validateRootname')]]);
      }
      $csdb_filename = $validateRootname[1];
      $ident = $validateRootname[2];
      $path = "csdb/{$project->name}";
    }
    elseif ($type = 'multimedia') {
      $csdb_filename = $request->file('entity')->getClientOriginalName();
      $ident = 'infoEntity';
      $path = "csdb/{$project->name}";
      preg_match("/ICN\-[A-Z0-9]{5}\-[A-Z0-9]{5,10}\-[0-9]{3}\-[0-9]{2}.[a-z]+/", $csdb_filename, $matches);
      if (empty($matches)) {
        return $this->ret(400, ["{$csdb_filename} is not match with pattern: ICN\-[A-Z0-9]{5}\-[A-Z0-9]{5,10}\-[0-9]{3}\-[0-9]{2}.[a-z]+"]);
      }
    }

    // writing: return true or false jika ada/tidak file existing
    if (Storage::disk('local')->exists($path . DIRECTORY_SEPARATOR . $csdb_filename) OR ModelsCsdb::where('filename', $csdb_filename)->first()) {
      return $this->ret(400, ["{$csdb_filename} is already existed."]);
    }
    elseif ($csdb_filename) {
      // validate referenced dmrl       
      if (!(($r = $this->validateByDMRL($request->get('dmrl'), $csdb_filename, $ident))[0])) {
        return $this->ret(400, [[$r[2] ?? 'default' => $r[1]]]);
      }

      // / validate Schema
      if ($request->get('xsi_validate')) {
        CSDB::validate('XSI', $dom);
        if (CSDB::get_errors(false, 'validateBySchema')) {
          return $this->ret(400,[['xmleditor' => CSDB::get_errors(true, 'validateBySchema')]]);
        }
      }
  
      // validate Brex
      if ($request->get('brex_validate') == 'on') {
        CSDB::validate('BREX', $dom, null, storage_path("app/{$path}"));
        if (CSDB::get_errors(false, 'validateByBrex')) {
          return $this->ret(400,[['xmleditor' => CSDB::get_errors(true, 'validateByBrex')]]);
        }
      }

      // saving
      $saved = false;
      if ($type == 'xml') {
        Storage::disk('local')->put($path . DIRECTORY_SEPARATOR . $csdb_filename, $xmlstring);
        $saved = true;
      } elseif ($type == 'multimedia') {
        $request->file('entity')->storeAs($path, $csdb_filename);
        $saved = true;
      }
      if ($saved) {
        ModelsCsdb::create([
          'filename' => $csdb_filename,
          'path' => $path,
          'description' => $request->get('description'),
          'status' => 'new',
          'editable' => 1,
          'initiator_id' => $request->user()->id,
          'project_name' => $project->name,
        ]);

        return $this->ret(200,["saved with filename: {$csdb_filename}"]);
      }
      return $this->ret(400, ["Error while processing {$csdb_filename}"]);
    } else {
      return $this->ret(400, ["Error while writing new objects."]);
    }
  }


  ################# OLD by Blade #################
  /**
   * mendahulukan uploaded file, baru editor
   */
  public function postcreate(Request $request)
  {
    // dd($request->all());
    // validate form
    if (!($project = Project::find($request->get('project_name')))) {
      Project::setFailMessage(['There is no such project name'], 'project_name');
      return back()->withInput()->with(['result' => 'fail'])
        ->withErrors(Project::getFailMessage(true, 'project_name'))
        ->withErrors(['check the input of Project Name.'], 'info');
    }
    $dom = null;
    $proccessid = CSDB::$processid = self::class . "::create";

    // set type and get dom if xml, by the string or file upload
    $type = '';
    $file = $request->file('entity');
    if ($file and in_array($file->getMimeType(), array('text/xml', 'text/plain'))) {
      $type = 'xml';
      $xmlstring = file_get_contents($file->getPathname());
      $dom = CSDB::importDocument('', '', $xmlstring);
    } elseif ($file and in_array($file->getMimeType(), array('image/jpg', 'image/jpeg', 'image/png', 'image/svg'))) {
      $type = 'multimedia';
    } else {
      $type = 'xml';
      $xmlstring = $request->get('xmleditor');
      $dom = CSDB::importDocument('', '', trim($xmlstring), '', 'tes'); // akan false jika tidak bisa jad DOM
    }

    // return false jika dom tidak bisa di buat
    if ($type == 'xml' and !$dom) {
      return back()->withInput()->with(['result' => 'fail'])->withErrors(CSDB::get_errors(true, $proccessid), 'info');
    } elseif ($dom) {
      // validation: return false jika rootname buka dmodule ataupun pm
      if (!($validateRootname = CSDB::validateRootname($dom))) {
        return back()->withInput()->with(['result' => 'fail'])->withErrors(CSDB::get_errors(true, 'validateRootname'), 'info');
      }
      $csdb_filename = $validateRootname[1];
      $ident = $validateRootname[2];
      $path = "csdb/{$project->name}";
    } elseif ($type = 'multimedia') {
      $csdb_filename = $request->file('entity')->getClientOriginalName();
      $ident = 'infoEntity';
      $path = "csdb/{$project->name}";
      preg_match("/ICN\-[A-Z0-9]{5}\-[A-Z0-9]{5,10}\-[0-9]{3}\-[0-9]{2}.[a-z]+/", $csdb_filename, $matches);
      if (empty($matches)) {
        return back()->withInput()->with(['result' => 'fail'])->withErrors(["{$csdb_filename} is not match with pattern: ICN\-[A-Z0-9]{5}\-[A-Z0-9]{5,10}\-[0-9]{3}\-[0-9]{2}.[a-z]+"], 'info');
      }
    }

    // writing: return true or false jika ada/tidak file existing
    if (Storage::disk('local')->exists($path . DIRECTORY_SEPARATOR . $csdb_filename)) {
      return back()->withInput()->with(['result' => 'fail'])->withErrors(["{$csdb_filename} is already existed."], 'info');
    } elseif ($csdb_filename) {

      // validate referenced dmrl       
      if (!(($r = $this->validateByDMRL($request->get('dmrl'), $csdb_filename, $ident))[0])) {
        return back()->withInput()->with(['result' => 'fail'])->withErrors($r[1], $r[2] ?? 'default');
      }

      // saving
      $saved = false;
      if ($type == 'xml') {
        Storage::disk('local')->put($path . DIRECTORY_SEPARATOR . $csdb_filename, $xmlstring);
        $saved = true;
      } elseif ($type == 'multimedia') {
        $request->file('entity')->storeAs($path, $csdb_filename);
        $saved = true;
      }
      if ($saved) {
        ModelsCsdb::create([
          'filename' => $csdb_filename,
          'path' => $path,
          'description' => $request->get('description'),
          'status' => 'new',
          'editable' => 1,
          'initiator_id' => $request->user()->id,
          'project_name' => $project->name,
        ]);

        return back()->withInput()->with(['result' => 'success'])->withErrors(["saved with filename: {$csdb_filename}"], 'info');
      }
      return back()->withInput()->with(['result' => 'fail'])->withErrors(["Error while processing {$csdb_filename}"]);
    } else {
      // return $this->result(false, []);
      return back()->withInput()->with(['result' => 'fail']);
    }
  }

  public function postupdate(Request $request)
  {
    // harusnya ga pakai id, karena filename itu unique jadi pakai filename.
    $csdb_object = ModelsCsdb::find($request->get('id'));

    // validasi existing and editable
    if (!$csdb_object) return back()->withInput()->with(['result' => 'fail'])->withErrors(["{$csdb_object->filename} is not exist. You may to go to CREATE page to build one."], 'info');
    if (!$csdb_object->editable) return back()->withInput()->with(['result' => 'fail'])->withErrors(["{$csdb_object->filename} is not enable to edit."], 'info');
    if (str_contains($csdb_object->path, "__")) return back()->withInput()->with(['result' => 'fail'])->withErrors(["{$csdb_object->filename} with status {$csdb_object->status} cannot be modified. It must be returned to used csdb."], 'info');

    $path = "csdb/{$csdb_object->project->name}";

    // validate initiator
    if ($request->user()->id != $csdb_object->initiator_id) {
      return back()->withInput()->with(['result' => 'fail'])->withErrors(["{$csdb_object->path} Only Initator which can be update this CSDB object."], 'info');
    }

    $entity = $request->file('entity');
    if ($entity and str_contains($entity->getMimeType(), 'text')) {
      $request->replace(['xmleditor' => file_get_contents($entity->getPathname())]);
      $entity = false;
    }
    // update by xmleditor
    if ($xmlstring = $request->get('xmleditor')) {
      $proccessid = CSDB::$processid = self::class . "::update";
      $dom = CSDB::importDocument('', '', trim($xmlstring));
      if (!$dom) {
        return back()->withInput()->with(['result' => 'fail'])->withErrors(CSDB::get_errors(true, $proccessid), 'info');
      }

      // validate rootname pm or dm, sekaligus mendapatkan filename nya juga
      if (!($validateRootname = CSDB::validateRootname($dom))) {
        return back()->withInput()->with(['result' => 'fail'])->withErrors(CSDB::get_errors(true, 'validateRootname'), 'info');
      }
      $csdb_filename = $validateRootname[1];

      // validate sequencial attribbute inWork
      $old_issueInfo = CSDB::importDocument(storage_path("app" . DIRECTORY_SEPARATOR . $csdb_object->path . DIRECTORY_SEPARATOR), $csdb_object->filename)->getElementsByTagName('issueInfo')[0];
      $old_inwork = $old_issueInfo->getAttribute('inWork');
      $old_issueNumber = $old_issueInfo->getAttribute('issueNumber');
      $new_issueInfo = $dom->getElementsByTagName('issueInfo')[0];
      $new_inwork = $new_issueInfo->getAttribute('inWork');
      $new_issueNumber = $new_issueInfo->getAttribute('issueNumber');
      if ($old_issueNumber != $new_issueNumber) {
        // fail, m: you cannot update issueNumber at here.
        return back()->withInput()->with(['result' => 'fail'])->withErrors(['you cannot update issueNumber of data module identification here.'], 'info');
      }
      if ($old_inwork != $new_inwork) {
        if ($new_inwork - $old_inwork != 1) {
          return back()->withInput()->with(['result' => 'fail'])->withErrors(['the inwork number of data module identification must be increment of 1.'], 'info');
        }
      }

      // validate Schema
      if ($request->get('xsi_validate')) {
        CSDB::validate('XSI', $dom);
        if (CSDB::get_errors(false, 'validateBySchema')) {
          return back()->withInput()->with(['result' => 'fail'])->withErrors(CSDB::get_errors(true, 'validateBySchema'), 'info');
        }
      }

      // validate Brex
      if ($request->get('brex_validate') == 'on') {
        CSDB::validate('BREX', $dom, null, storage_path("app/{$path}"));
        if (CSDB::get_errors(false, 'validateByBrex')) {
          return back()->withInput()->with(['result' => 'fail'])->withErrors(CSDB::get_errors(true, 'validateByBrex'), 'info');
        }
      }

      // saving to database
      $old_name = $csdb_object->filename;
      $old_path = $csdb_object->path;
      $old_status = $csdb_object->status;
      // dd($old_path, $path, $old_name, $csdb_filename);
      if ($old_path == $path and $old_name == $csdb_filename) {
        $csdb_object->update(array_merge($request->all(), ['status' => 'modified']));
        Storage::disk('local')->put($path . DIRECTORY_SEPARATOR . $csdb_filename, $xmlstring);
      } else {
        // validate referenced dmrl, dilakukan jika filename (tidak termasuk issueNumber dan inWork) nya berbeda dengan yang lama
        if (preg_replace("/_\d{3,5}-\d{2}|_[A-Za-z]{2,3}-[A-Z]{2}/", '', $old_name) != preg_replace("/_\d{3,5}-\d{2}|_[A-Za-z]{2,3}-[A-Z]{2}/", '', $csdb_filename)) { // untuk membersihkan inwork dan issue number pada filename
          $ident = $validateRootname[2];
          if (!(($r = $this->validateByDMRL($request->get('dmrl'), $csdb_filename, $ident))[0])) {
            return back()->withInput()->with(['result' => 'fail'])->withErrors($r[1], $r[2] ?? 'default');
          }
        }

        ModelsCsdb::create([
          'filename' => $csdb_filename,
          'path' => $path,
          'status' => 'new',
          'description' => '',
          'editable' => 1,
          'initiator_id' => $request->user()->id,
          'project_name' => $csdb_object->project->name,
        ]);

        // saving to local
        // moving old file
        $csdb_object->update(['path' => $old_path . "/__unused", 'status' => 'unused']);
        Storage::disk('local')->move($old_path . DIRECTORY_SEPARATOR . $old_name, $old_path . "/__unused" . DIRECTORY_SEPARATOR . $old_name);
        // create new file
        Storage::disk('local')->put($path . DIRECTORY_SEPARATOR . $csdb_filename, $xmlstring);
      }

      $url = preg_replace("/filename=.+.xml/", "filename={$csdb_filename}", back()->getTargetUrl());
      return back()->setTargetUrl($url)->withInput()->with(['result' => 'success'])->withErrors(["saved with filename: {$csdb_filename}"], 'info');
    }
    // update by entity jika mime bukan text
    elseif ($entity) {
      // tidak perlu validate referenced dmrl karena ini update entity sehingga pasti filename nya sama

      // validate Brex
      if ($request->get('brex_validate') == 'on') {
        CSDB::validate('BREX-NONCONTEXT', $csdb_object->path . DIRECTORY_SEPARATOR . $csdb_object->filename, null, storage_path("app"));
        if (CSDB::get_errors(false, 'validateByBrex')) {
          return back()->withInput()->with(['result' => 'fail'])->withErrors(CSDB::get_errors(true, 'validateByBrex'), 'info');
        }
      }
      // saving to database
      $csdb_object->update(array_merge($request->all(), ['status' => 'modified']));

      // saving to local
      $entity->storeAs($path, $csdb_object->filename);
      return back()->withInput()->with(['result' => 'success'])->withErrors(["{$csdb_object->filename} has been deleted from local disk."], 'info');
    }
    // update ex: description or others if entity or xmleditor is empty
    else {
      $csdb_object->update(array_merge($request->all(), ['status' => 'modified']));
      return back()->withInput()->with(['result' => 'success'])->withErrors(["{$csdb_object->filename} has been updated."], 'info');
    }
  }

  public function getdelete(Request $request)
  {
    $user_id = $request->user()->id;
    $filename = $request->get('filename');
    $csdb_model = ModelsCsdb::where('filename', $filename);

    // validation initator id
    if ($csdb_model->first(['initiator_id'])->initiator_id != $user_id) return back()->withInput()->with(['result' => 'fail'])->withErrors(["Only the initiator can delete the DM."], 'info');

    // update table project
    $pr = DB::table('project')->where('csdbs', 'like', "%{$csdb_model->first()->id}%")->first();
    if ($pr) {
      $pr->csdbs = str_replace($csdb_model->first()->id, '', $pr->csdbs);
      DB::table('project')->updateOrInsert(['id' => $pr->id], collect($pr)->toArray());
    }

    // update storage
    $cm = $csdb_model->first();
    Storage::disk('local')->move($cm->path . DIRECTORY_SEPARATOR . $filename, "csdb/{$cm->project->name}/__deleted/{$filename}");
    $cm = $csdb_model->update(['status' => 'deleted', 'path' => "csdb/{$cm->project->name}/__deleted"]);

    // update table csdb
    $csdb_model->update([
      'status' => 'deleted'
    ]);

    return back()->withInput()->with(['result' => 'success'])->withErrors(["{$filename} has been deleted from local disk.",], 'info');
  }

  public function getrestore(Request $request)
  {
    $filename = $request->get('filename');
    $csdb_model = ModelsCsdb::where('filename', $filename);

    $cm = $csdb_model->first();
    Storage::disk('local')->move($cm->path . DIRECTORY_SEPARATOR . $filename, "csdb/{$cm->project->name}/{$filename}");
    $csdb_model->update(['status' => 'modified', 'path' => "csdb/{$cm->project->name}"]);

    return back()->withInput()->with(['result' => 'success'])->withErrors(["{$filename} has been restored to project {$cm->project->name}.",], 'info');
  }

  public function postdelete(Request $request)
  {
    $filename = $request->get('filename');
    $csdb_model = ModelsCsdb::where('filename', $filename)->first();

    Storage::disk('local')->delete("{$csdb_model->path}/{$csdb_model->filename}");
    $csdb_model->delete();
    return back()->withInput()->with(['result' => 'success'])->withErrors(["{$filename} has been HARD deleted.",], 'info');
  }

  public function index(Request $request)
  {
    $query = array();
    foreach ($request->all() as $k => $v) {
      switch ($k) {
        case 'mic':
          if ($v == 'entity') {
            $query[] = ['filename', 'like', "%ICN-%"];
          } else {
            $v = strtoupper($v);
            $query[] = ['filename', 'like', "%{$v}%"];
          }
          break;
        case 'status':
          $query[] = ['status', '=', $v];
          break;
        case 'initiator':
          $query[] = ['initiator_id', '=', User::where('email', '=', $v)->first('id')->id];
          break;
      }
    }
    foreach ($query as $q) {
      if (isset($lists)) {
        $lists->where($q[0], $q[1], $q[2]);
      } else {
        $lists = ModelsCsdb::where($q[0], $q[1], $q[2]);
      }
    }
    isset($lists) ? ($lists = $lists->get()) : null;
    return  view('csdb.index', [
      'listsobj' => $lists ?? null,
      'table' => 'csdb',
    ]);
  }

  public function getcreate(Request $request)
  {
    return view('csdb.create', $request->all());
  }

  public function getupdate(Request $request)
  {
    // untuk generate URL
    if ($entity = $request->get('entity')) {
      $csdb_object = ModelsCsdb::where('filename', 'like', "%{$entity}")->first(['filename', 'path']);
      $file = Storage::get($csdb_object->path . DIRECTORY_SEPARATOR . $csdb_object->filename);
      $mime = Storage::mimeType($csdb_object->path . DIRECTORY_SEPARATOR . $csdb_object->filename);
      if (str_contains($mime, 'image')) {
        $scale = $request->get('scale') ?? 50;
        $file = new ImageResize(storage_path("app/csdb/{$entity}"));
        $file->scale($scale);
        $file = $file->output();
      }
      $r = Response::make($file, 200, ['Content-Type' => $mime]);
      return $r;
    }


    $filename = $request->get('filename');
    if (!$filename) {
      return back();
    }
    $csdb_object = ModelsCsdb::where('filename', 'like', "%{$filename}")->first(['id', 'filename', 'path', 'description', 'initiator_id']);
    $mime = Storage::mimeType($csdb_object->path . DIRECTORY_SEPARATOR . $csdb_object->filename);
    $data = [
      'id' => $csdb_object->id,
      'description' => $csdb_object->description,
      'initiator' => $csdb_object->initiator->email,
    ];
    if (str_contains($mime, "text")) {
      $data = array_merge($data, ['xmleditor' => Storage::get($csdb_object->path . DIRECTORY_SEPARATOR . $csdb_object->filename)]);
    } else {
      $data = array_merge($data, ['use_xmleditor' => false, 'entitysrc' => route('get_update_csdb_object') . "?entity={$filename}&scale={$request->get('scale')}"]);
    }
    // return view dan text
    if ($csdb_object) {
      return view('csdb.update', $data);
    } else {
      return back();
    }
  }

  /**
   * jika $object type tidak termasuk bagian yang perlu di validasi dmrl, maka dianggap true
   */
  public function validateByDMRL(string $dmrlfilename = null, string $object_name = '', string $object_type)
  {
    if (!in_array($object_type, ['dmodule', 'pm', 'infoEntity', 'comment', 'dml'])) {
      return [true, ''];
    }
    if ($dmrlfilename == '' or !($dmrl = ModelsCsdb::where('filename', $dmrlfilename)->first(['id', 'filename', 'path'])) or !(Storage::exists($dmrl->path . "/" . $dmrl->filename))) {
      return [false, ["No such DMRL"], 'info'];
    } else {
      $dmrl_dom = CSDB::importDocument(storage_path("app/{$dmrl->path}/"), $dmrl->filename);
      if (!CSDB::validate('XSI', $dmrl_dom, 'dml.xsd')) {
        $err = CSDB::get_errors(true, 'validateBySchema');
        return [false, ["dmrl" => array_merge(["DMRL must be comply to dml.xsd"], $err)]]; // key 'dmrl' ini adalah input name pada HTML
      }
      $xpath = new \DOMXPath($dmrl_dom);
      $dmlEntries = $xpath->evaluate("//dmlEntry");
      $nominal_idents = array();
      foreach ($dmlEntries as $key => $dmlEntry) {
        $ident = str_replace("Ref", '', $dmlEntry->firstElementChild->tagName);
        if ($dmlEntry->firstElementChild->tagName == 'infoEntityRef') {
          $nominal_idents[] = $dmlEntry->firstElementChild->getAttribute('infoEntityRefIdent');
        } else {
          $nominal_idents[] = call_user_func_array(CSDB::class . "::resolve_{$ident}Ident", [$dmlEntry->getElementsByTagName("{$ident}RefIdent")[0]]);
        }
      }
      $actual_ident = preg_replace("/_\d{3,5}-\d{2}|_[A-Za-z]{2,3}-[A-Z]{2}/", '', $object_name); // untuk membersihkan inwork dan issue number pada filename
      if (!in_array($actual_ident, $nominal_idents)) {
        $actual_ident = preg_replace('/\.\w+$/', '', $actual_ident);
        return [false, ["{$actual_ident} is not required by the DMRL."], 'info'];
      }
      return [true, ''];
    }
  }

  public function getdetail(Request $request)
  {
    if (!($filename = $request->get('filename'))) {
      return back();
    }

    $csdb_model = ModelsCsdb::where('filename', $filename)->first();
    if (!isset($csdb_model->id)) {
      abort(403, "{$filename} is not available in database.");
    }

    return view('csdb.detail', [
      'object' => $csdb_model,
      'id' => $csdb_model->id,
      'filename' => $filename,
    ]);

    // $csdb_model = ModelsCsdb::where('filename',$filename)->first();
    // $file = Storage::get("{$csdb_model->path}/{$csdb_model->filename}");

  }


  public function request(Request $request)
  {
    $csdb = ModelsCsdb::where('filename', $request->get('filename'))->first(['path', 'filename', 'project_name']);
    $file = Storage::get("{$csdb->path}/{$csdb->filename}");
    return response()->json([
      'csdb' => $file
    ], 200);
  }

  // public function fail(array $messages = [], $inputName = ''){
  //   if(!empty($inputName)){
  //    $res = [
  //       'result' => 'fail',
  //       'error' => [
  //         $inputName => $messages,
  //       ]
  //     ];
  //   }
  //   else {
  //     $res = [
  //       'result' => 'fail',
  //       'messages' => $messages,
  //     ];
  //   }

  //   return back()->withInput()->with($res);
  // }

  // /**
  //  * if you want to return message with input @name, please attach input @name as key, and the value contains array of text message. eg. argument#2 = ["dmrl" => ['DMRL column is required']]
  //  */
  // public function result(bool $result, array $messages, array $errors = [])
  // {

  //   $r = [
  //     'result' => $result ? 'success' : 'fail',
  //     'messages' => $messages,
  //   ];
  //   // $e = new MessageBag(['tes']);
  //   // return back()->withInput()->with($r)->withErrors(['dmrl' => ['bar','asa']]); // 'foo' akan dibaca sebagai input@name, ['bar','asa'] adalah valuenya. Bisa juga string saja eg.:'bar'
  //   // return back()->withInput()->with($r)->withErrors(['foo','bar'],'top')->withErrors(['foo','bar']);
  //   // return back()->withInput()->with($r)->withErrors($errors);
  //   // dd(new MessageBag(['foo']));

  //   dd($r);
  //   return back()->withInput()->with($r);
  //   $c = function()use($r){
  //     return back()->withInput()->with($r);    
  //   };
  //   // $e = new MessageBag(['tes']);
  //   return $c();
  // }

}
