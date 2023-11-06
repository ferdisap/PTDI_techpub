@extends('html_head',[
  'title' => 'CSDB Object'
])

@section ('body')
  <header>
    @include('navbar')
  </header>
  

  @include('csdb.components.info')

  <div class="row">
    <div class="border col-md-2">
      @include ('csdb.aside')
    </div>
    
    <form action="{{ route('create_csdb_object') }}" method="post" enctype="multipart/form-data" class="row col-md-10">
      <section class="col-md-10">
          @csrf
          <h1>Create CSDB Object</h1>
          @include('csdb.components.modelIdentCode',[
            'checked' => request()->old('modelIdentCode')
          ])
          @include('csdb.components.editor',[
            'button' => 'create'
          ])
          <div>
            <input type="file" name="entity">
          </div>
        </section>

        <div class="border col-md-2">
          <div>Initiator: {{ request()->user()->email }}</div>
          <label for="description">Description</label>
          <textarea name="description" id="description" cols="30" rows="10">{{ request()->old('description') ?? ($description ?? '') }}</textarea>
        </div>
    </form>
  
  </div>
@endsection