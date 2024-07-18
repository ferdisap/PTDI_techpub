import Randomstring from "randomstring";

// source: https://stackoverflow.com/questions/4179708/how-to-detect-if-the-pressed-key-will-produce-a-character-inside-an-input-text
function isCharacterKeyPress(evt) {
  if (typeof evt.which == "undefined") {
    // This is IE, which only fires keypress events for printable keys
    return true;
  } else if (typeof evt.which == "number" && evt.which > 0) {
    // In other browsers except old versions of WebKit, evt.which is
    // only greater than zero if the keypress is a printable key.
    // We need to filter out backspace and ctrl/alt/meta key combinations
    // return !evt.ctrlKey && !evt.metaKey && !evt.altKey && evt.which != 8;
    // modifan saya
    return !evt.ctrlKey && !evt.metaKey && !evt.altKey && evt.which != 8 
    && (evt.which !== 1) && (evt.which !== 2) && (evt.which !== 3) && (evt.which !== 27) && (evt.which !== 13) && (evt.which !== 37) && (evt.which !== 38) && (evt.which !== 39) && (evt.which !== 40);
  }
  return false;
}

function isArrowDownKeyPress(evt){
  return (evt.keyCode === 40) ? true : false;
}
function isArrowUpKeyPress(evt){
  return (evt.keyCode === 38) ? true : false;
}
function isEnterKeyPress(evt){
  return (evt.keyCode === 13) ? true : false;
}
function isEscapeKeyPress(evt){
  return (evt.which === 27) ? true : false;
}
function isLeftClick(evt){
  return (evt.which === 1) ? true : false;
}
function isRightClick(evt){
  return (evt.which === 3) ? true : false;
}

class DropdownInputSearch {
  idInputText = '';
  idDropdownListContainer = ''

  timeout = 0;
  process = new Promise(r => { r() });
  result = [];
  isDone = true;

  selected = {};
  hovered = {};
  listItemKey = '';
  showList = true;

  constructor(listItemKey) {
    this.idInputText = Randomstring.generate({ charset: 'alphabetic' });
    this.idDropdownListContainer = Randomstring.generate({ charset: 'alphabetic' });
    this.listItemKey = listItemKey
  }

  keypress(event, techpubRoute) {
    window.e = event;
    event.preventDefault();
    event.stopPropagation();
    this.showList = true;
    // console.log(isEnterKeyPress(event), isLeftClick(event));
    if(isCharacterKeyPress(event)) this.searching(techpubRoute);
    else if(isArrowDownKeyPress(event)) this.moveDown(event);
    else if(isArrowUpKeyPress(event)) this.moveUp(event);
    else if(isEnterKeyPress(event)) this.select(event);
    else if(isLeftClick(event)) this.selectByClick(event);
    else if(isEscapeKeyPress(event)) this.cancel(event);
  }

  searching(techpubRoute) {
    // console.log(techpubRoute);
    this.process = new Promise((r, j) => {
      clearTimeout(this.timeout);
      this.isDone = false;
      this.timeout = setTimeout(() => {
        let worker;
        if (window.Worker) {
          worker = new Worker(`/worker/WorkerInputSearch.js`, { type: "module" });
        }
        if (!worker) return false;
        worker.onmessage = (e) => {
          if (e.data.error) { // error merupakan key dari worker js
            j(false);
            this.result = [];
            this.isDone = false;
          } else {
            r(true);
            this.isDone = true;
            this.result = e.data.result; // result merupakan key dari server
          }
          worker.terminate();
        }
        worker.postMessage({ route: techpubRoute });
      }, 500);
    });

    return this.process;
  }

  moveDown(event){
    let el = event.target;
    if(event.target.id === this.idInputText){
      el = document.getElementById(this.idDropdownListContainer).firstElementChild;
    } else {
      el = el.nextElementSibling ?? document.getElementById(this.idInputText);// null jika tidak ada nextElementSibling;
    }
    this.focus(el);
    this.unfocus(event.target);
    this.setHovered(el);
  }

  moveUp(event){
    let el = event.target.previousElementSibling ?? document.getElementById(this.idInputText); // null jika tidak ada previousElementSibling
    console.log(el);
    this.focus(el);
    this.unfocus(event.target);
    this.setHovered(el);
  }

  select(event){
    this.selected = this.hovered;
    document.getElementById(this.idInputText).value = this.selected[this.listItemKey];
    this.showList = false;
  }
  
  selectByClick(event){
    this.setHovered(event.target);
    return this.select(event);
  }

  cancel(event){
    this.hovered, this.selected = {};
    this.showList = false;
  }

  // ##### function helper below ######
  setHovered(element){
    let listItemKeyValue = element.getAttribute(this.listItemKey);
    // console.log(listItemKeyValue);
    this.hovered = this.result.find(v => v[this.listItemKey] === listItemKeyValue);
  }
  focus(element){
    // console.log(element);
    element.tabIndex = 0;
    element.focus();
  }
  unfocus(element){
    element.tabIndex = -1;
  }
}

export default DropdownInputSearch;