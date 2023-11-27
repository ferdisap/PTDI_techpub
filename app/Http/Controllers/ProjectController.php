<?php

namespace App\Http\Controllers;

use App\Models\Csdb;
use App\Models\Project;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Support\MessageProvider;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
use Ptdi\Mpub\CSDB as MpubCSDB;

class ProjectController extends Controller
{
  public function index(Request $request)
  {
    return view('project.index', [
      'listspr' => Project::all(),
    ]);
  }

  public function getcreate()
  {
    return view('project.index',[
      'create' => true,
    ]);
  }

  public function postcreate(Request $request)
  {
    // validation
    $dmrlIdent = null;
    $validator = Validator::make($request->all(),[
      'name' => ['required'],
      'dmrl' => ['required', 
        File::types('xml'), 
        function(string $attribute, UploadedFile $value, Closure $fail) use (&$dmrlIdent){
          $dmrl = MpubCSDB::importDocument('','',trim($value->openFile()->fread($value->getSize())));
          if(!$dmrl){
            $fail('DMRL cannot be identified as valid XML form.');
          }
          if(($dmlIdent = $dmrl->getElementsByTagName("dmlIdent"))->length == 0){
            $fail("DMRL must have dmlIdent of its identification");
          }
          $dmlIdent = MpubCSDB::resolve_dmlIdent($dmlIdent[0]);
          $dmrlIdent = $dmlIdent;
        }
      ],
    ]);

    if($validator->fails()){
      return back()->withInput()->withErrors($validator)->with(['result' => 'fail']);
    }

    $project = Project::create($request->all());
    $request->file('dmrl')->storeAs("csdb/{$project->name}",$dmrlIdent);
    $dmrl = Csdb::create([
      'filename' => $dmrlIdent,
      'path' => "csdb/{$project->name}",
      'status' => 'new',
      'description' => '',
      'editable' => 1,
      'initiator_id' => $request->user()->id,
      'project_name' => $project->name,
    ]);
    return Redirect::route('index_project');
  }

  public function getassign(Request $request){
    return view('project.index',[
      'assign' => true,
      'name' => $request->get('name'),
    ]);
  }

  public function delete(Request $request)
  {
    if(!$request->get('name')){
      return (new CsdbController())->fail(['You have to add project name.']);
    }
    if(!($pr = Project::find($request->get('name')))){
      return (new CsdbController())->fail(['There is no such project name.']);
    }
    DB::table('csdb_project')->where('project_name',$request->get('name'))->delete();
    $pr->delete();
    return back()->withInput()->with([
      'result' => 'success',
      'messages' => ["Project with name: {$request->get('name')} has been deleted. "]
    ]);
  }

  public function postassign(Request $request)
  {
    $prname = $request->get('name');

    $objects = $request->get('ident');
    $objects = array_map(fn($v) => trim($v),explode(",",$objects));
    
    $pr = Project::with('csdb')->find($prname);
    
    foreach ($objects as $obj){
      if(!($csdb = Csdb::where('path',"csdb/{$obj}")->first(['id']))){
        return (new CsdbController())->fail(["{$obj} is not available in data base."],'ident');
      }
      
      if(!$pr->csdb()->where('path', "csdb/{$obj}")->first()){
        $pr->csdb()->attach($csdb->id);
      }
      return back()->withInput()->with([
        'result' => 'success',
        'messages' => ["{$request->get('ident')} has been attached into {$prname}"],
      ]);
    }
  }

  public function getdetail(Request $request)
  {
    $pr = Project::find($request->get('name'));
    if(!$pr){
      return abort(500);
    }
    return view('project.index',[
      'detail' => true,
      'pr' => $pr,
    ]);
  }

}
