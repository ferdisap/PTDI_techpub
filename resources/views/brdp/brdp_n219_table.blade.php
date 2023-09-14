@extends('html_head')
@section('styles')
<link rel="stylesheet" href="/css/brdp/table.css">
<link rel="stylesheet" href="/general/general.css">
@endSection
@section('scripts_onTop')
<script src="/general/hideShow.js"></script>
@endsection
@section('body')
<header>
  @include('navbar')

  <div class="container text-center mt-3">
    <h1>N219 Project - BRDP</h1>
    <div class="nav justify-content-center d-flex">
      <a href="#" class="nav-link">S1000D</a>
      <a href="#" class="nav-link disabled">PTDI</a>
      <a href="#" class="nav-link disabled">Contract</a>
      <a href="#" class="nav-link disabled">Customer</a>
    </div>
    
    <div class="container mt-3 d-flex justify-content-center input-group">
      <span class="input-group-text">
        <div class="input-group">
          <input class="me-2" type="checkbox" onclick="Brdp.BrSearch.useApplSchema = this.checked">
          <button class="bg-white" data-bs-toggle="modal" data-bs-target="#applSchema" style="max-width:130px">Select Schema</button>
        </div>
      </span>
      <input class="form-control w-50 flex-inherit" type="text" filterBy="all" placeholder="find everything of business rule.." aria-label="find business rule" onkeypress="Brdp.BrSearch.listener(event)">
      <button class="input-group-text" onclick="Brdp.BrSearch.listener(event)">Search</button>
    </div>
    <p id="totalSearchResult" class="mb-0 my-1"></p>
  </div>
  <div class="text-center"><a href="javascript:void(0)" rel="noopener noreferrer" onclick='window.open("/refreshLocalStorage","_blank", "popup=yes,width=100,height=100,noopener,noreferrer")'>Refresh</a> Local Storage</div>
</header>

<article id="BrList"></article>

