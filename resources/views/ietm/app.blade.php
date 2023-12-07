<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite('resources/css/app.css')
  @vite('resources/js/ietm/app.js')
  <title>IETM</title>
</head>
<body class="font-tahoma" id="body">
  <router-view>
  </router-view>
</body>