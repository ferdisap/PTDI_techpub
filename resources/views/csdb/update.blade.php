@extends('html_head',[
  'title' => 'CSDB Object'
])

@section('body')
  <header>
    @include('navbar')
  </header>

  @include('project.components.info')
  
  <div class="d-flex">
    @include ('project.aside')

    <div class="d-block">
      <form action="{{ route('get_update_csdb_object') }}" method="post" class="row" enctype="multipart/form-data">
        @csrf      
        <section class="col-md-10">
          <h1>
            Update CSDB Object
            <input type="hidden" name="id" value="{{ request()->old('object_id') ?? $id }}"/>
          </h1>
          @include('csdb.components.editor',[
            'button' => 'update',
            'button2' => 'commit'
          ])
          <div>
            <img src="{{ $entitysrc ?? '' }}" alt="entity" width="50%">
            <input type="file" name="entity">
          </div>
        </section>
        <div class="border col-md-2">
          <div>Initiator: {{ $initiator }}</div>
    
          <!-- XSI Validate -->
          <input type="checkbox" name="xsi_validate" id="xsi_validate" checked/>
          <label for="xsi_validate">XSI Validate</label>
          <br/>
    
          <!-- BREX Validate -->
          <input type="checkbox" name="brex_validate" id="brex_validate" checked/>
          <label for="brex_validate">BREX Validate</label>
          <br/>
    
          <!-- dmrl -->
          <label for="dmrl">DMRL</label>
          <input type="text" name="dmrl" id="dmrl" class="{{ isset(session()->get('error')['dmrl']) ? 'border border-danger' : '' }}" value="{{ request()->old('dmrl') ?? '' }}">
          <br/>
          @error('dmrl')
          <ul>
            @foreach($errors->get('dmrl') as $message)
            <li class="text-danger fs-6">{{ $message }}</li>
            @endforeach
          </ul>
          @enderror
    
          <!-- description -->
          <label for="description">Description</label>
          <textarea name="description" id="description" cols="30" rows="10">{{ request()->old('description') ?? ($description ?? '') }}</textarea>
        </div>
      </form>
      <br/>
      {{-- dipindah ke halaman detail csdb --}}
      {{-- <div>
        <h1>Verification of</h1>
        <div id="csdb_filename">Filename: DMC-XXX-XXX</div>
        <div id="csdb_type">Type: Publication Module</div>
        <div id="csdb_title">Title: Lorem ipsum dolor sit amet.</div>
        <div id="csdb_issueDate">Issue Date: DD-MM-YYYY</div>
        <div id="csdb_issueType">Issue Type: New</div>
        <div id="csdb_responsiblePartnerCompany">code: XXXXl | name: XXXX</div>
        <div id="csdb_originator">code: XXXXl | name: XXXX</div>
        <div id="csdb_applicability">Applicability: all</div>
        <div id="csdb_brexDmRef">DMC-XXX-XXX</div>
        <div id="csdb_qualityAssurance">QA status: unverified</div>
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
      </div> --}}
    </div>
  </div>

  {{-- <script>
    fetch("{{ route('request_csdb_object') }}?filename={{ request()->get('filename') }}")
    .then(r => r.json())
    .then(v => {
      csdb = (new DOMParser).parseFromString(v.csdb,'text/xml');
      window.csdb = csdb;
    });
  </script> --}}

@endsection
