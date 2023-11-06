@extends('html_head')

@section('body')
<header>
  @include('navbar')
  <div class="container text-center mt-3">
    <h1>N219 Project - DML</h1>
    <div class="nav justify-content-center d-flex">
      <a href="#" class="nav-link">DMRL</a>
      <a href="#" class="nav-link">CSL</a>
    </div>
    
    <div class="container mt-3 d-flex justify-content-center input-group w-50">
      <input class="form-control w-50 flex-inherit" type="text" filterBy="all" placeholder="try to find something" aria-label="find business rule" onkeypress="Brdp.BrSearch.listener(event)">
      <button class="input-group-text" onclick="">Search</button>
    </div>
    <p id="totalSearchResult" class="mb-0 my-1"></p>
  </div>
</header>

<div class="container">
  <Article id="dml">{{ $dml ?? '' }}</Article>
</div>
@endsection