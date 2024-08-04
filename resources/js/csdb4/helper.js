

// const isObject = (obj) => typeof obj === 'object' && !Array.isArray(obj) && obj !== null
// return (typeof str === 'string' || str instanceof String);
// return Array.isArray(arr);
// const isClass = Function.prototype.call.bind(Object.prototype.toString); // [object Function] or [object FormData] or [object Array] or dll
const array_unique = (arr) => arr.filter((value,index,a) => a.indexOf(value) === index);

/**
 * sudah dicoba, hasilnya sama seperti di laravel request yang ubah fd ke array
 */
const formDataToObject = (v) => {
  const obj = {};
  v.forEach((value,key,fd) => {
    if(key.substr(key.length-2) === '[]') {
      obj[key.substr(0,key.length-2)] = obj[key.substr(0,key.length-2)] ?? [];
      obj[key.substr(0,key.length-2)].push(value);
    }
    else obj[key] = value
  })
  return obj;
}

const isObject = (v) => (v !== undefined) && (v !== null) && (v.constructor.name === 'Object') && (!Array.isArray(v));

const isString = (v) => (v !== undefined) && (v !== null) && (v.constructor.name === 'String');

const isNumber = (v) => (v !== undefined) && (v !== null) && (v.constructor.name === 'Number');

const isEmpty = (v) => (v !== undefined) && (v !== null) && (v !== '') && ((v.length | Object.keys(v).length) < 1);

const isArray = (v) => (v !== undefined) && (v !== null) && (v.constructor.name === 'Array');

const isClassIntance = (v) => (v !== undefined) && (v !== null) && (v.constructor.name !== 'Object') && (v.constructor.name !== 'Array') && (v.constructor.name !== 'Function') && (v.constructor.name !== 'String') && (v.constructor.name !== 'Number');

const isFunction = (v) => (v !== undefined) && (v !== null) && (v.constructor.name === 'Function');

const findAncestor = function(el, sel) {
  while ((el = el.parentElement) && !((el.matches || el.matchesSelector).call(el,sel)));
  return el;
}

// event
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

/**
 * Urutan:
 * 1. jika ada text, maka text dicopy;
 * 2. jika ada event, maka pakai text didalam event target
 * 3. jika window selection type 'Range' maka pakai text dalam anchorNode nya
 * 4. jika ada ContextMenu dan ada anchorNode nya, pakai text dalam anchorNode nya
 * @param {*} event 
 * @param {*} text 
 * @returns 
 */
function copy(event, text)
{
  if(text) {
    navigator.clipboard.writeText(text); // output promise
    return;
  }
  let a;
  const selection = window.getSelection();
  if(event) a = event.target;
  else if(selection.type === 'Range') a = selection.anchorNode;
  else if(this.ContextMenu && this.ContextMenu.anchorNode) a = this.ContextMenu.anchorNode;
  
  if(a){
    const range = new Range();
  
    // Start range at second paragraph
    range.setStartBefore(a);
  
    // End range at third paragraph
    range.setEndAfter(a);
  
    // Add range to window selection
    selection.addRange(range);
  
    navigator.clipboard.writeText(range.toString()); // output promise
  }
  return;
}

export {
  array_unique, formDataToObject, isObject, isNumber, isEmpty, 
  isString, isArray, isClassIntance, isFunction, findAncestor,
  isArrowDownKeyPress, isArrowUpKeyPress,isEnterKeyPress, isEscapeKeyPress, isLeftClick, isRightClick,
  copy
};