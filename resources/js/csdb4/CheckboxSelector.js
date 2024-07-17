import Randomstring from "randomstring";

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
    console.log(isSelect);
    this.selectionMode = true;
    this.isShowTriggerPanel = false;
    if(!cssInputSelector) cssInputSelector = "#"+this.id+" input[type='checkbox']";
    setTimeout(()=>document.querySelectorAll(cssInputSelector).forEach((input) => input.checked = isSelect),0)
    this.isSelectAll = isSelect;
  }

  /**
   * 
   * @param {boolean} isSelect 
   * @param {string} cssInputSelector 
   * @returns {array} contain string input value
   */
  getAllSelectionValue(isSelect = true, cssInputSelector = ''){
    let values = new Array;
    if(!cssInputSelector) cssInputSelector = "#"+this.id+" input[type='checkbox']";
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

export default CheckboxSelector;