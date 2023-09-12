@extends('html_head')

@section('body')
  <header>
    @include('navbar')
  </header>
  <section class="mt-3 text-center container">
    {{-- Introcution BRDP --}}
    <div class="row my-lg-5">
      <div class="col-lg-8 col-md-10 mx-auto">
        <h1>Data Module</h1>
        <p class="lead text-body-secondary">Information required to support the Product must be produced, as discrete pieces of information called data modules, and stored in a CSDB. All data modules have a basic structure</p>
        <p class="lead text-body-secondary">All data modules have a basic structure.</p>
        <p class="lead text-body-secondary">Information sets are used to establish the required scope of the information and data module coding strategy.</p>
        <p class="lead text-body-secondary">All data modules are produced in accordance with structural rules. These are reinforced by  writing and illustration rules, together with front matter and warnings, cautions and notes. The  rules are supported by specific guidance for authoring data modules.</p>
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
          Example of <span class="text-primary bg-gradient">Validating</span> Data Module
          <hr style="border:2px solid black; opacity:100">
        </div>
      </div>      
      <div class="w-50">
        <form action="{{ route('validate-dmodule') }}" method="post" name="form_validate" enctype="multipart/form-data" class="position-relative">
          <div class="d-flex">
            <div class="form-check m-3">
              <input class="form-check-input" type="radio" name="aircraft_model" id="project_n219_1" value="n219" checked>
              <label class="form-check-label" for="project_n219_1">
                N219
              </label>
            </div>
            <div class="form-check m-3">
              <input class="form-check-input" type="radio" name="aircraft_model" id="project_cn235_1" value="cn235">
              <label class="form-check-label" for="project_cn235_1">
                CN235
              </label>
            </div>
            <div class="form-check m-3">
              <input class="form-check-input" type="radio" name="aircraft_model" id="project_nc212_1" value="nc212">
              <label class="form-check-label" for="project_nc212_1">
                NC212
              </label>
            </div>
          </div>
          <div class="mb-3">
            <label for="dm_file" class="form-label">Choose a data module file:</label>
            <input class="form-control" type="file" name="dm_file" id="d,_file">
          </div>
          <div class="mb-3">
            <label for="dm_text" class="form-label">Data Module XML Text</label>
            <textarea class="form-control" id="dm_text" rows="10" name="dmodule"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Validate</button>
        </form>
      </div>
  </section>
@endsection

@section('scripts_onBottom')
<script>
  function validate(event){
    event.preventDefault();
    if(this.tagName != 'FORM'){
      return false;
    }
    const xhr = new XMLHttpRequest();
    const formData = new FormData(this);
    
    xhr.open(this.method, this.action);
    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name=csrf-token]').getAttribute('content'));

    xhr.onprogress = () => {
      add_loading_buffer(event.target);
    }
    xhr.onload = () => {
      // setTimeout(() => {
      //   remove_loading_buffer(event.target);
      // }, 5000);
      remove_loading_buffer(event.target);
    }
    xhr.send(formData);
  }

  const form_validate = document.forms['form_validate'];
  form_validate.addEventListener('submit', validate);
  
</script>
@endsection