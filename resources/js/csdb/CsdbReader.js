import { request } from "./request";

class CsdbReader {
  /**
   * @param {string} filename 
   */
  constructor(filename){
    this.filename = filename;
  }

  /**
   * return file
   */
  get csdb(){
    return request("/route/get_request_csdb_object",'GET', {
      filename:this.filename});
  }

  /**
   * @param {HTMLElement} element
   */
  container;

  render(utility = 'detail'){
    let iframe = document.createElement('iframe');
    iframe.style.width = "100%";
    iframe.style.height = "70vh";
    iframe.src = `/route/get_transform_csdb/?filename=${this.filename}&utility=${utility}`;
    this.container.innerHTML = '';
    this.container.appendChild(iframe);
  }
}

window.CsdbReader = CsdbReader;