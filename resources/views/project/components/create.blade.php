{{-- @error('name')
@dump((session()->get('errors')->default)->getMessages()['name'])
@enderror --}}
<div>
  <form action="{{ route('post_create_project') }}" method="post" enctype="multipart/form-data">
    @csrf
    <label for="name">Name</label>
    <input type="text" name="name" id="name" value="{{ request()->old('name') ?? '' }}" class="@error('name') border border-danger @enderror">
    @error('name')
    <ul>
      <li class="text-danger fs-6">{{ $message }}</li>
    </ul>
    @enderror
    <br/>
  
    <label for="description">Description</label>
    <textarea name="description" id="description" cols="30" rows="10"
    class="@error('description') border border-danger @enderror">{{ request()->old('description') ?? '' }}</textarea>
    @error('description')
    <ul>
      <li class="text-danger fs-6">{{ $message }}</li>
    </ul>
    @enderror
    
    <br/>
    <label for="dmrl">Import metadata</label>
    <input type="file" name="dmrl" id="dmrl">
    @error('dmrl')
    {{-- @dd($errors) --}}
    {{-- @dd($errors->get('dmrl')) --}}
    <ul>
      <li class="text-danger fs-6">{{ $message }}</li>
    </ul>
    @enderror

    <br/>
    <button type="submit">Create</button>
  </form>
</div>