@extends('html_head')

@section('body')
  <header>
    @include('navbar')
  </header>
  <section class="mt-3 text-center container">
    {{-- Introcution BRDP --}}
    <div class="row my-lg-5">
      <div class="col-lg-8 col-md-10 mx-auto">
        <h1>Business Rule Decision Point Index</h1>
        <p class="lead text-body-secondary">A business rules document data module is one of a set of data modules that define all the business rules together with descriptions and examples that are required to support an organization and/or project. This includes the rules and requirements regarding how S1000D XML files must be configured and all of the other types of rules defined in Chap 2.5.1. This includes the entities, notations, elements, and attributes that are allowed and any restrictions on their values. The intent is to provide S1000D implementation guidance to authors, editors, and Information Technology personnel, who are responsible for the creation and development of valid content conforming to the design considerations of project managers and information architects (in accordance with contractual obligations and corporate, organization, and/or customer guidelines). Refer to Chap 2.5.1.</p>
        <div class="d-flex justify-content-center">
          <a href="/brdp/n219" class="text-decoration-none">
            <img src="/images/N219.png" alt="" srcset="" style="width:100px">
            <br><span class="fw-bold">N219</span>
          </a>
          <a href="#" class="text-decoration-none">
            <img src="/images/CN235.png" alt="" srcset="" style="width:100px">
            <br><span class="fw-bold">CN235</span>
          </a>
          <a href="#" class="text-decoration-none">
            <img src="/images/NC212I.png" alt="" srcset="" style="width:100px">
            <br><span class="fw-bold">NC212I</span>
          </a>
        </div>
      </div>
    </div>
  </section>
  <section class="mt-3 container">    
      {{-- Examples of BRDP --}}
      <div class="d-flex justify-content-center w-100 mt-3">
        <div class="fs-1 fw-bold">
          Example of <span class="text-primary bg-gradient">BRDP</span>
          <hr style="border:2px solid black; opacity:100">
        </div>
      </div>
  </section>
@endsection