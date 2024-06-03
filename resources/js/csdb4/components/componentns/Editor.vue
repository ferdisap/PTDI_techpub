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
      isFile: false,
      rn_update: 'api.update_object',
      rn_create: 'api.create_object',
    }
  },
  props: ['filename'],
  computed: {
    getEditor(){
      if(this.editor){
        return this.editor.dom.outerHTML;
      }
    },
    isFileUpload(){
      if( this.isFile 
          && (
            (this.$props.filename && this.$props.filename.slice(0,3) === 'ICN')
            || !this.isUpdate
          )
      ){
      // if(!this.$props.filename || (this.isFile && this.$props.filename.slice(0,3) === 'ICN')){
      // if(this.isFile && this.$props.filename.slice(0,3) === 'ICN'){
        return true;
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
      if(this.$props.filename.slice(0,3) === 'ICN'){
        this.isFile = true;
        this.rn_update = 'api.update_ICN';
      } else {
        this.isFile = false;
        this.rn_update = 'api.update_object';
        let raw = await this.getRaw(this.$props.filename);
        this.changeText(raw);
      }
    },
    setCreate(){
      this.isUpdate = false;
      this.isFile = false;
      this.changeText('');
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
    async create(event) {
      if(!this.isFileUpload){
        const formData = new FormData(event.target);
        formData.append('xmleditor', this.editor.state.doc.toString());
        let response = await axios({
          route: {
            name: this.rn_create,
            data: formData,
          }
        })
        if(response.statusText === 'OK'){
          this.emitter.emit('createObjectFromEditor', { model: response.data.data });
          // response harus ada SQL object model. 
        }
      } else {
        const formData = new FormData(event.target);
        console.log(window.fd = formData);
        let response = await axios({
          route: {
            name: 'api.upload_ICN',
            data: formData
          }
        });
        if(response.statusText === 'OK'){
          this.emitter.emit('createICNFromEditor', { model: response.data.data });
        }
      }
    },
    async update(event) {
      console.log('update');
      let emitName;
      const formData = new FormData(event.target);
      if(!this.isFileUpload){
        formData.append('xmleditor', this.editor.state.doc.toString());
        formData.append('filename', this.$props.filename);
        emitName = 'updateObjectFromEditor';
      } else {
        formData.append('filename', this.$props.filename);
        emitName = 'updateICNFromEditor';
      }
      let response = await axios({
        route: {
          name: this.rn_update,
          data: formData
        }
      })
      if (response.statusText === 'OK') {
        this.emitter.emit(emitName, { model: response.data.data });
      }
    },
    submit(event) {
      return !this.isUpdate ? this.create(event) : this.update(event);
    },
    switchTo(name){
      switch (name) {
        case 'editor':
          this.isFile = false; 
          setTimeout(() => {
            document.querySelector('#xml-editor-container').innerHTML = this.editor.dom.outerHTML;
          });
          break;
        case 'file-upload':
          this.isFile = true
          break;
        default:
          break;
      }
    },
    readURL(evt) {
      let file = evt.target.files[0];
      if (file) {
        if (file.type == 'text/xml') {
          alert('you will be moved to xml editor.');
          // push route to editor page here
          const reader = new FileReader();
          reader.onload = () => {
            this.switchTo('editor');
            this.changeText(reader.result);
            this.emitter.emit('readTextFileFromEditor');
          }
          reader.readAsText(file);
        }
        else {
          this.emitter.emit('readFileURLFromEditor', {
              mime: file.type,
              sourceType: 'url',
              source: URL.createObjectURL(file),
            });
        }
      }
    },
  },
  async mounted() {
    this.editor = new EditorView({
      doc: '',
      extensions: [keymap.of(defaultKeymap), EditorView.lineWrapping, lineNumbers(), gutter({ class: "cm-mygutter" })],
      parent: document.querySelector('#xml-editor-container')
    });
    // this.editor.state.doc.toString() // untuk ngambil isi text nya

    if(this.$props.filename){
      this.setUpdate();
      if(this.$props.filename.slice(0,3) !== 'ICN') {
        let raw = await this.getRaw(this.$props.filename);
        this.changeText(raw);
      }
    }

  }
}
</script>
<style>
#xml-editor-container {
  background-color: rgba(0, 0, 0, 0); 
}
.cm-editor {
  height: 600px;
}
</style>
<template>
  <div class="editor px-3">
    <div class="h-[5%] mb-3">
      <h1 class="text-blue-500 w-full text-center">Editor</h1>
      <a href="#" v-if="!isUpdate" @click.prevent="setUpdate()" class="block text-center text-sm underline text-blue-600">Switch to Update</a>
      <a href="#" v-else @click.prevent="setCreate()" class="block text-center text-sm underline text-blue-600">Switch to Create</a>
    </div>

    <form v-if="isFileUpload" class="mb-3" enctype="multipart/form-data" @submit.prevent="submit($event)">
    <!-- <form v-if="isFileUpload" class="mb-3" enctype="multipart/form-data" method="POST" action="/api/uploadICN"> -->
      <h1>File Upload
        <a @click="switchTo('editor')" href="#" class="font-normal text-sm underline text-blue-600">Switch to XML editor</a><br/>
      </h1>
      <span class="text-sm">The file can be anything as csdb object.</span>
      <div class="mb-5">
        <div class="flex space-x-3">
          <label for="brex_validate" class="text-gray-900 dark:text-white text-sm font-light">BREX Validate</label>
          <input type="checkbox" id="brex_validate" name="brex_validate" />
        </div>
        <div v-if="isUpdate"> {{ $props.filename }} </div>
        <div class="flex space-x-3 w-full mb-2">
          <div v-if="!isUpdate" class="block w-[70%]">
            <label for="icn-filename" class="text-sm">Filename</label><br/>
            <input type="text" id="icn-filename" name="filename" placeholder="filename without extension" class="py-1 w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg" /> 
            <div class="error text-sm text-red-600" v-html="techpubStore.error('filename')"></div>
          </div>
          <div v-if="!isUpdate" class="block w-auto">
            <label for="securityClassification" class="text-sm">Security Classification</label><br/>
            <input type="text" name="securityClassification" placeholder="type the SC code" class="py-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg" />
            <div class="error text-sm text-red-600" v-html="techpubStore.error('securityClassification')"></div>
          </div>
        </div>
        <input type="file" id="entity" name="entity" @change="readURL($event)" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
        <div class="error text-sm text-red-600" v-html="techpubStore.error('entity')"></div>
      </div>
      <button type="submit" name="button" class="button bg-violet-400 text-white hover:bg-violet-600">{{ !this.isUpdate ? 'create' : 'update' }}</button>
    </form>
    <form v-else @submit.prevent="submit($event)">
      <h1 class="">XML Editor
        <a @click="switchTo('file-upload')" href="#" class="font-normal text-sm underline text-blue-600">Switch to file upload</a><br/>
      </h1>
      <span class="text-sm">Only text in XML form can be processed.</span>
      <div class="h-max">
        <div class="mb-2 flex space-x-3">
          <div class="w-1/2">
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
        <div class="error text-sm text-red-600" v-html="techpubStore.error('xmleditor')"></div>
        <br />
      </div>
      <button type="submit" name="button" class="button bg-violet-400 text-white hover:bg-violet-600">{{ !this.isUpdate ? 'create' : 'update' }}</button>
    </form>
  </div>
</template>