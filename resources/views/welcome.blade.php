@extends('html_head')

@section('body')
  <header>
    @include('navbar')
  </header>
  <main>
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <div class="d-flex justify-content-center w-100">
      <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel" style="width:1000px">
        <div class="carousel-inner">
          @for ($i = 1; $i <= 8; $i++)
            <div class="carousel-item {{$i == 1 ? 'active' : ''}}">
              <img src="images/n219_images/0{{ $i }}.jpg" class="d-block w-100" alt="N219" style="height:300px;object-fit:none">
            </div>
          @endfor
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
        <div class="d-flex justify-content-center w-100 bg-black text-white fs-6 py-1 fst-normal">- PT Dirgantara Indonesia -</div>
      </div>
    </div>

    <section>
      {{-- Our Products --}}
      <div class="d-flex justify-content-center w-100 mt-3">
        <div class="fs-1 fw-bold">
          Our <span class="text-primary bg-gradient">Products</span>
          <hr style="border:2px solid black; opacity:100">
        </div>
      </div>

      {{-- Our Products - Content (row 1) --}}
      <div id="container-productsCard" class="d-flex justify-content-center w-100 mt-3">
        <div class="card mx-3" style="width: 18rem;">
          <div style="height:100px;display:flex;align-items:center;justify-content:center;font-size:5rem;font-weight:700">BRDP</div>
          <div class="card-body">
            <h5 class="card-title">Business Rule Decision Point</h5>
            <p class="card-text">The rules must be implemented in each publication data authored.</p>
            <a href="/brdp" class="btn btn-primary">Details</a>
          </div>
        </div>
        <div class="card mx-3" style="width: 18rem;">
          <div style="height:100px;display:flex;align-items:center;justify-content:center;font-size:5rem;font-weight:700">BREX</div>
          <div class="card-body">
            <h5 class="card-title">Business Rule Exchange Index</h5>
            <p class="card-text">The summary of rules for validation of each realising publication.</p>
            <a href="/brex" class="btn btn-primary">Details</a>
          </div>
        </div>
        <div class="card mx-3" style="width: 18rem;">
          <div style="height:100px;display:flex;align-items:center;justify-content:center;font-size:5rem;font-weight:700">DML</div>
          <div class="card-body">
            <h5 class="card-title">Data Management List</h5>
            <p class="card-text">Manage all data modules required to grouped by each publication type.</p>
            <a href="/dml" class="btn btn-primary">Details</a>
          </div>
        </div>
      </div>
      {{-- Our Products - Content (row 2) --}}
      <div id="container-productsCard" class="d-flex justify-content-center w-100 mt-3">
        <div class="card mx-3" style="width: 54rem;">
          <div style="height:100px;display:flex;align-items:center;justify-content:center;font-size:5rem;font-weight:700">Aircraft Manuals</div>
          <div class="card-body">
            <h5 class="card-title">Aircraft Publication Data</h5>
            <p class="card-text">Containing manuals either for operation or maintenance selected aircraft. Also include technical data record such as SB or SL.</p>
            <a href="#" class="btn btn-primary">Details</a>
          </div>
        </div>
      </div>
    </section>

    <section>
      {{-- News Update --}}
      <div class="d-flex justify-content-center w-100 mt-3">
        <div class="fs-1 fw-bold">
          <span class="text-primary bg-gradient">News</span> Update
          <hr style="border:2px solid black; opacity:100">
        </div>
      </div>

      {{-- List of news --}}
      <div id="container-newsUpdate" class="container">
        <div class="news">
          <h1 class="mt-1">New DMC has been released, General - Intercommunication System</h1>
          <span class="small fst-italic">2023 june 07</span>
          <p class="lead">Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis at eligendi iusto commodi ad tempore placeat sapiente error quisquam autem!</p>
          <p>Read the <a href="#">details.</a></p>
        </div>
        <div class="news">
          <h1 class="mt-1">One BRDP has been updated, BRDP-S1-00071</h1>
          <span class="small fst-italic">2023 june 07</span>
          <p class="lead">Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis at eligendi iusto commodi ad tempore placeat sapiente error quisquam autem!</p>
          <p>Read the <a href="#">details.</a></p>
        </div>
        <div class="news">
          <h1 class="mt-1">BREX has been updated.</h1>
          <span class="small fst-italic">2023 june 07</span>
          <p class="lead">Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis at eligendi iusto commodi ad tempore placeat sapiente error quisquam autem!</p>
          <p>Read the <a href="#">details.</a></p>
        </div>
      </div>

    </section>

  </main>
@endSection