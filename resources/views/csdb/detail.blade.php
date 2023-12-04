@extends('html_head',[
  'title' => 'CSDB Index'
])

{{-- @section('scripts_onTop') --}}
{{-- <script src="{{ route('get_request_csdb_js') }}?filename=request.js"></script> --}}
{{-- <script type="module" src="{{ route('get_request_csdb_js') }}?filename=detail.js"></script> --}}
{{-- @endsection --}}

@section('body')
  <header>
    @include('navbar')
  </header>

  @include('project.components.info')

  <div class="d-flex">
    @include('project.aside')

    <div id="csdb_object" class="col-md-10 ">
      <input id="filename" value="{{ $filename ?? '' }}">
      <a href="/route/get_update_csdb_object?filename={{ $filename }}">Edit</a>

      <hr>
      @if(substr($filename, 0,3) != 'ICN')
        <form action="{{ route('post_csdb_object_verify') }}" method="POST">
          @csrf
          <input type="hidden" name="id" value="{{ $id }}">
      
          <input type="radio" name="verificationType" id="tabtop" value="tabtop">
          <label for="firstVerification">tabtop</label>
          <input type="radio" name="verificationType" id="onobject" value="onobject">
          <label for="firstVerification">onobject</label>
          <input type="radio" name="verificationType" id="ttandoo" value="ttandoo">
          <label for="firstVerification">ttandoo</label>
          @error('verificationType')
          <div class="text-danger fs-6">{{ $message }}</div>
          @enderror
      
          <br/>
          <label for="applicRefId">Applicability Refference Id: </label>
          <input type="text" name="applicRefId" id="applicRefId" value="{{ request()->old('applicRefId') ?? '' }}">
          @error('applicRefId')
          <div class="text-danger fs-6">{{ $message }}</div>
          @enderror
      
      
          <br/>
          @error('verification')
          <div class="text-danger fs-6">{{ $message }}</div>
          @enderror
          <button type="submit" name="verification" value="unverified">Unverified</button>
          <button type="submit" name="verification" value="firstVerification">First Verification</button>
        </form>
      @endif

      <div id="csdb_object_detail" style="width: inherit" class="">
        <div class="d-flex">Title: <div id="title"></div></div>        
        <div class="d-flex">Issue Date: <div id="resolve_issueDate"></div></div>        
        <div class="d-flex">Issue Type: <div id="resolve_issueType"></div></div>        
        <div class="d-flex">Responsible Partner Company: <div id="resolve_responsibleParnerCompany"></div></div>        
        <div class="d-flex">Originator: <div id="resolve_originator"></div></div>
        <div class="d-flex">Applicability: <div id="getApplicability"></div></div>
        <div class="d-flex">BREX DM Ref: <div id="resolve_brexDmRef"></div></div>
        <div class="d-flex">QA: <div id="resolve_qualityAssurance"></div></div>
      </div>

    </div>

    {{-- @vite(['resources/js/csdb/detail.js']) --}}
    @vite(['resources/js/csdb/CsdbReader.js'])
    <script type="module">
      let reader = new CsdbReader('{{ $filename }}');
      reader.container = document.querySelector("#csdb_object_detail");
      reader.render('detail');
    </script>
    
  </div>

  <div name="export_to_pdf">
    <a href="{{ route('get_export_csdb') }}?filename={{ $filename }}&type=pdf">export to pdf</a>
  </div>
  <div name="export_to_packages">
    <a href="{{ route('get_export_csdb') }}?filename={{ $filename }}&type=package">export to packages</a>
  </div>

@endsection