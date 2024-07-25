

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


export {array_unique, formDataToObject, isObject, isNumber, isEmpty, 
  isString, isArray, isClassIntance, isFunction, findAncestor};