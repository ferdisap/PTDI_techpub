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
      <input class="brdp_input_search form-control w-50" type="text" filterBy="all" placeholder="find everything of business rule.." aria-label="find business rule" onkeypress="BrdpSearch.evetListener(this,event)">
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
          <td><input type="text" class="brdp_input_search" filterBy="ident" placeholder="search ident.." style="width: 100px"onkeypress="BrdpSearch.evetListener(this,event)"></td>
          <td><input type="text" class="brdp_input_search" filterBy="title" placeholder="search title.."onkeypress="BrdpSearch.evetListener(this,event)"></td>
          <td><input type="text" class="brdp_input_search" filterBy="category" placeholder="search category.."onkeypress="BrdpSearch.evetListener(this,event)"></td>
          <td><input type="text" class="brdp_input_search" filterBy="audit" placeholder="search audit.."onkeypress="BrdpSearch.evetListener(this,event)"></td>
          <td><input type="text" class="brdp_input_search" filterBy="decision" placeholder="search decision.."onkeypress="BrdpSearch.evetListener(this,event)"></td>
        </tr>
      </thead>
      <tbody>
        @foreach($lists as $list)
        {{-- @dd($list) --}}
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