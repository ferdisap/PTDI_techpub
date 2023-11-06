<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="coba/editor.css">
  <title>Editor</title>
</head>
<body>
  <div id="editor">
    Lorem, ipsum dolor
    <span onclick="create_element(this.parentElement,'para')">create paragraphs</span></div>
  {{-- <div onClick="this.contentEditable='true';">
    lorem ipsum dolor lorem ipsum dolorlorem ipsum dolor
  </div> --}}
  {{-- <para onclick="this.contentEditable='true';">tes</para> --}}
</body>
<script src="/coba/editor.js"></script>
</html>