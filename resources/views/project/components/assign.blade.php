@props(['name'])
<div>
  <h4>Assign Object to {{ $name }}</h4>
  <span class="fst-italic">Separate by the comma for more object.</span>
  <form action="{{ route('post_assign_object') }}" method="post">
    @csrf
    <input type="hidden" name="name" value="{{ $name }}">
    <label for="Object Ident">Name</label>
    <br/>
    <textarea cols="30" rows="10"
              name="ident" id="ident" class="@error('ident') border border-danger @enderror">{{ request()->old('ident') ?? '' }}</textarea>
    @if(isset(session()->get('error')['ident']))
    <ul>
      @foreach(session()->get('error')['ident'] as $message)
      <li class="text-danger fs-6">{{ $message }}</li>
      @endforeach
    </ul>
    @endif
    <br/>
    <button type="submit">Create</button>
  </form>
</div>