@extends('html_head',[
  'title' => 'Repo Index'
])

@section('body')
  <header>
    @include('navbar')
  </header>

  @include('project.components.info')

  <div class="d-flex">
    @include('project.aside')

    <section>
      <h1>List of Repository</h1>
      
      @php
      $repos = \App\Models\Repo::all();
      @endphp
      <table>
        <tr>
          <th>Name</th>
          <th>Project</th>
          <th>Token</th>
          <th>Date Created</th>
          <th>Action</th>
        </tr>
        @foreach ($repos as $repo)
        <tr>
          <td><a href="/ietm/content/{{ $repo->name }}" target="_blank">{{ $repo->name }}</a></td>
          <td>{{ $repo->project_name }}</td>
          <td>{{ base64_decode($repo->token) }}</td>
          <td>{{ date('d-M-Y', strtotime($repo->created_at) ) }}</td>
          <td><a href="">Refresh</a> | <a href="{{ route('get_delete_repo') }}?repoName={{ $repo->name }}">Delete</a></td>
        </tr>
        @endforeach
      </table>
    </section>
  </div>
@endsection