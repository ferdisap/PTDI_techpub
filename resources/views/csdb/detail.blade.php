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

    <div id="csdb_object" class="col-md-10 dump_red">
      <input id="filename" value="{{ $filename ?? '' }}">

      <hr>
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

      <div id="csdb_object_detail" style="width: inherit" class="dump_red">
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

  {{-- <script type="importmap">
    {
      "imports": {
        "detail": "{{ route('get_request_csdb_js') }}?filename=detail.js"
      }
    }
  </script>
  <script>
    
    csdb_detail_render({
      filename: '{{ $filename }}',
      id: 'csdb_object_detail',
      mime: '{{ $mime }}'
    }); 
  </script> --}}

  {{-- <script type="module">
    window.route = window.route ?? {};
    window.route.get_request_csdb_object = "{{ route('get_request_csdb_object') }}";
    window.route.get_request_csdb_xsl = "{{ route('get_request_csdb_xsl') }}";
    window.route.get_transform_csdb = "{{ route('get_transform_csdb') }}";

    let x = 'var';

    import { csdb_detail_render } from "{{ route('get_request_csdb_js') }}?filename=detail.js";

    csdb_detail_render({
      filename: '{{ $filename }}',
      id: 'csdb_object_detail',
      mime: '{{ $mime }}'
    });    
  </script> --}}

@endsection