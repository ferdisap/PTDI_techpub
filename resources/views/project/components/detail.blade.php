@props(['pr'])
<div>
  <h4>Detail of {{ $pr->name }}</h4>

  <div>
    <a href="{{ route('get_create_csdb_object') }}?project=MALE">Create New Object</a>
  </div>

  @php
  $objs = $pr->csdb()->where('status','!=', 'deleted')->where('status', '!=', 'unused')->get();
  @endphp
  <table>
    @foreach($objs as $object)
      <tr>
        <td>
          <div>
            <a href="{{ route('get_detail_csdb_object') }}?filename={{ $object->filename }}">{{ $object->filename }}</a>
          </div>
        </td>
        <td>{{ $object->status }}</td>
        <td>
          <button><a href="{{ route('get_delete_csdb_object') }}?filename={{ $object->filename }}">delete</a></button>
        </td>
        <td>
          <button><a href="{{ route('get_update_csdb_object') }}?filename={{ $object->filename }}">update</a></button>
        </td>
      </tr>
    @endforeach
  </table>

</div>