@extends('html_head')

@section('body')
  <header>
    @include('navbar')
  </header>
  <section class="mt-3 text-center container">
    {{-- Introcution BRDP --}}
    <div class="row my-lg-5">
      <div class="col-lg-8 col-md-10 mx-auto">
        <h1>Data Module Code</h1>
        <div class="para text-body-secondary text-start mb-2">
          <p>
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Officia facilis eos natus alias placeat ipsum dolorum beatae ad temporibus tenetur, fugiat qui provident accusamus eum! Tenetur velit nobis perferendis rerum?
          </p>
          <p>Kinde of DMC:</p>
          <ul>
            <li>checklist</li>
            <li>crew</li>
            <li>description</li>
            <li>fault</li>
            <li>frontmatter</li>
            <li>ipd</li>
            <li>procedure</li>
            <li>process</li>
            <li>sb</li>
            <li>schedule</li>
            <li>wiring data</li>
            <li>wiring fields</li>
          </ul>
        </div>
        <div class="d-flex justify-content-center">
          <a href="/dmc/n219" class="text-decoration-none mx-3">
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
          Recent <span class="text-primary bg-gradient">Update</span> DMC
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