<div class="modal fade" id="applSchema" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="applSchemaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="applSchemaLabel">Applicability Schema</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <a href="javascript:void(0)" class="text-end mx-3" onclick="Brdp.BrSearch.selectDeselectAll(this)">deselect all</a>
      <div class="modal-body">
        {{-- check box --}}
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="appliccrossreftableXsd" id="appliccrossreftableXsd" checked>
          <label class="form-check-label" for="appliccrossreftableXsd">Applicable Cross Reference Table</label><br/>
          
          <input class="form-check-input" type="checkbox" value="brDocXsd" id="brDocXsd" checked>
          <label class="form-check-label" for="brDocXsd">Business rule data module</label><br/>
          
          <input class="form-check-input" type="checkbox" value="brexXsd" id="brexXsd" checked>
          <label class="form-check-label" for="brexXsd">Business rule exchange index</label><br/>

          <input class="form-check-input" type="checkbox" value="checklistXsd" id="checklistXsd" checked>
          <label class="form-check-label" for="checklistXsd">Crew checklist</label><br/>

          <input class="form-check-input" type="checkbox" value="commentXsd" id="commentXsd" checked>
          <label class="form-check-label" for="commentXsd">Comment</label><br/>

          <input class="form-check-input" type="checkbox" value="comrepXsd" id="comrepXsd" checked>
          <label class="form-check-label" for="comrepXsd">Common information repository</label><br/>

          <input class="form-check-input" type="checkbox" value="condcrossreftableXsd" id="condcrossreftableXsd" checked>
          <label class="form-check-label" for="condcrossreftableXsd">Applicability conditions cross reference table</label><br/>

          <input class="form-check-input" type="checkbox" value="containerXsd" id="containerXsd" checked>
          <label class="form-check-label" for="containerXsd">Container</label><br/>

          <input class="form-check-input" type="checkbox" value="crewXsd" id="crewXsd" checked>
          <label class="form-check-label" for="crewXsd">Crew</label><br/>

          <input class="form-check-input" type="checkbox" value="ddnXsd" id="ddnXsd" checked>
          <label class="form-check-label" for="ddnXsd">Data dispatch note</label><br/>

          <input class="form-check-input" type="checkbox" value="descriptXsd" id="descriptXsd" checked>
          <label class="form-check-label" for="descriptXsd">Descriptive</label><br/>

          <input class="form-check-input" type="checkbox" value="dmlXsd" id="dmlXsd" checked>
          <label class="form-check-label" for="dmlXsd">Data management list</label><br/>

          <input class="form-check-input" type="checkbox" value="faultXsd" id="faultXsd" checked>
          <label class="form-check-label" for="faultXsd">Fault</label><br/>

          <input class="form-check-input" type="checkbox" value="frontmatterXsd" id="frontmatterXsd" checked>
          <label class="form-check-label" for="frontmatterXsd">Front matter</label><br/>

          <input class="form-check-input" type="checkbox" value="icnMetadataXsd" id="icnMetadataXsd" checked>
          <label class="form-check-label" for="icnMetadataXsd">ICN metadata</label><br/>

          <input class="form-check-input" type="checkbox" value="ipdXsd" id="ipdXsd" checked>
          <label class="form-check-label" for="ipdXsd">IPD data module</label><br/>

          <input class="form-check-input" type="checkbox" value="learningXsd" id="learningXsd" checked>
          <label class="form-check-label" for="learningXsd">Learning data module</label><br/>

          <input class="form-check-input" type="checkbox" value="pmXsd" id="pmXsd" checked>
          <label class="form-check-label" for="pmXsd">Pulication module</label><br/>

          <input class="form-check-input" type="checkbox" value="prdcrossreftableXsd" id="prdcrossreftableXsd" checked>
          <label class="form-check-label" for="prdcrossreftableXsd">Product crossreference table data module</label><br/>

          <input class="form-check-input" type="checkbox" value="procedXsd" id="procedXsd" checked>
          <label class="form-check-label" for="procedXsd">Procedural data module</label><br/>

          <input class="form-check-input" type="checkbox" value="processXsd" id="processXsd" checked>
          <label class="form-check-label" for="processXsd">Process data module</label><br/>

          <input class="form-check-input" type="checkbox" value="sbXsd" id="sbXsd" checked>
          <label class="form-check-label" for="sbXsd">Service bulletin data module</label><br/>

          <input class="form-check-input" type="checkbox" value="schedulXsd" id="schedulXsd" checked>
          <label class="form-check-label" for="schedulXsd">Maintenance planning data module</label><br/>

          <input class="form-check-input" type="checkbox" value="scocontentXsd" id="scocontentXsd" checked>
          <label class="form-check-label" for="scocontentXsd">SCO content data module</label><br/>

          <input class="form-check-input" type="checkbox" value="scormcontentpackageXsd" id="scormcontentpackageXsd" checked>
          <label class="form-check-label" for="scormcontentpackageXsd">SCORM</label><br/>

          <input class="form-check-input" type="checkbox" value="updateXsd" id="updateXsd" checked>
          <label class="form-check-label" for="updateXsd">Update file Schema</label><br/>

          <input class="form-check-input" type="checkbox" value="wrngdataXsd" id="wrngdataXsd" checked>
          <label class="form-check-label" for="wrngdataXsd">Wiring data module</label><br/>

          <input class="form-check-input" type="checkbox" value="wrngfldsXsd" id="wrngfldsXsd" checked>
          <label class="form-check-label" for="wrngfldsXsd">Wiring fields data module</label><br/>         
        </div>
        {{-- end check box --}}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="Brdp.BrSearch.setApplSchema(this.parentElement.parentElement.querySelectorAll('input')); ">Set</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts_onBottom')
<script src="/general/createXML.js"></script>
<script src="/general/AllStyle.js"></script>
<script src="/js/brdp/Brdp2.js"></script>
<script>
  function refresh(){
    Brdp.refresh()
    .then((v) => v ? Brdp.BrList.refresh() : false)
    .then((v) =>  v ? Brdp.BrDetail.refresh() : false)
    .then((v) =>  v ? Brdp.BrDecision.refresh() : false)
    .then((v) =>  {
      if (v) {
        Brdp.renderHtml('BrList', Brdp.BrList.htmlDoc.firstElementChild);
        (hash = window.location.hash) != '' ? document.querySelector(hash).scrollIntoView(true) : null;
      }
    })
  }
  document.addEventListener('DOMContentLoaded', ()=>{
    refresh();
  });
</script>
@endsection