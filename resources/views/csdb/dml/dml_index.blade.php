@extends('html_head')

@section('body')
  <header>
    @include('navbar')
  </header>
  <section class="mt-3 text-center container">
    {{-- Introcution BRDP --}}
    <div class="row my-lg-5">
      <div class="col-lg-8 col-md-10 mx-auto">
        <h1>Data Management List</h1>
        <div class="para text-body-secondary text-start mb-2">
          <p>
            For the planning, management and control of the content of the CSDB for individual projects the 
            use of the following data management lists are recommended:
          </p>
          <ul>
            <li>DMRL - Data Management Requirement List
            <li>CSL - CSDB Status List</li>
          </ul>
        </div>
        <div class="para text-body-secondary text-start mb-2">
          <p>A Data Management Requirement List (DMRL) is used to identify the required data modules 
            and, by project decision, publication modules for a project. The data management requirement 
            list supports planning, reporting, production and configuration control, especially in a work share 
            environment. A data management requirement list can be generated in parts (eg, by partner 
            companies for later merging) or in a complete form.
          </p>
          <p>The purpose of a CSDB Status List (CSL) is to exchange information about the status of the 
            CSDB. For example, in a multi-partner project, the CSL is used to check that the CSDB objects 
            (data modules, illustrations, etc) that have been sent by one partner have been successfully 
            loaded by another, or the data received by a customer is what has been sent by the project.</p>
        </div>
        <div class="d-flex justify-content-center">
          <a href="/dml/n219" class="text-decoration-none mx-3">
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
          Object Recent <span class="text-primary bg-gradient">Update</span>
          <hr style="border:2px solid black; opacity:100">
        </div>
      </div>
      <div class="container-fluid mt-1">
        <h1 class="display-6">DMC-XXXX-XX-XX-XX.xml</h1>
        <p class="lead">Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolorum porro asperiores quae, ipsum ea, obcaecati distinctio, neque similique nesciunt accusamus doloremque. Itaque dolorum ad molestias deleniti, culpa est suscipit repellendus eum sunt minus facilis saepe eius doloribus qui at nostrum ea sed molestiae quam maiores accusamus sit? Debitis dolores quisquam accusantium accusamus quod! Ullam enim error deleniti labore suscipit. Fugiat ullam ab molestias a cum, repellat, culpa atque dolorum quasi dolores voluptates rerum accusantium earum exercitationem eveniet perspiciatis repudiandae. Placeat laudantium porro, accusamus sed a deleniti, ipsam quia, cum id modi fugiat illum aspernatur consectetur incidunt exercitationem quidem! Nam, consequatur ad reiciendis similique, eaque doloremque ut harum cumque facilis eius enim. Tempore delectus repellat et autem culpa id obcaecati, ipsam harum, perferendis repudiandae nesciunt sed, omnis voluptatem vel! Eos, dolore?</p>
      </div>
      <div class="container-fluid mt-1">
        <h1 class="display-6">DMC-XXXX-XX-XX-XX.xml</h1>
        <p class="lead">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Fuga mollitia officia, autem, ducimus eligendi quisquam id culpa, non qui laboriosam quasi quibusdam inventore! Molestias dolorum dicta aliquam quas. Ut, ipsum.</p>
      </div>
      <div class="container-fluid mt-1">
        <h1 class="display-6">DMC-XXXX-XX-XX-XX.xml</h1>
        <p class="lead">Lorem ipsum dolor sit amet consectetur adipisicing elit. Laborum aperiam illo dolor ipsum! Non modi libero illo quibusdam ea blanditiis.</p>
      </div>
  </section>
@endsection