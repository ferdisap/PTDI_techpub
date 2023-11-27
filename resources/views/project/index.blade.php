@extends('html_head',[
  'title' => 'CSDB Index'
])

@section('body')
  <header>
    @include('navbar')
  </header>

  @include('project.components.info')

  <div class="d-flex">
    @include ('project.aside')
  
    <section>
      <a href="{{ route('get_create_project') }}">Create Project</a>

      @if(isset($create) AND $create)
        @include('project.components.create')
      @endif

      @if(isset($assign) AND $assign)
        @include('project.components.assign')
      @endif

      @if(isset($detail) AND $detail)
        @include('project.components.detail')
      @endif

      @if(isset($listspr))
      <table>
        <thead class="border-bottom">
          <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($listspr as $pr)
            <tr>
              <td><a href="{{ route('get_detail_project') }}?name={{ $pr->name }}">{{ $pr->name }}</a></td>
              <td>{{ $pr->description }}</td>
              <td><a href="/csdb/project/delete?name={{ $pr->name }}"><button>delete</button></a></td>
              <td><a href="{{ route('get_assign_object') }}?name={{ $pr->name }}"><button>Assign Object</button></a></td>
            </tr>   
          @endforeach
          
        </tbody>
      </table>
      @endif
      
    </section>
  </div>
@endsection