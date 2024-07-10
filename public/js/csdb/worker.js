import setListTreeData from './setListTreeData.js';

const ListTree = {
  request: async function (e) {
    let component = e.data.component;
    let response;
    if (Object.values(component.route.method).includes('GET') || component.route.method.includes('HEAD')) {
      response = await fetch(component.route.url);
    }
    return new Promise(async (resolve, reject) => {
      if (response.ok) {
        let json = await response.json();
        resolve(setListTreeData(json.data));
      } else {
        reject([]);
      }
    })
  },
  /**
 * @return {Array}
 * @param {Object} response from axios Response 
 */
  // setListTreeData: function (data){
  //   return setListTreeData(data);
  // }
}

onmessage = async function (e) {
  let ret;
  let component = e.data.component;
  if (component && component.name === 'ListTree') {
    ret = await ListTree.request(e);
  }
  // else if(component && )
  postMessage(ret);
}
