@extends('html_head')

@section('body')
  <header>
    @include('navbar')
  </header>
  <section class="mt-3 text-center container">
    {{-- Introcution BRDP --}}
    <div class="row my-lg-5">
      <div class="col-lg-8 col-md-10 mx-auto">
        <h1>Business Rule Exchange Index</h1>
        <p class="lead text-body-secondary" style="text-align: left">Generally, business rules agreed upon within a project or enterprise can be of different characters, some of which are concerned with how the XML structures must be used in the CSDB objects. To facilitate a formalized and machine readable representation of how such rules apply to the XML structures, a corresponding BREX data module can be used.</p>
        <p class="lead text-body-secondary" style="text-align: left">Examples of use of the BREX data module include:
          <ul class="lead text-body-secondary" style="text-align: left">
            <li>recording and exchanging rules while they are being developed in a project or enterprise. The possibility to make a formal description of the business rules decreases the risk of misinterpretations and misunderstandings.</li>
            <li>supporting a correct interpretation of CSDB objects. This is of particular significance for security and safety related information (for example classification and units of measure for threshold intervals).</li>
            <li>enabling validation of the CSDB objects against agreed upon rules, for example when applying automated methods.</li>
          </ul>
        </p>
        <div class="d-flex justify-content-center">
          <a href="/brex/n219" class="text-decoration-none mx-3">
            <img src="/images/N219.png" alt="" srcset="" style="width:100px">
            <br><span class="fw-bold">N219</span>
          </a>
          <a href="#" class="text-decoration-none mx-3">
            <img src="/images/CN235.png" alt="" srcset="" style="width:100px">
            <br><span class="fw-bold">CN235</span>
          </a>
          <a href="#" class="text-decoration-none mx-3">
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
          BREX <span class="text-primary bg-gradient">Contents</span>
          <hr style="border:2px solid black; opacity:100">
        </div>
      </div>
      <div class="container-fluid mt-1">
        <h1 class="display-6">SNS Rules</h1>
        <p class="lead">The description of several SNS system that apply to the project concerned (N219 Aircraft).</p>
      </div>
      <div class="container-fluid mt-1">
        <h1 class="display-6">Context Related Rules</h1>
        <p class="lead">Contains rules that can be related to one or more Schema contexts (ie, rules regarding the use of elements in various Schemas).</p>
        <p class="lead">Rules given for a specific Schema override rules applicable to all Schema contexts. Rules specific to a Schema must be true restrictions to commonly applicable rules</p>
      </div>
      <div class="container-fluid mt-1">
        <h1 class="display-6">Context Non-Related Rules</h1>
        <p class="lead">contains the set of project-specific business rules which cannot be related to any particular schema/context or any particular elements and attributes in the schema structures.</p>
      </div>
  </section>
@endsection