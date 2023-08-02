<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
</head>
<body>


  
<script src="/?utility=getfile&ct=javascript&path=assets/general/js/createXML.js"></script>
<script src="/?utility=getfile&ct=javascript&path=assets/general/js/AllStyle.js"></script>
<script src="/?utility=getfile&ct=javascript&path=assets/brdp/js/Brdp2.js"></script>
<script>
  function refresh(){
    localStorage.removeItem('allStyle');
    localStorage.removeItem('allDecision');
    AllStyle.getListAllGeneralStyle()
    .then(v => {
      if (v){
        AllStyle.cache;
        return true;
      }
    })
    .then(v => {
      if (v){
        return Brdp.refresh()
        .then((v) =>  v ? Brdp.BrDecision.refresh() : false)
      }
    })
    .then(v => {
      if(v){
        window.close();
      }
    });
  }
  document.addEventListener('DOMContentLoaded', ()=>{
    refresh();
  });
</script>
</body>
</html>