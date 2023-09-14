@extends('html_head')
@section('styles')
<style>
  .dump_red {
    border: 1px solid red;
  }
</style>
@endSection

@section('body')
<header>
  @include('navbar')

  <div class="container text-center mt-3">
    <h1>N219 Project - BREX</h1>
    <div class="nav justify-content-center d-flex">
      <a href="javascript:void(0)" onclick="Brex.showContent('SnsRule', Brex.SnsRule.htmlDoc)" class="nav-link">SNS Rules</a>
      <a href="javascript:void(0)" onclick="Brex.showContent('ContextRule', Brex.ContextRule.htmlDoc)" class="nav-link">Context Rules</a>
      <a href="#" class="nav-link">Non-Context Rules</a>
    </div>
    
    <div class="container mt-3 d-flex justify-content-center">
      <input class="form-control w-50" type="text" filterBy="all" placeholder="find everything of brex.." onkeypress="BrexSearch.evetListener(this,event)">
    </div>
    <p id="totalSearchResult" class="mb-0 my-1"></p>
  </div>
</header>

<article id="snsStructure">
  <div class="d-flex justify-content-center flex-column container">
    <p>Sns is structured by the number of characters which is defined by the BRDP. This system is designed to provide standardization in the arrangement or addressing of the Product</p>
    <div class="d-flex justify-content-center w-100">
      <img class="align-self-center" style="max-width: 400px" src="/images/brex/ICN-AE-A-040303-0-C0419-00004-A-03-1.jpeg" alt="SNS Structure - chapterized">
      <img class="align-self-center" style="max-width: 400px" src="/images/brex/ICN-AE-A-040303-0-C0419-00005-A-04-1.jpg" alt="SNS Structure - non chapterized">
    </div>
  </div>
</article>
<article id="SnsRule"></article>
<article id="ContextRule"></article>

@endsection

@section('scripts_onBottom')
<script src="/general/createXML.js"></script>
<script src="/general/AllStyle.js"></script>
<script src="/js/brex/Brex.js"></script>
@endsection