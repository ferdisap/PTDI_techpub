<?php

namespace App\Http\Controllers;

use App\Models\Csdb;
use App\Models\Project;
use App\Models\Repo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Uid\Ulid;

class RepoController extends Controller
{
  public function get(Request $request)
  {
    $repo = Repo::where('token',base64_encode($request->get('token')))->first();
    dd($repo);
  }

  public function getcreate(Request $request)
  {
    // validation password
    if(!$request->get('token')) return back()->withInput()->with(['result' => 'fail'])->withErrors(['token' => 'token is required.'])->withErrors(["Failed to create repo."], 'info');

    // validation project name
    if(!($project = Project::find($request->get('project_name')))) return back()->withInput()->with(['result' => 'fail'])->withErrors(["Failed to create repo.",], 'info');;

    $csdbs = $project->csdb;
    $reponame = $project->name."_".Carbon::now()->toDateString()."_".Str::random(10);
    // rawan tokennya sama, sehingga buat dulu Repo model, baru storage dipindahkan
    Repo::create([
      'name' => $reponame,
      'path' => "repo/{$reponame}",
      'project_name' => $project->name,
      'token' => base64_encode($request->get('token')),
    ]);
    foreach ($csdbs as $csdb) {
      $oldpathname = $csdb->path."/".$csdb->filename;
      $newpathname = "repo/{$reponame}/{$csdb->filename}";
      Storage::copy($oldpathname, $newpathname);
    }
    return back();
  }
}
