console.log('brDoc');

function setURLHash(id){
  window.history.pushState({},"",window.location.origin + window.location.pathname + '#' + id);
}

function goToHash(hash){
  window.location.hash = "";
  setTimeout(()=>{
    window.location.hash = hash;
  },0);
}

function openDetail(brId,brDecisionId,trId,el){
  console.log(el);
  let tr = createDetailContainer();
  

}

function createDetailContainer(){
  let tr = document.createElement('tr');
  tr.setAttribute('style','border:1px solid red;width:100%;height:100px');
  tr.innerHTML = 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Laboriosam, velit?';
  return tr;
}