import Randomstring from "randomstring";
import { useTechpubStore } from "../techpub/techpubStore";
import axios from "axios";
import mitt from 'mitt';

class CheckboxSelector{
  cbHovered = ''; // checkbox input id
  isSelectAll = false;
  selectionMode = false;
  id = ''; 
  isShowTriggerPanel = false;

  constructor(){
    this.id = Randomstring.generate({charset:'alphabetic'});
  }

  select(cbid = ''){
    if(!cbid) cbid = this.cbHovered;
    this.isSelectAll = false;
    this.selectionMode = true;
    this.isShowTriggerPanel = false
    setTimeout(()=>{
      let input = document.getElementById(cbid);
      input.checked = !input.checked
    },100);
    // setTimeout(()=>document.getElementById(cbid).checked = true,100);
  }

  selectAll(isSelect = true, cssInputSelector = ''){
    this.selectionMode = true;
    this.isShowTriggerPanel = false;
    if(!cssInputSelector) cssInputSelector = "#"+this.id+" input[type='checkbox']";
    setTimeout(()=>document.querySelectorAll(cssInputSelector).forEach((input) => input.checked = isSelect),0)
    this.isSelectAll = isSelect;
  }

  cancel(){
    this.selectAll(false);
    this.selectionMode = false;
  }

  /**
   * 
   * @param {boolean} isSelect 
   * @param {string} cssInputSelector 
   * @returns {array} contain string input value
   */
  getAllSelectionValue(isSelect = true, cssInputSelector = ''){
    let values = new Array;
    if(!cssInputSelector) {
      cssInputSelector = isSelect ? "#"+this.id+" input[type='checkbox']:checked" : "#"+this.id+" input[type='checkbox']:not(:checked)"
    };
    document.querySelectorAll(cssInputSelector).forEach((inputEl)=> {
      if(inputEl.checked = isSelect){
        values.push(inputEl.value);
      }
    });
    return values;
  }

  /**
   * 
   * @param {boolean} isSelect 
   * @param {string} cssInputSelector 
   * @returns {NodeList} contain string input value
   */
  getAllSelectionElement(isSelect = true, cssInputSelector = ''){
    if(!cssInputSelector) {
      cssInputSelector = isSelect ? "#"+this.id+" input[type='checkbox']:checked" : "#"+this.id+" input[type='checkbox']:not(:checked)"
    };
    return document.querySelectorAll(cssInputSelector);
  }
  
}

class CsdbObjectCheckboxSelector extends CheckboxSelector {

  context;

  constructor(component){
    super();
    this.context = component
  }

  /**
   * makesure value checkbox is filename to put to server
   * @return {Promise} berisi string filename yang di change path 
   */
  async changePath(event, config, callback){
    let resolve,reject;
    const prom = new Promise((r,j) => {
      resolve = r; reject = j;
    })

    if(!(config)){
      config = {};
      config.data = new FormData(event.target);
    }

    const data = config.data;
    const routeName = config.routeName ? config.routeName : 'api.change_object_path';

    let value = this.selectionMode ? this.getAllSelectionValue() : [document.getElementById(this.cbHovered).value];
    if(data instanceof FormData) data.set('filename', value);
    else data.filename = value;
    
    const route = useTechpubStore().getWebRoute(routeName,data);
    
    let response = await axios({
      url: route.url,
      method: route.method[0],
      data: route.params,
    });

    if(response.statusText === 'OK'){
      (callback.bind(this))();
      resolve(value);
    } else {
      reject(false);
    }
    return prom;
  }
}

export {CheckboxSelector, CsdbObjectCheckboxSelector};