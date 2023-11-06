// document.addEventListener('DOMContentLoaded', function(){
  // console.log('editor.js');
  // window.editor = new Editor('editor', 'para');
// });
// document.querySelector("div#editor").addEventListener('click', function(){
//   let para = new Element('para');
//   para.element.addEventListener('click', function(){
//     `<textarea id="board"></textarea>`
//   });
// })
function create_element(editor, name){
  let doc = document.implementation.createDocument('http://www.ptditechpub.com/2023/s1000d', name);
  doc.createAttributeNS('http://www.w3.org/1999/xhtmsl','xh');
  window.doc = doc;
  return;
  // let doc = document.implementation.createDocument('http://www.w3.org/1999/xhtml', `sd:${name}`);
  el = doc.firstElementChild
  // el.contentEditable = 'true';
  el.setAttributeNS
  el.addEventListener('keydown', (event) => {event.keyCode == 13 ? event.preventDefault() : false});
  el.innerHTML = 'lorem lorem ipusm'
  editor.appendChild(el);
}

// let parse = Range.prototype.createContextualFragment.bind(document.createRange());
// document.querySelector('div#editor').appendChild(parse(`<textarea id="board"></textarea>`))

class Editor {
  editor;element;
  constructor(id, name){
    this.editor = document.querySelector(`textarea#${id}`);
    this.element = new Element(name);

    console.log('editor was initialized.')
    this.editor.addEventListener('keyup', (event) => {
      this.element.edit(event, this.editor)
    });
    return this;
  }
}

class Element {
  name;
  element;
  // allowableChar = /[a-zA-Z0-9~!@#$%^&*()_+`\-={}\[\]\|:;"'<>,.?\/\ ]/g
  constructor(name){
    this.name = name;
    let el = document.implementation.createDocument('http://www.ptditechpub.com/2023/s1000d', name);
    // let el = document.implementation.createDocument('http://www.w3.org/1999/xhtml', name);
  
    window.el = el;
    this.element = el;
    console.log(el);
  }

  edit(event, editor){
    event.preventDefault();
    
    clearTimeout(this.timeout);
    this.timeout = setTimeout(() => {
      let text = editor.value;
      this.element.firstElementChild.innerHTML = text;
      // window.el = this.element;
      // console.log(this.element.firstElementChild);
    }, 500);
    // console.log(text);
    // this.element.firstElementChild.innerHTML += (String.fromCharCode(event.keyCode).match(this.allowableChar)) ? event.key : '';
    // console.log(event.keyCode, event.key, event);
    // console.log(event, this);
  }
}