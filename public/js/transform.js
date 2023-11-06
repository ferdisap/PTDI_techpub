function transform(formElement){
  
  const formData = new FormData(formElement);
  const method = formElement.method;
  const action = formElement.action

  const xhr = new XMLHttpRequest();
  
  xhr.open(method,action);
  xhr.responseType = 'json';
  xhr.onload = (progressEvent) => {
    // const parser = new DOMParser();
    // console.log(window.result = xhr.response);
    // window.response = xhr.response;

    // let dmc = parser.parseFromString(xhr.responseText, 'application/xml');
    // window.dmc = dmc;
    
    console.log(window.response = xhr.response);
    let container = document.querySelector('#web-transformed');
    let iframe = document.createElement('iframe');
    iframe.style.width="inherit";
    iframe.style.height="inherit";

    // iframe.src = "/requestFile/n219/dump.xml?dataType=dump";
    iframe.src = xhr.response.url
    container.innerHTML = '';
    container.appendChild(iframe);

    // iframe = container.firstElementChild;
    // iframe.contentDocument.firstElementChild = dmc.firstElementChild;

    // container.appendChild(dmc.firstElementChild);
    // container.innerHTML = '';
    // container.append(dmc.documentElement);

    // let iframe = document.querySelector('iframe#web-transformed');
    // iframe.contentDocument.open();
    // iframe.contentDocument.documentE
    // iframe.contentDocument = dmc;
    // iframe.contentDocument.write("<p>Hello world!</p>");
    // iframe.contentDocument.close();

  }

  xhr.send(formData);
  // console.log(method, action);
  // console.log(window.formData = formData)
  
}