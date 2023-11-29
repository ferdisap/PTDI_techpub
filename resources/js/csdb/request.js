/**
 * params is object
 */
export function request(url, method ,params = {})
{
  let result = '';
  try {
    url = new URL(url); 
  } catch (error)
  {
    url = new URL(`${window.location.origin}${url}`);
  }
  const xhr = new XMLHttpRequest();

  xhr.onload = () => {
    if (xhr.status >= 200 && xhr.status < 300) {
      if(xhr.getResponseHeader('Content-Type').includes('xml')){
        return result = xhr.responseXML;
      }
      if(xhr.getResponseHeader('Content-Type').includes('json')){
        result = JSON.parse(xhr.responseText);
        return result;
      }
    }
  }
  if(method == 'GET'){
    url.search = new URLSearchParams(params);
    xhr.open(method, url,false);
    xhr.send();
  }
  else if(method == 'POST'){
    xhr.open(method, url,false);
    xhr.setRequestHeader('X-CSRF-Token', document.querySelector('meta[name=csrf-token]').content);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send(new URLSearchParams(params));
  }
  
  return result;
}

