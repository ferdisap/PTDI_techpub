<?php

namespace App\Http\Controllers;

use App\Models\Csdb as ModelsCsdb;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rules\File;
use Ptdi\Mpub\CSDB;

class CsdbController extends Controller
{
  private function validateRootname($dom)
  {
    $rootname = $dom->firstElementChild->tagName;
    if($rootname == 'dmodule'){
      $csdbIdent = $dom->getElementsByTagName('dmIdent')[0];
      $csdb_filename = CSDB::resolve_dmIdent($csdbIdent);
      $ident = 'dmCode';
    } 
    elseif ($rootname == 'pm'){
      $csdbIdent = $dom->getElementsByTagName('pmIdent')[0];
      $csdb_filename = CSDB::resolve_pmIdent($csdbIdent);
      $ident = 'pmCode';
    } 
    else {
      return back()->withInput()->with([
        'result' =>'fail',
        'messages' => [
          0 => 'CSDB cannot identified as PM or DM.'
        ],
      ]);
    }
    return [$csdbIdent, $csdb_filename, $ident];
  }

  /**
   * mendahulukan uploaded file, baru editor
   */
  public function create(Request $request)
  {
    $dom = null;

    $modelIdentCode = strtolower($request->get('modelIdentCode')); // supaya orang aware tidak asal buat DM
    $proccessid = CSDB::$processid = self::class."::create";

    // set type and get dom if xml, by the string or file upload
    $type = '';
    $file = $request->file('entity');    
    if($file AND in_array($file->getMimeType(),array('text/xml', 'text/plain'))){
      $type = 'xml';
      $xmlstring = file_get_contents($file->getPathname());
      $dom = CSDB::importDocument($file->getPathname());
    } 
    elseif ($file AND in_array($file->getMimeType(),array('image/jpg', 'image/jpeg', 'image/png', 'image/svg'))){
      $type = 'multimedia';
    }
    else {
      $type = 'xml';
      $xmlstring = $request->get('xmleditor');
      $dom = CSDB::importDocument('',$xmlstring); // akan false jika tidak bisa jad DOM
    }

    // return false jika dom tidak bisa di buat
    if($type == 'xml' AND !$dom){
      return $this->fail(CSDB::get_errors(true, $proccessid));
    }
    elseif($dom){
      // validation: return false jika rootname buka dmodule ataupun pm
      $validateRootname = $this->validateRootname($dom);
      $csdbIdent = $validateRootname[0];
      $csdb_filename = $validateRootname[1];
      $ident = $validateRootname[2];

      // validation: return false jika model ident code tidak sesuai dengan radio button
      if($modelIdentCode != strtolower($csdbIdent->getElementsByTagName($ident)[0]->getAttribute('modelIdentCode'))){
        return $this->fail(['Your modelIdentCode in editor and in radio button is not same.']);
      }
      $path = "csdb/{$modelIdentCode}/{$csdb_filename}";
    }
    elseif($type = 'multimedia'){
      $csdb_filename = $request->file('entity')->getClientOriginalName();
      $path = "csdb/multimedia/{$csdb_filename}";
      $modelIdentCode = true;
      preg_match("/ICN\-[A-Z0-9]{5}\-[A-Z0-9]{5,10}\-[0-9]{3}\-[0-9]{2}.[a-z]+/",$csdb_filename,$matches);
      if(empty($matches)){
        return $this->fail(["{$csdb_filename} is not match with pattern: ICN\-[A-Z0-9]{5}\-[A-Z0-9]{5,10}\-[0-9]{3}\-[0-9]{2}.[a-z]+"]);
      }
    }

    // writing: return true or false jika ada/tidak file existing
    if(Storage::disk('local')->exists($path)){
      return $this->fail(["{$csdb_filename} is already existed."]);
    }
    elseif($csdb_filename AND $modelIdentCode){
      $saved = false;
      if($type == 'xml') {
        Storage::disk('local')->put($path, $xmlstring);
        $saved = true;
      }
      elseif($type == 'multimedia'){
        $saved = true;
        $request->file('entity')->storeAs('csdb/multimedia',$csdb_filename);
      }
      if($saved){
        ModelsCsdb::create([
          'path' => "{$path}",
          'description' => $request->get('description'),
          'status' => 'new',
          'initiator_id' => $request->user()->id,
        ]);
        return back()->withInput()->with([
          'result' => 'success',
          'messages' => ["saved with filename: {$csdb_filename}"]
        ]);
      }
      return $this->fail(["{$csdb_filename} has been existed. You may to go to EDIT page if you want."]);
    } else {
      return $this->fail();
    }
  }

  public function update(Request $request)
  {
    $xmlstring = $request->get('xmleditor');
    $proccessid = CSDB::$processid = self::class."::update";
    $dom = CSDB::importDocument('',$xmlstring);
    
    if(!$dom){
      return $this->fail(CSDB::get_errors(true, $proccessid));
    }   
    
    // return false jika rootname buka dmodule ataupun pm
    $validateRootname = $this->validateRootname($dom);
    // $csdbIdent = $validateRootname[0]; // tidak dipakai
    $csdb_filename = $validateRootname[1];
    $ident = $validateRootname[2];
    
    $modelIdentCode = $dom->getElementsByTagName($ident)[0]->getAttribute('modelIdentCode');

    if($csdb_filename AND Storage::disk('local')->exists("csdb/{$modelIdentCode}/{$csdb_filename}")){
      Storage::disk('local')->put("csdb/{$modelIdentCode}/{$csdb_filename}", $xmlstring);
      return back()->withInput()->with([
        'result' => 'success',
        'messages' => [
          0 => "saved with filename: {$csdb_filename}",
        ]
      ]);
    }
    else {
      return $this->fail(["{$csdb_filename} is not exist. You may to go to CREATE page to build one."]);
    }
  }

  public function delete(Request $request)
  {
    $user_id = $request->user()->id;
    $filename = $request->get('filename');
    $csdb_object = ModelsCsdb::where('path', 'like', "%{$filename}");

    if($csdb_object->first(['initiator_id'])->initiator_id != $user_id){
      return $this->fail(["Only the initiator can delete the DM."]);
    }
    
    if($csdb_object AND Storage::exists($csdb_object->first(['path','id'])->path)){
      Storage::delete($csdb_object->first(['path','id'])->path);
      $csdb_object->update([
        'status' => 'deleted'
      ]);

      return back()->withInput()->with([
        'result' => 'success',
        'messages' => [
          0 => "{$filename} has been deleted from local disk.",
        ]
      ]);
    }
    else {
      return $this->fail();
    }
  }

  private function fail(array $messages = []){
    return back()->withInput()->with([
      'result' => 'fail',
      'messages' => $messages,
    ]);
  }
}
