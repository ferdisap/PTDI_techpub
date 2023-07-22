@extends('html_head')
@section('styles')
<link rel="stylesheet" href="/css/brdp/table.css">
@endSection

@section('body')
<header>
  @include('navbar')

  <div class="container text-center mt-3">
    <h1>N219 Project</h1>
    <div class="nav justify-content-center d-flex">
      <a href="#" class="nav-link">S1000D</a>
      <a href="#" class="nav-link disabled">PTDI</a>
      <a href="#" class="nav-link disabled">Contract</a>
      <a href="#" class="nav-link disabled">Customer</a>
    </div>
    
    <div class="container mt-3 d-flex justify-content-center">
      <input class="form-control w-50" type="text" filterBy="all" placeholder="find everything of business rule.." aria-label="find business rule" onkeypress="BrdpSearch.evetListener(this,event)">
    </div>
    <p id="totalSearchResult" class="mb-0 my-1"></p>
  </div>
</header>

<article class="d-flex justify-content-center">
  <div class="container-lg mt-3" style="width:90%; height:75vh; overflow:auto">
    <table id="brdpList-table">
      <thead>
        <tr>
          <th>Ident</th>
          <th>Title</th>
          <th>Category</th>
          <th>Audit</th>
          <th>Decision</th>
        </tr>
        <tr>
          <td>
            <div class="input-group">
              <input type="number" class="filterSort input-group-text p-0" style="width: 30px" for="ident" id="" max="5" min="1" value="1">
              <input type="text" class="form-control" filterBy="ident" placeholder="search ident.." onkeypress="BrdpSearch.evetListener(this,event)">
            </div>
          </td>
          <td>
            <div class="input-group">
              <input type="number" class="filterSort input-group-text p-0" style="width: 30px" for="title" id="" max="5" min="1" value="2">
              <input type="text" class="form-control" filterBy="title" placeholder="search title.." onkeypress="BrdpSearch.evetListener(this,event)">
            </div>
          </td>
          <td>
            <div class="input-group">
              <input type="number" class="filterSort input-group-text p-0" style="width: 30px" for="category" id="" max="5" min="1" value="3">
              <input type="text" class="form-control" filterBy="category" placeholder="search category.." onkeypress="BrdpSearch.evetListener(this,event)">
            </div>
          </td>
          <td>
            <div class="input-group">
              <input type="number" class="filterSort input-group-text p-0" style="width: 30px" for="audit" id="" max="5" min="1" value="4">
              <input type="text" class="form-control" filterBy="audit" placeholder="search audit.." onkeypress="BrdpSearch.evetListener(this,event)">
            </div>
          </td>
          <td>
            <div class="input-group">
              <input type="number" class="filterSort input-group-text p-0" style="width: 30px" for="decision" id="" max="5" min="1" value="5">
              <input type="text" class="form-control" filterBy="decision" placeholder="search decision.." onkeypress="BrdpSearch.evetListener(this,event)">
            </div>
          </td>
        </tr>
      </thead>
      <tbody>
        @foreach($lists as $list)
        <tr id="{{ $list['id'] }}" onclick="{{ $list['tr_onclick'] }}" onmouseover="BrdpTable.changeBgColor(this)">
          <td>{!! $list['ident'] !!}</td>
          <td>{{ $list['title'] }}</td>
          <td>{{ $list['category'] }}</td>
          <td>{{ $list['audit'] }}</td>
          <td>{{ $list['decision'] }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</article>
@endsection

@section('scripts_onBottom')
<script src="/js/brdp/brDoc.js"></script>
@endsection