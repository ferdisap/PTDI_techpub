<?php

namespace App\Http\Controllers;

use App\Models\Csdb;
use Closure;
use DOMNodeList;
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
    $csdb_model = Csdb::find($request->id);
    $schema_xml = MpubCSDB::getSchemaUsed(MpubCSDB::importDocument(storage_path("app/{$csdb_model->path}/"), $csdb_model->filename));
    $schema_xpath = new DOMXPath($schema_xml);
    $qa_schema = $schema_xpath->evaluate("//xs:element[@ref='qualityAssurance']");
    // dd($qa_schema, $schema_xml, MpubCSDB::importDocument(storage_path("app/{$csdb_model->path}/"), $csdb_model->filename));
    $qa_schema = ($qa_schema->length > 0) ? $qa_schema[0] : null;
    if ($qa_schema) {
      $qa_minOccurs = 1;
      $qa_maxOccurs = $qa_schema->getAttribute('maxOccurs') ?? 1;
    } 
    else {
      return back()->withInput()->with(['result' => 'success'])->withErrors(["No need verification"], 'info');
    }

    $validator = Validator::make($request->all(), [
      // 'verification' => ['required', fn(string $attr, $value, Closure $fail) => ($value == 'firstVerification' OR $value == 'secondVerification' OR $value == 'unverified') ? true : $fail("The verification proccess must be firstVerification or secondVerification or unverified.") ],
      'verification' => [
        'required',
        function (string $attr, $value, Closure $fail) use ($request) {
          if ($value == 'firstVerification' or $value == 'secondVerification' or $value == 'unverified') {
            if ($value != 'unverified') {
              if (!$request->get('verificationType')) {
                $fail("You need to select one of the verification type.");
              }
            }
          } else {
            $fail("The verification proccess must be firstVerification or secondVerification or unverified.");
          }
        }
      ],
      'verificationType' => [
        function (string $attr, $value, Closure $fail) use ($request) {
          if ($request->get('verification') == 'unverified') {
            return true;
          }
          return ($value == 'tabtop' or $value == 'onobject' or $value == 'ttandoo') ? true : $fail("The verification type is not match with schema rule.");
        }
      ],
    ]);

    if ($validator->fails()) {
      return back()->withInput()->withErrors($validator)->with(['result' => 'fail']);
    }

    // $csdb_model = Csdb::find($request->id);
    $file = Storage::get("{$csdb_model->path}/{$csdb_model->filename}");
    $dom = MpubCSDB::importDocument('', '', $file);
    $domXpath = new DOMXPath($dom);

    // validate applicRefId
    if (($applicRefId = $request->get('applicRefId')) and ($domXpath->evaluate("//applic[@id='{$applicRefId}']"))->length == 0) {
      return back()->withInput()->with(['result' => 'fail'])->withErrors(['applicRefId' => "There is no such {$applicRefId} of applicRefId."]);
    }

    // find <qualityAssurance> with applicRefId or not
    if ((isset($qa_minOccurs) and $qa_minOccurs > 0) and (isset($qa_maxOccurs) and $qa_maxOccurs > 1)) {
      $xpath = "//identAndStatusSection/descendant::qualityAssurance";
      $xpath .= ($applicRefId) ? "[@applicRefId='{$applicRefId}']" : '';
      $qa = $domXpath->evaluate($xpath);
    } elseif (isset($qa_maxOccurs) and $qa_maxOccurs == 1) {
      $xpath = "//identAndStatusSection/descendant::qualityAssurance";
      $qa = $domXpath->evaluate($xpath);
    } else {
      $qa = new DOMNodeList;
    }

    // make <qualityAssurance> if it is not found
    if ($qa->length != 0) {
      $qa = $qa[0];
    } else {
      $qa = $dom->createElement("qualityAssurance");
      $qa->setAttribute("applicRefId", $applicRefId);

      // add <qualityAssurance applicRefId=""> ke dalam <(?)Status>
      $q = $domXpath->evaluate("//qualityAssurance")[0];
      $status = $dom->getElementsByTagName("{$dom->firstElementChild->tagName}Status")[0];
      $status->insertBefore($qa, $q);
    }


    // make element <firstVerification> or <secondVerification> or <unverified>
    $verification = $dom->createElement($request->get('verification'));
    if ($request->get('verification') != 'unverified') {
      $verification->setAttribute("verificationType", $request->get('verificationType'));
    }

    // remove child inside <qualityAssurance>
    while ($ch = $qa->firstElementChild) {
      $ch->remove();
    }
    $qa->appendChild($verification);
    $dom->save(storage_path("app/{$csdb_model->path}/{$csdb_model->filename}"));
    return back()->withInput()->with(['result' => 'success'])->withErrors(["The verification has been updated"], 'info');
  }
}
