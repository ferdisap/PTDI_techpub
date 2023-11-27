@props(['pr'])
<div>
  <h4>Detail of {{ $pr->name }}</h4>

  <div>
    <a href="{{ route('get_create_csdb_object') }}?project=MALE">Create New Object</a>
  </div>

  @foreach($pr->csdb()->get() as $csdb)
    @php
    $filename = $csdb->filename; 
    @endphp
    <div>
      {{-- <a href="{{ route('get_update_csdb_object') }}?filename={{ $filename }}">{{ $filename }}</a> --}}
      <a href="{{ route('get_detail_csdb_object') }}?filename={{ $filename }}">{{ $filename }}</a>
    </div>
  @endforeach

</div>