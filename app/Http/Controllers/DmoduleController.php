<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ptdi\Mpub\Object\DModule;

class DmoduleController extends Controller
{
  public function indexDModule()
  {
    return view('dmodule.dmodule_index', [
      'title' => 'DModule index'
    ]);
  }

  public function validate(Request $request)
  {
    if($dm_file = $request->file('dm_file')){
      if($dm_file->getMimeType() == 'text/xml'){
        // dd($dm_file->getPathname());
        // $dm = file_get_contents($dm_file->getPathname());
        // dd($dm, $dm_file);
        $dmodule = new DModule($dm_file->getPathname());
        $dmodule->resolve();

        dd($dmodule);
      }
    }
    dd($request->all());
    return 'foo';
    dd($request->filename);
  }
}
