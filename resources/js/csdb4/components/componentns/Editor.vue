<script>
import { EditorView, gutter, lineNumbers, GutterMarker, keymap } from "@codemirror/view";
import { defaultKeymap } from "@codemirror/commands";
import { useTechpubStore } from '../../../techpub/techpubStore';
import axios from "axios";
export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      editor: undefined, // editor.state.doc.toString() // untuk ngambil isi text nya
      isUpdate: false,
      rn_update: 'api.update_object',
      rn_create: 'api.create_object',
    }
  },
  props: ['text', 'filename'],
  computed: {
    getEditor(){
      if(this.editor){
        return this.editor.dom.outerHTML;
      }
    },
  },
  methods: {
    changeText(text = '', from = 0, to = undefined){
      if(!to){
        to = this.editor.state.doc.toString().length;
      }
      this.editor.dispatch({changes:{from:from, to:to, insert:text}});
    },
    async setUpdate(){
      this.isUpdate = true;
      let raw = await this.getRaw(this.$props.filename);
      this.changeText(raw);
    },
    setCreate(){
      this.isUpdate = false;
      this.changeText('');
      
      console.log(this.$props.filename);
      console.log(this.isCreate, !this.$props.filename, this.isUpdate);
    },
    async getRaw(filename){
      let response = await axios({
        route: {
          name: 'api.request_csdb_object',
          data: {
            filename: filename
          }
        }
      });
      if(response.statusText == 'OK'){
        return response.data
      }
    },
    async create() {
      const formData = new FormData(event.target);
      formData.append('xmleditor', this.editor.state.doc.toString());
      let response = await axios({
        route: {
          name: this.rn_create,
          data: formData,
        }
      })
      if(response.statusText === 'OK'){
        // response harus ada SQL object model. 
        // akan emit event ke Explorer dengan data berupa model, 
        // kemudian model di push ke Listtree object untuk di update listtree nya
        // kemudian preview akan reload sesuai dengan model tersebut
        // kemudian history di reload sesuai model
        // kemudian Ident status di reload sesuai model
        // kemudian Folder di reload sesuai path model
        // item bottomBar yang lain di set false (hide)
      }
    },
    async update() {
      const formData = new FormData(event.target);
      formData.append('xmleditor', this.editor.state.doc.toString());
      formData.append('filename', this.$props.filename);
      let response = await axios({
        route: {
          name: this.rn_update,
          data: formData
        }
      })
      if (response.statusText === 'OK') {
        this.emitter.emit('updateObjectFromEditor', { filename: this.$props.filename });
        // kemudian history, identStatus di set false (hide) agar tidak memberatkan saat live preview
      }
    },
    submit() {
      return !this.isUpdate ? this.create() : this.update();
    } 
  },
  async mounted() {
    this.editor = new EditorView({
      doc: '',
      extensions: [keymap.of(defaultKeymap), EditorView.lineWrapping, lineNumbers(), gutter({ class: "cm-mygutter" })],
      parent: document.querySelector('#xml-editor-container')
    });
    // this.editor.state.doc.toString() // untuk ngambil isi text nya

    if (this.$props.text) {
      this.changeText(this.$props.text);
    }
    else if (this.$props.filename) {
      let raw = await this.getRaw(this.$props.filename);
      this.changeText(raw);
    }

    if(this.$props.filename){
      this.isUpdate = true;
    }
  }
}
</script>
<style>
#xml-editor-container {
  background-color: rgba(0, 0, 0, 0); 
}
.cm-editor {
  height: 700px;
}
</style>
<template>
  <form @submit.prevent="submit">
    <h1 class="px-3">XML Editor</h1>
    <a href="#" v-if="!isUpdate" @click.prevent="setUpdate()" class="mx-3 underline text-blue-600">Change to Update</a>
    <a href="#" v-else @click.prevent="setCreate()" class="mx-3 underline text-blue-600">Change to Create</a>
    <div class="h-max mt-3">
      <div class="mb-2 px-3 flex space-x-3">
        <!-- validation -->
        <div class="w-1/2">
          <span class="text-gray-900 dark:text-white text-xl font-bold">Validation</span>
          <span> Optional.</span>
          <br />
          <div class="flex px-3">
            <div class="block w-2/4">
              <label for="xsi_validate" class="text-gray-900 dark:text-white text-sm font-light">XSI Validate</label>
              <br />
              <label for="brex_validate" class="text-gray-900 dark:text-white text-sm font-light">BREX Validate</label>
            </div>
            <div class="block w-1/4">
              <input type="checkbox" id="xsi_validate" name="xsi_validate" checked />
              <br />
              <input type="checkbox" id="brex_validate" name="brex_validate" />
            </div>
          </div>
        </div>
      </div>
      <!-- editor -->
      <div id="xml-editor-container" class="text-xl mb-2"></div>
      <div class="text-red-600" v-html="techpubStore.error('xmleditor')"></div>
      <br />
    </div>
    <button type="submit" name="button" class="button bg-violet-400 text-white hover:bg-violet-600">Submit</button>
  </form>
</template>