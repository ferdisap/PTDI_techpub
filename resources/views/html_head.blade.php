<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="/css/bootstrap_5.2.0.min.css">
  @yield('styles')
  <script src="/js/bootstrap_5.2.0.min.js"></script>
  @yield('scripts')
  <title>{{ $title ?? 'N219 Techpub' }}</title>
</head>
<body>
  @yield('body')
</body>