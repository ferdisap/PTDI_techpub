// function createXML(create = [null, 'root'], method = 'GET', url ="", utility = 'getfile', ct = 'xml', path = "", ){
function createXML(create = [null, 'root'], path = null, method = null,){

  if (method == null && path == null){
    let namespace = create[0];
    let root = create[1];
    return Promise.resolve(document.implementation.createDocument(namespace, root, null));
  } 
  else if(method == 'GET'){
    xhr = new XMLHttpRequest();

    let prom = new Promise((resolve,reject) => {
      // xhr.open(method, `${url}/?utility=${utility}&ct=${ct}&path=${path}` , false); 
      xhr.open(method, path, false);
      xhr.onload = () => {
        if (xhr.status >= 200 && xhr.status < 300) {
          // console.log(window.xhr = xhr);
          if(xhr.getResponseHeader("Content-Type") == 'application/json'){
            return resolve(JSON.parse(xhr.responseText));
          } else {
            return resolve(xhr.responseXML);
          }
          // if (ct == 'xml'){
          //   return resolve(xhr.responseXML)
          // } else {
          //   return resolve(JSON.parse(xhr.responseText));              
          // }
        } else {
            return resolve(false);
            // reject({
            //     status: this.status,
            //     statusText: xhr.statusText
            // });
            // console.log(path)
        }
      };
      // xhr.onerror = () => {
          // reject({
          //     status: this.status,
          //     statusText: xhr.statusText
          // });
          // console.log(path);
      // };
      xhr.send();
    });
    return prom;
  }

  
}