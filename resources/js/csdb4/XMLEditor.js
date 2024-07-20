import { EditorView, gutter, lineNumbers, GutterMarker, keymap } from "@codemirror/view";
import { defaultKeymap } from "@codemirror/commands";
import Randomstring from "randomstring";
import {isProxy, toRaw} from 'vue';

/**
 * cara pakai:
 * 1. instance class
 * 2. setRoute(techpubRoute)
 * 3. run attachEditor(),
 * 3. fetchRaw() or changeText()
*/

const privateData = new WeakMap();

class XMLEditor{

  process = undefined;
  timeout = 0;

  route = {};
  editor = 'foo';
  id = '';

  constructor(id = ''){
    if(!id) id = Randomstring.generate({ charset: 'alphabetic' });
    this.id = id;
    // source: https://stackoverflow.com/questions/52001178/restrict-javascript-class-instances-to-particular-properties
    return new Proxy(this, {
      get: (context,prop) => this[prop],
      set: (context,prop,value) => {
        switch (prop) {
          case 'id':
            throw new Error(`Cannot set ${prop}.`);
            break;
          default:
            context[prop] = value;
            break;
        }
      },
    })
  }

  setRoute = (techpubRoute) => {
    this.route = techpubRoute;
    // ubah formData to object
    if(techpubRoute.params instanceof FormData){
      this.route.params = {};
      techpubRoute.params.keys().forEach(k => {
        let v = techpubRoute.params.getAll(k); // output array
        if(v.length === 1) this.route.params[k] = v[0];
        else if(v.length > 1) this.route.params = v;
        // tidak di set jika valuenya empty;
      });
    }
  }

  /**
   * pakai fungsi anonymouse agar thisnya merujuk ke proxy yang dibuat constructor
   */
  attachEditor = () => {
    this.editor = new EditorView({
      doc: '',
      extensions: [keymap.of(defaultKeymap), EditorView.lineWrapping, lineNumbers(), gutter({ class: "cm-mygutter" })],
      parent: document.querySelector('#'+this.id),
    });
  }
  
  fetchRaw = () => {
    this.changeText(' ON LOADING...');
    this.process = new Promise((r, j) => {
      clearTimeout(this.timeout);
      this.isDone = false;
      this.timeout = setTimeout(()=>{
        let worker;
        if(window.Worker){
          worker = new Worker('/worker/WorkerXMLEditor.js',{type: "module"});
        }
        if(!worker) return false;
        worker.onmessage = (e) => {
          this.changeText(e.data);
          worker.terminate();
        }
        worker.postMessage({route: this.route});
      },500);
    });

    return this.process;
  }

  changeText = (text = undefined, from = 0, to = 0) => {
    if(text === undefined || text === null){
      this.fetchRaw();
    }
    else {
      to = this.getTextLength();
      this.editor.dispatch({changes:{from:from, to:to, insert:text}});
    }
  }

  getTextLength = () => {
    return this.editor.state.doc.toString().length;
  }
}

export default XMLEditor