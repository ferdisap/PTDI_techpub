const array_unique = (arr) => {
  return arr.filter((value,index,a) => a.indexOf(value) === index);
};

const isObject = (obj) => {
  return typeof obj === 'object' && !Array.isArray(obj) && obj !== null
}

export {array_unique, isObject};