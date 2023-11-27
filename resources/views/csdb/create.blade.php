@extends('html_head',[
  'title' => 'CSDB Object'
])

@section ('body')
  <header>
    @include('navbar')
  </header>
  

  @include('project.components.info')

  <div class="row">
    <div class="border col-md-2">
      @include ('project.aside')
    </div>
    
    <form action="{{ route('get_create_csdb_object') }}" method="post" enctype="multipart/form-data" class="row col-md-10">
      <section class="col-md-10">
          @csrf
          <h1>Create CSDB Object</h1>
          @include('csdb.components.editor',['button' => 'create'])
          <div>
            <input type="file" name="entity">
          </div>
        </section>

        <div class="border col-md-2">
          <div>Initiator: {{ request()->user()->email }}</div>
          
          {{-- project_name --}}
          <label for="project_name">Project Name</label>
          <input type="text" name="project_name" id="project_name" class="{{ isset(session()->get('error')['project_name']) ? 'border border-danger' : '' }}" value="{{ request()->old('project_name') ?? ($project ?? '') }}">
          <br/>
          @error('project_name')    
          <ul>
            @foreach($errors->get('project_name') as $message)
            <li class="text-danger fs-6">{{ $message }}</li>
            @endforeach
          </ul>
          @enderror

          {{-- dmrl --}}
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

          <label for="description">Description</label>
          <textarea name="description" id="description" cols="30" rows="10">{{ request()->old('description') ?? ($description ?? '') }}</textarea>
        </div>
    </form>
  
  </div>
@endsection