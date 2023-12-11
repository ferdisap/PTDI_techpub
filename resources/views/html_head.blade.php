<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="stylesheet" href="/css/bootstrap_5.2.0.min.css">

  <style>
    th{
      border-bottom: 1px solid black
    }
    th,td{
      padding: 2px;
      padding-left:5px;
      padding-right:5px;
    }

    .loading_buffer {
      border: 16px solid #f3f3f3; /* Light grey */
      border-top: 16px solid #3498db; /* Blue */
      border-radius: 50%;
      animation: spin 2s linear infinite;
      position: absolute;
      width:50px;
      height:50px;
      top:calc(50% - 25px);
      left:calc(50% - 25px);
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .dump_red{
      border:1px solid red;
    }
  </style>

  @yield('styles')
  
  <script src="/js/bootstrap_5.3.1.bundel.min.js"></script>
  
  @yield('scripts_onTop')

  <title>{{ $title ?? 'N219 Techpub' }}</title>
</head>
<body class="lead">

  <template id="loading_buffer">
    <div style="
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    background:rgba(0,0,0,0.5);
    ">
      <div class="loading_buffer"></div>
    </div>
  </template>

  <script>
    const template_loading_buffer = document.getElementById('loading_buffer');
    const clone_loading_buffer = template_loading_buffer.content.cloneNode(true);
    function add_loading_buffer(targetNode){
      targetNode.appendChild(clone_loading_buffer);
    }
    function remove_loading_buffer(targetNode){
      const loading_buffer = targetNode.getElementsByClassName('loading_buffer')[0];
      targetNode.removeChild(loading_buffer.parentElement);
    }
  </script>
  
  
  @yield('body')
  
  @yield('scripts_onBottom')
</body>