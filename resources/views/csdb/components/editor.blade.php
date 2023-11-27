@props([
  'use_xmleditor' => true,
  'xmleditor' => $xmleditor ?? request()->old('xmleditor'),
  'button',
  'button2',
])
<div class="border">
  @if($use_xmleditor)
    <h5>XML Data Module Editor</h5>
    <textarea name="xmleditor" id="xmleditor">{{ request()->old('xmleditor') ?? $xmleditor }}</textarea>
  @endif
  <button type="submit" class="float-end" name="button" value="{{ $button }}">{{ $button }}</button>
  @if(isset($button2))
  <button type="submit" class="float-end" name="button" value="{{ $button2 }}">{{ $button2 }}</button>
  @endif
</div>

@section('scripts_onTop')
<link rel="stylesheet" href="/js/codemirror-5.65.15/lib/codemirror.css">
<script src="/js/codemirror-5.65.15/lib/codemirror.js"></script>
@endSection

@section('scripts_onBottom')
<script>
  var editor = CodeMirror.fromTextArea(document.getElementById('xmleditor'), {
    mode: "xml",
    lineWrapping: true,
    lineNumbers: true,
    "scrollbarStyle": "native",
    "autocorrect": true,
    "indentWithTabs": true,
  });
  editor.save()
</script>
@endsection