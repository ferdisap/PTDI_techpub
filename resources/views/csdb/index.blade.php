@extends('html_head',[
  'title' => 'CSDB Index'
])

@section('body')
  <header>
    @include('navbar')
  </header>

  @include('csdb.components.info')

  <div class="d-flex">
    @include ('csdb.aside')
  
    <section>
      <div>
        List of model ident codes
        <ol>
          <li><a href="/csdb?mic=male">MALE</a></li>
          <li><a href="/csdb?mic=n219">N219</a></li>
          <li><a href="/csdb?mic=multimedia">multimedia</a></li>
        </ol>
      </div>
      @if(isset($listsobj))
      <table>
        <thead class="border-bottom">
          <tr>
            <th>Filename</th>
            <th>Status</th>
            <th>Action</th>
            <th>Description</th>
          </tr>
        </thead>
        <tbody>
          @foreach($listsobj as $obj)
          @php
          $filename = preg_replace("/.+\//","",$obj->path);
          $status = $obj->status;
          $description = $obj->description;
          @endphp
          <tr>
            <td><a href="/csdb/object/update?filename={{ $filename }}">{{ $filename }}</a></td>
            <td><a href="/csdb?mic={{ $mic }}&status={{ $status }}">{{ $status }}</a></td>

            <td class="d-flex">
              @if($mic != 'multimedia')
              <button><a href="/csdb/object/update?filename={{ $filename }}">update</a></button>
              @endif
              <form action="/csdb/object/delete" method="post">
                @csrf
                <input type="hidden" name="filename" value="{{ $filename }}">
                <button type="submit" class="underline">delete</button>
              </form>
            </td>

            <td>{{ $description }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @endif
    </section>
  </div>
@endsection