@extends('html_head',[
  'title' => 'CSDB Object'
])

@section('body')
  <header>
    @include('navbar')
  </header>

  @include('csdb.components.info')
  
  <form action="{{ route('update_csdb_object') }}" method="post" class="row">
    @csrf
    <div class="border col-md-2">
      @include ('csdb.aside')
    </div>
  
    <section class="col-md-8">
      <h1>Update CSDB Object</h1>
      @include('csdb.components.editor',[
        'button' => 'update'
      ])
    </section>

    <div class="border col-md-2">
      <div>Initiator: {{ $initiator }}</div>
      <label for="description">Description</label>
      <textarea name="description" id="description" cols="30" rows="10">{{ request()->old('description') ?? ($description ?? '') }}</textarea>
    </div>
  </form>
@endsection
