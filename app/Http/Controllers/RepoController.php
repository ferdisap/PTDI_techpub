<?php

namespace App\Http\Controllers;

use App\Models\Csdb;
use App\Models\Project;
use App\Models\Repo;
use App\Models\RepoObjectDMC;
use App\Models\RepoObjectPMC;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ptdi\Mpub\CSDB as MpubCSDB;
use Ptdi\Mpub\ICNDocument;
use Symfony\Component\Uid\Ulid;
use XSLTProcessor;

class RepoController extends Controller
{
  // public function get(Request $request)
  // {
  //   $repo = Repo::where('token', base64_encode($request->get('token')))->first();
  //   dd($repo);
  // }

  public function getindex(Request $request)
  {
    return view('repo.index');
  }

  public function getdelete(Request $request)
  {
  }

  public function getcreate(Request $request)
  {
    // validation password
    if (!$request->get('token')) return back()->withInput()->with(['result' => 'fail'])->withErrors(['token' => 'token is required.'])->withErrors(["Failed to create repo."], 'info');

    // validation project name
    if (!($project = Project::find($request->get('project_name')))) return back()->withInput()->with(['result' => 'fail'])->withErrors(["Failed to create repo.",], 'info');;

    $csdbs = $project->csdb;
    $csdbs = $csdbs->map(function ($item) {
      $item->prefix = explode('-', $item->filename)[0];
      switch ($item->prefix) {
        case 'PMC':
          $doc = MpubCSDB::importDocument(storage_path("app/{$item->path}/"), $item->filename);
          $item->pt = $doc->documentElement->getAttribute('pmType');
          $item->title = MpubCSDB::resolve_pmTitle($doc->getElementsByTagName('pmTitle')[0]);
          $item->issuedate = MpubCSDB::resolve_issueDate($doc->getElementsByTagName('issueDate')[0]);
          $item->sc = MpubCSDB::resolve_securityClassification($doc);
          break;
        case 'DMC':
          $doc = MpubCSDB::importDocument(storage_path("app/{$item->path}/"), $item->filename);
          $item->title = MpubCSDB::resolve_dmTitle($doc->getElementsByTagName('dmTitle')[0]);
          $item->issuedate = MpubCSDB::resolve_issueDate($doc->getElementsByTagName('issueDate')[0]);
          $item->schema = MpubCSDB::getSchemaUsed($doc, 'filename');
          $item->sc = MpubCSDB::resolve_securityClassification($doc);
      }
      return $item;
    });

    $reponame = $project->name . "_" . Carbon::now()->toDateString() . "_" . Str::random(10);
    // rawan tokennya sama, sehingga buat dulu Repo model, baru storage dipindahkan
    $repo = Repo::create([
      'name' => $reponame,
      'path' => "repo/{$reponame}",
      'project_name' => $project->name,
      'token' => base64_encode($request->get('token')),
    ]);
    foreach ($csdbs as $csdb) {
      $oldpathname = $csdb->path . "/" . $csdb->filename;
      $newpathname = "repo/{$reponame}/{$csdb->filename}";
      Storage::copy($oldpathname, $newpathname);
      if ($csdb->prefix == 'PMC') {
        RepoObjectPMC::create([
          'repo_id' => $repo->id,
          'filename' => $csdb->filename,
          'pt' => $csdb->pt,
          'title' => $csdb->title,
          'issuedate' => $csdb->issuedate,
          'sc' => $csdb->sc,
        ]);
      } elseif ($csdb->prefix == 'DMC') {
        RepoObjectDMC::create([
          'repo_id' => $repo->id,
          'filename' => $csdb->filename,
          'title' => $csdb->title,
          'issuedate' => $csdb->issuedate,
          'schema' => $csdb->schema,
          'sc' => $csdb->sc,
        ]);
      }
    }

    return back();
  }

  ############# API #############
  private function fail($code, $messages = [])
  {
    return response()->json([
      'messages' => $messages,
    ], $code);
  }

  private function addRepos(array $repo)
  {
    $this->repos = $this->repos ?? [];
    $this->repos[] = $repo;
    return $this->repos;
  }

  private function getRepos()
  {
    return $this->repos ?? [];
  }

  public function provide_repo(Request $request)
  {
    if ($tokenRepo = $request->get('tokenRepo')) {
      $encrypter = app(\Illuminate\Contracts\Encryption\Encrypter::class);
      $token = base64_encode($tokenRepo);
      $repos = Repo::where('token', $token)->get(['name', 'created_at']);
      $code = count($repos) > 0 ? 200 : 400;
      foreach ($repos as $repo) {
        $this->addRepos(['name' => $repo->name, 'created_at' => $repo->created_at]);
      }
      return response()->json([
        'repos' => $this->getRepos(),
      ], $code)->withCookie(cookie('tokenRepo', $tokenRepo, 5, null, null, null, false, false));
    } else {
      return $this->fail(400, ['no such repo available']);
    }
  }

  public function provide_repo_object(Request $request, $repoName)
  {
    $repo = Repo::with(['pmc', 'dmc'])->where('name', $repoName)->first();
    $tokenRepo = base64_encode(Cookie::get('tokenRepo'));
    if ($tokenRepo != $repo->token) return $this->fail(400, ['You should put the true repo token.']);
    
    $this->addRepos(['name' => $repo->name, 'objects' => array_merge($repo->pmc->toArray(), $repo->dmc->toArray())]);
    return response()->json([
      'repos' => $this->getRepos(),
    ], 200);
  }

  public function provide_object_detail(Request $request, Repo $repo, $filename)
  {
    $tokenRepo = base64_encode(Cookie::get('tokenRepo'));
    if ($tokenRepo != $repo->token) return $this->fail(400, ['You should put the true repo token.']);

    $object = new Csdb();
    $object->repoName = $repo->name;
    $csdb = MpubCSDB::importDocument(storage_path("app/{$repo->path}/"), $filename);
    if ($csdb instanceof ICNDocument) {
      return response($csdb->getFile(''), 200, [
        'Content-Type' => $csdb->getFileinfo()['mime_type']
      ]); 
    }
    $object->DOMDocument = $csdb;
    $transformed = $object->transform_to_xml(resource_path("views/ietm/xsl"));

    $this->addRepos([
      'name' => $repo->name,
      'objects' => [
        [
          'name' => $filename,
          'transformed_html' => $transformed,
        ]
      ],
    ]);
    return response()->json([
      'repos' => $this->getRepos(),
    ], 200);
  }

  public function handle_pmc(Request $request, $filename)
  {
    $object = RepoObjectPMC::with('repo')->where('filename', $filename)->first();
    $utilize = $request->get('utilize') ?? 'handle-pmEntry';
    if ($utilize == 'handle-pmEntry') {
      return $object->pmEntry();
    }
  }
}
