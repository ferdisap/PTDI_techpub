<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    
  <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
	<script src="https://projects.davidlynch.org/maphilight/jquery.maphilight.min.js"></script>
  @vite('resources/css/app.css')
  @vite('resources/css/loadingbar.css')
  @vite('resources/css/dmodule.css')
  @vite('resources/js/csdb3/app.js')


  {{-- ga bisa pakai tooltips dari bootstrap, karena vue component akan dirender dynamic, sementara tooltip harus di initialize setelah component di render. Merepotkan sehingga tidak cocok --}}
  {{-- @vite('resources/scss/style.scss') --}}
  {{-- @vite('resources/js/bootstrap.js') --}}
  {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> --}}
  {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script> --}}

  {{-- <link rel="stylesheet" href="https://www.jsdelivr.com/package/npm/@creativebulma/bulma-tooltip"> --}}
  {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@creativebulma/bulma-tooltip@1.2.0/dist/bulma-tooltip.min.css"> --}}
  {{-- @vite('resources/css/app2.css') --}}
  {{-- @vite('resources/scss/style.scss') --}}
  
  <title>CSDB</title>
</head>
<body id="body">
</body>