import { isString } from "../helper";
/**
 * src = https://github.com/mdn/web-components-examples/blob/main/edit-word/main.js
 */
class Rm extends HTMLElement {
  editor = undefined;
  constructor(){
    super();
  }
  get value(){
    return this.editor.getText('html-entity');
  }

  set value(text){
    this.editor.changeText(text);
    return text;
  }
};

export default Rm;