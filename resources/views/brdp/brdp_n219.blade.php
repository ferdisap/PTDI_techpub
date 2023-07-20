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
      <input class="form-control w-50" type="text" placeholder="find business rule.." aria-label="find business rule">
    </div>
  </div>
</header>

<article class="container d-flex justify-content-center">
  <table id="brdpList-table">
    <thead>
      <tr>
        <th>Ident</th>
        <th>Title</th>
        <th>Category</th>
        <th>Audit</th>
        <th>Decision</th>
      </tr>
    </thead>
    <tbody>
      @foreach($lists as $list)
      {{-- @dd($list) --}}
      <tr id="{{ $list['id'] }}" onclick="{{ $list['tr_onclick'] }}">
        <td onclick="{{ $list['td_ident_onclick'] }}">{!! $list['ident'] !!}</td>
        <td>{{ $list['title'] }}</td>
        <td>{{ $list['category'] }}</td>
        <td>{{ $list['audit'] }}</td>
        <td>{{ $list['decision'] }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</article>
@endsection

@section('scripts_onBottom')
<script src="/js/brdp/brDoc.js"></script>
@endsection