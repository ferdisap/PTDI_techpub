<?php

namespace App\Http\Controllers;

use App\Models\Csdb;
use Closure;
use DOMXPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Ptdi\Mpub\CSDB as MpubCSDB;

class CsdbProcessingController extends CsdbController
{
  /**
   * perlu input@name: verificationType, verification, applicRefId
   */
  public function postverify(Request $request)
  {
    // dd($request->all());
    $validator = Validator::make($request->all(), [
      // 'verification' => ['required', fn(string $attr, $value, Closure $fail) => ($value == 'firstVerification' OR $value == 'secondVerification' OR $value == 'unverified') ? true : $fail("The verification proccess must be firstVerification or secondVerification or unverified.") ],
      'verification' => [
        'required', 
        function(string $attr, $value, Closure $fail) use($request){
          if($value == 'firstVerification' OR $value == 'secondVerification' OR $value == 'unverified'){
            if($value != 'unverified'){
              if(!$request->get('verificationType')){
                $fail("You need to select one of the verification type.");
              }
            }
          } else {
            $fail("The verification proccess must be firstVerification or secondVerification or unverified.");
          }
        }],
      'verificationType' => [
        function(string $attr, $value, Closure $fail) use ($request) {
          if($request->get('verification') == 'unverified'){
            return true;
          }
          return ($value == 'tabtop' OR $value == 'onobject' OR $value == 'ttandoo') ? true : $fail("The verification type is not match with schema rule.");
        }
      ],
    ]);

    if($validator->fails()){
      return back()->withInput()->withErrors($validator)->with(['result' => 'fail']);
    }
    
    $csdb = Csdb::find($request->id);
    $file = Storage::get("{$csdb->path}/{$csdb->filename}");
    $dom = MpubCSDB::importDocument('','',$file);
    $domXpath = new DOMXPath($dom);
    
    // validate applicRefId
    if( ($applicRefId = $request->get('applicRefId')) AND ($domXpath->evaluate("//applic[@id='{$applicRefId}']"))->length == 0 ){
      return back()->withInput()->with(['result' => 'fail'])->withErrors(['applicRefId' => "There is no such {$applicRefId} of applicRefId."]);
    }

    // find <qualityAssurance> with applicRefId or not
    $xpath = "//qualityAssurance";
    $xpath .= ($applicRefId) ? "[@applicRefId='{$applicRefId}']" : '';
    $qa = $domXpath->evaluate($xpath);

    // make <qualityAssurance> if it is not found
    if($qa->length != 0){
      $qa = $qa[0];
    } else {
      $qa = $dom->createElement("qualityAssurance");
      $qa->setAttribute("applicRefId", $applicRefId);
      
      // add <qualityAssurance applicRefId=""> ke dalam <(?)Status>
      $q = $domXpath->evaluate("//qualityAssurance")[0];
      $status = $dom->getElementsByTagName("{$dom->firstElementChild->tagName}Status")[0];
      $status->insertBefore($qa,$q);
    }

    // make element <firstVerification> or <secondVerification> or <unverified>
    $verification = $dom->createElement($request->get('verification'));
    if($request->get('verification') != 'unverified'){
      $verification->setAttribute("verificationType", $request->get('verificationType'));
    }

    // remove child inside <qualityAssurance>
    while ($ch = $qa->firstElementChild){
      $ch->remove();
    }
    $qa->appendChild($verification);
    $dom->save(storage_path("app/{$csdb->path}/{$csdb->filename}"));
    return back()->withInput()->with(['result' => 'success'])->withErrors(["The verification has been updated"],'info');
  }
}
