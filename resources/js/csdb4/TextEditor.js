import { EditorView, gutter, lineNumbers, GutterMarker, keymap } from "@codemirror/view";
import { defaultKeymap } from "@codemirror/commands";
import Randomstring from "randomstring";
import { isArray } from "./helper";

class TextEditor {
  id = undefined;
  editor = {};

  constructor(id) {
    this.id = id ?? Randomstring.generate({ charset: 'alphabetic' });
  }

  attachEditor() {
    const container = document.getElementById(this.id);
    container.value = new Proxy(this, {
      //  ...value. t, adalah target, k adalah key
      get: (t,k,v) => {}
    })
    this.editor = new EditorView({
      doc: '',
      extensions: [keymap.of(defaultKeymap), EditorView.lineWrapping, gutter({ class: "cm-mygutter" })],
      // extensions: [keymap.of(defaultKeymap), EditorView.lineWrapping, lineNumbers(), gutter({ class: "cm-mygutter" })],
      parent: container
    });
  }

  changeText(text = '', from = 0, to = -1) {
    if(isArray(text)) text = text.join("\n");
    else if (!text  && text != '') return;
    if (to < 0) to = this.getText().length; // harapannya jika to 0 maka akan nambah text di awal
    
    this.editor.dispatch({ changes: { from: from, to: to, insert: text } });
  }

  // src: https://stackoverflow.com/questions/8627902/how-to-add-a-new-line-in-textarea-element
  // src: https://stackoverflow.com/questions/15433188/what-is-the-difference-between-r-n-r-and-n
  // &#10; Line Feed and &#13; Carriage Return
  // \r = CR (Carriage Return) → Used as a new line character in Mac OS before X
  // \n = LF (Line Feed) → Used as a new line character in Unix/Mac OS X
  // \r\n = CR + LF → Used as a new line character in Windows
  // line yang dihasilkan dari editor adalah '\n' (unix)
  getText(lineType = '') {
    let line;
    switch (lineType) {
      case 'html-entity':
        line = '<br/>'
        break;
      case 'entity-code':
        line = '&#10;'
        break;
    }
    return line ? this.editor.state.doc.toString().replace(/\n/gm,line) : this.editor.state.doc.toString();
  }
}

export default TextEditor;