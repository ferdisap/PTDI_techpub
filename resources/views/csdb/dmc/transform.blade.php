@extends('html_head')

@section('body')
  <header>
    @include('navbar')
  </header>
  <div class="d-flex">
    <div class="w-50 border vh-100">
      <h1>Transform DMC</h1>
      <span>Put your code string here..</span>
      <form action="/dmc/{{ $aircraft }}/transform" method="post">
        @csrf
        <div class="outputType">
          <div class="form-check">
            <input class="form-check-input" type="radio" name="outputType" id="outputType1" value="web">
            <label class="form-check-label" for="outputType1">
              web
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="outputType" id="outputType2" value="pdf" checked>
            <label class="form-check-label" for="outputType2">
              pdf
            </label>
          </div>
        </div>
        
        <textarea name="xmlstring" id="xmlstring" class="w-100" style="height: 80vh"></textarea>
        <button type="submit" class="btn btn-primary float-end" onclick="event.preventDefault();transform(this.parentElement)">Transform</button>
      </form>
    </div>
    <div class="w-50 border vh-100">
      <h1>Result</h1>
      <span>PDF</span> | 
      <span>Web</span>
      <div id="web-transformed" style="width:100%;height:85vh">
        {{-- <iframe id="web-transformed" frameborder="0" src="about:blank" style="display: block"> --}}
        {{-- </iframe> --}}
      </div>
    </div>
  </div>
  @endsection

  @section('scripts_onBottom')
  <script src="/js/transform.js"></script>
  @endSection