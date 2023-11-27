<div class="text-center">{{ session()->get('result') }}</div>
@if(!empty($errors->info->all()))
  @foreach($errors->info->all() as $message)
    <div class="text-center">{{ $message }}</div>
  @endforeach
@endif
{{-- @if(session()->get('messages'))
  @php $k = 0; @endphp
  @while(isset(session()->get('messages')[$k]))
    <div class="text-center">{{ session()->get('messages')[$k] }}</div>
    @php $k++; @endphp
  @endwhile
@endif --}}
{{-- @dump($errors, $errors->get('dmrl'))
@error('dmrl')
@dump('aa')
@enderror --}}