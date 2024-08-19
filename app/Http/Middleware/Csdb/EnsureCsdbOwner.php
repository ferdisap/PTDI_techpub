<?php

namespace App\Http\Middleware\Csdb;

use App\Models\Csdb;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCsdbOwner
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    // if($request->route('filename')){
      // $request->CSDBModel = Csdb::getCsdb($request->route('filename'))
    // }

    return $next($request);
  }
}
