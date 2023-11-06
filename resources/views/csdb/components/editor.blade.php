@props([
  'xmleditor' => $xmleditor ?? request()->old('xmleditor')
])
<div class="border">
  <h5>XML Data Module Editor</h5>
  <textarea name="xmleditor" id="xmleditor">{{ $xmleditor }}</textarea>
  <button type="submit" class="float-end">{{ $button }}</button>
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