<script>
import { EditorView, gutter, lineNumbers, GutterMarker, keymap } from "@codemirror/view";
import { defaultKeymap } from "@codemirror/commands";
import { useTechpubStore } from '../../../techpub/techpubStore';
import axios from "axios";
import $ from 'jquery';
export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      showEditor: true,
      showEntityViewer: false,
      editor: undefined,
      rn_update: 'api.update_object',
      rn_create: 'api.create_object',
    }
  },
  props:['text', 'routeNameCreate', 'routeNameUpdate', 'title'],
  methods: {
    attachEditor(text = '') {
      let container = $('#xml-editor-container');
      container.css({ 'background-color': 'rgba(0, 0, 0, 0.91)' });

      let editor = new EditorView({
        doc: text,
        extensions: [keymap.of(defaultKeymap), EditorView.lineWrapping, lineNumbers(), gutter({ class: "cm-mygutter" })],
      });
      this.editor = editor;
      // editor.state.doc.toString() // untuk ngambil isi text nya
      container.html(editor.dom);
      container.css({ 'background-color': 'rgba(0, 0, 0, 0)' });
    },
    create(){
      const formData = new FormData(event.target);
      formData.append('xmleditor', this.editor.state.doc.toString());
      const route = this.techpubStore.getWebRoute(this.rn_create, formData);
      axios({
        url: route.url,
        method: route.method[0],
        data: formData
      })
      .then(response => this.$root.success(response))
      .catch(error => this.$root.error(error));
    },
    update(){
      const formData = new FormData(event.target);
      formData.append('xmleditor', this.editor.state.doc.toString());
      formData.append('filename', this.$route.params.filename);
      const route = this.techpubStore.getWebRoute(this.rn_update, formData);
      axios({
        url: route.url,
        method: route.method[0],
        data: formData
      })
      .then(response => this.$root.success(response))
      .catch(error => this.$root.error(error));
    },
    submit(filename){
      return filename ? this.update() : this.create();
    }
  },
  mounted(){
    if(this.$props.routeNameCreate) {
      this.rn_create = this.$props.routeNameCreate;
    }
    if(this.$props.routeNameUpdate) {
      this.rn_update = this.$props.routeNameUpdate;
    }
    this.attachEditor(this.$props.text ? this.$props.text : this.techpubStore.readText);
  }
}
</script>
<style>
.cm-editor {
  height: 300px;
}
</style>
<template>
  <h1>{{ $props.title }}</h1>
  <form @submit.prevent="submit($route.params.filename)">
    <h1>XML Editor</h1>
    <div v-show="showEditor" class="h-max mt-3">
      <div class="mb-2 flex space-x-3">
        <!-- validation -->
        <div class="w-1/4">
          <span class="text-gray-900 dark:text-white text-xl font-bold">Validation</span>
          <span> Optional.</span>
          <br />
          <div class="flex">
            <div class="block w-2/4">
              <label for="xsi_validate" class="text-gray-900 dark:text-white text-sm font-light">XSI Validate</label>
              <br />
              <label for="brex_validate" class="text-gray-900 dark:text-white text-sm font-light">BREX Validate</label>
            </div>
            <div class="block w-1/4">
              <input type="checkbox" id="xsi_validate" name="xsi_validate" />
              <br />
              <input type="checkbox" id="brex_validate" name="brex_validate" />
            </div>
          </div>
        </div>
      </div>
      <!-- editor -->
      <div id="xml-editor-container" class="text-xl mb-2" style="background-color:rgba(0, 0, 0, 0.091)"></div>
      <div class="text-red-600" v-html="techpubStore.error('xmleditor')"></div>
      <br />
    </div>
    <button type="submit" name="button" class="button bg-violet-400 text-white hover:bg-violet-600">Submit</button>
  </form>
</template>