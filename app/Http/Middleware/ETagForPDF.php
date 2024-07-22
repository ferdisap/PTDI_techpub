<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response as FacadesResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * masih belum mengakomodir header If-Match
 */
class ETagForPDF
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    // handle request
    $method = $request->getMethod();

    // support using HEAD method for checking IF-None-Match
    if($request->isMethod('HEAD')) $request->setMethod('GET');
    
    // create ETags
    $CSDBModel = ($request->route('CSDBModel'));
    $etag = '"'.md5($CSDBModel->filename."___".$CSDBModel->updated_at).'"';

    // check If-None-Match
    $noneMatch = $request->getEtags();
    // if(in_array($etag, $noneMatch)) return FacadesResponse::make('',304);
    if(in_array($etag, $noneMatch)) return FacadesResponse::make()->setNotModified();

    // handling response
    $response = $next($request);

    // set ETag to response
    $response->setEtag($etag);

    $request->setMethod($method);

    return $response;
  }
}
