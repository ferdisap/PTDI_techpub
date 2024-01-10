<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
import { EditorView, gutter, lineNumbers, GutterMarker, keymap } from "@codemirror/view";
import { defaultKeymap } from "@codemirror/commands";
import $ from 'jquery';
import { mount } from '@vue/test-utils';
import ObjectUpdate from './ObjectUpdate.vue';

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      showEditor: true,
      showUpload: false,
      filename_current: '',
      editor: undefined,
      showEntityViewer: false,
      project_name_input: this.$props.projectName ?? '',
      dmrl_input: ''
    }
  },
  props: ['projectName', 'filename', 'utility', 'blobObject'],
  methods: {
    submit(evt) {
      this.techpubStore.showLoadingBar = true;
      this.techpubStore.Errors = [];
      const formData = new FormData(evt.target);
      window.formData = formData;
      window.evt = evt;
      let xml = this.editor.state.doc.toString();
      window.xml = xml;
      // if (!formData.get('upload')) {
      if (xml) {
        formData.append('xmleditor', xml);
        formData.append(evt.submitter.name, evt.submitter.value);
        formData.append('filename', this.$props.filename);
      } else {
        formData.append('filename', evt.target.entity.files[0].name);
      }
      // formData.append('projectName', this.project_name_input);
      axios({
        url: evt.target.action,
        method: evt.target.method,
        data: formData,
        headers: { "Content-Type": "multipart/form-data" }
      })
        .then(response => this.$root.success(response))
        .catch(error => this.$root.error(error));
    },
    async getObjectText() {
      this.filename_current = this.$props.filename;
      let filename = this.$props.filename;
      let projectName = this.$props.projectName;

      const render = (currentDetailObject) => {
        window.cur = currentDetailObject;
        if(currentDetailObject[0].includes('text')){
          this.attachEditor(currentDetailObject[1]);
        }
        else {
          let container = $("#entity-viewer")
          container.attr('src', currentDetailObject[1]);
          container.attr('type', currentDetailObject[0]);
          this.showEntityViewer = true;
        }
      }

      if(this.techpubStore.currentDetailObject.filename == filename && this.techpubStore.currentDetailObject.projectName == projectName){
        let currentDetailObject = await this.techpubStore.getCurrentDetailObject();
        render(currentDetailObject);
      }
      else {
        let route = this.techpubStore.getWebRoute('api.getobject', { projectName: projectName, filename: filename },);
        axios({
          url: route.url,
          method: route.method[0],
          data: route.params,
          responseType: 'blob',
        })
        .then(async (rsp) => {
          window.rsp = rsp;
          const currentDetailObject = await this.techpubStore.getCurrentDetailObject('',{blob: rsp.data});
          render(currentDetailObject);
        })
        .catch(error => this.$root.error(error));
      }

    },
    attachEditor(text) {
      this.text = text;
      let container = $('#xml-editor-container');
      container.css({ 'background-color': 'rgba(0, 0, 0, 0.91)' });

      let editor = new EditorView({
        doc: this.text,
        extensions: [keymap.of(defaultKeymap), EditorView.lineWrapping, lineNumbers(), gutter({ class: "cm-mygutter" })],
        // parent: document.body
      });
      this.editor = editor;
      // window.editor = editor;
      // editor.state.doc.toString() // untuk ngambil isi text nya
      container.html(editor.dom);
      container.css({ 'background-color': 'rgba(0, 0, 0, 0)' });
    },
    readURL(evt) {
      // console.log(evt.target.files);
      let file = evt.target.files[0];
      if (file) {
        const reader = new FileReader();
        if (file.type == 'text/xml') {
          reader.onload = () => {
            this.attachEditor(reader.result);
            this.showEditor = true;
            this.showEntityViewer = false;
          }
          reader.readAsText(file);
        }
        else {
          reader.onload = () => {
            let vr = $('#entity-viewer');
            vr.attr('type', file.type);
            vr.attr('src', reader.result);
            this.showEntityViewer = true;
            this.showEditor = false

            let en = $('#entity-name');
            en.text(file.name);

            let xmlEditor = $('#xml-editor-container');
            xmlEditor.html('');
          }
          reader.readAsDataURL(file);
        }
      }
    }
  },
  mounted() {
    // update
    if (this.$props.filename && this.$props.projectName) {
      this.getObjectText();
    }
    //create
    else {
      this.attachEditor('');
    }
  },
  updated() {
    if (this.$props.filename && this.$props.projectName && (this.filename_current != this.$props.filename)) {
      this.text = ''
      this.getObjectText();
    }
  }
}
</script>
<style>
.cm-editor {
  max-height: 50vh;
  min-height: 25vh
}

.cm-scroller {
  overflow: auto
}
</style>
<template>
  <h2 class="text-center"> {{ $props.utility == 'create' ? 'Create' : 'Update' }} Object </h2>
  <h4 class="text-center">{{ $props.filename }}</h4>
  <br />

  <div class="w-full text-center mb-3">
    <button :class="[showEditor ? 'border-b-black border-b-4' : '', 'button-nav']"
      @click="showEditor = !showEditor">Editor</button>
    <button :class="[showUpload ? 'border-b-black border-b-4' : '', 'button-nav']"
      @click="showUpload = !showUpload">Upload</button>
    <button :class="[showEntityViewer ? 'border-b-black border-b-4' : '', 'button-nav']"
      @click="showEntityViewer = !showEntityViewer">Entity</button>
  </div>

  <div id="tes-img-preview"></div>

  <form method="POST"
    :action="$props.utility === 'create' ? techpubStore.getWebRoute('api.post_create_csdb_object').path : techpubStore.getWebRoute('api.post_update_csdb_object').path"
    @submit.prevent="submit($event)">

  <div v-show="showUpload" class="mb-3">
    <!-- file upload -->
    <!-- <form
      :action="$props.utility === 'create' ? techpubStore.getWebRoute('api.post_create_csdb_object').path : techpubStore.getWebRoute('api.post_update_csdb_object').path"
      method="POST" @submit.prevent="submit($event)" enctype="multipart/form-data"> -->
      <input type="hidden" name="upload" value="1">
      <div class="mb-5">
        <label for="entity" class="block mb-2 text-gray-900 dark:text-white text-xl font-bold">Upload File</label>
        <span>The file can be anything as csdb object.</span>
        <input type="file" id="entity" name="entity" @change="readURL($event)"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
        <div class="text-red-600" v-html="techpubStore.error('entity')"></div>
      </div>
      <!-- <button type="submit" class="button bg-violet-400 text-white hover:bg-violet-600">Upload</button> -->
    <!-- </form> -->
  </div>

  <hr>

  <!-- xml editor -->
  <div>
  <!-- <form method="POST"
    :action="$props.utility === 'create' ? techpubStore.getWebRoute('api.post_create_csdb_object').path : techpubStore.getWebRoute('api.post_update_csdb_object').path"
    @submit.prevent="submit($event)"> -->
    <div v-show="showEditor" class="h-max mt-3">
      <div class="mb-2 flex space-x-3">
        <!-- project name -->
        <div class="w-2/5">
          <label for="project_name" class="block mb-2 text-gray-900 dark:text-white text-lg font-bold">Project Name</label>
          <span>The project name is required to be filled.</span>
          <input type="text" id="project_name" name="project_name" placeholder="type your project name .."
            v-model="project_name_input"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
          <div class="text-red-600" v-html="techpubStore.error('project_name')"></div>
        </div>
        <!-- DMRL -->
        <div class="w-2/5">
          <label for="dmrl" class="block mb-2 text-gray-900 dark:text-white text-lg font-bold">DMRL</label>
          <span>The project name is required to be filled.</span>
          <input type="text" id="dmrl" name="dmrl" placeholder="type your DMRL file .." v-model="dmrl_input"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
          <div class="text-red-600" v-html="techpubStore.error('dmrl')"></div>
        </div>
        <!-- validation -->
        <div class="w-1/4">
          <span class="block text-gray-900 dark:text-white text-xl font-bold">Validation</span>
          <span>Optional.</span>
          <br />
          <div class="flex">
            <div class="block w-2/4">
              <label for="xsi_validate" class="text-gray-900 dark:text-white text-sm font-light">XSI Validate</label>
              <br />
              <label for="brex_validate" class="text-gray-900 dark:text-white text-sm font-light">BREX Validate</label>
            </div>
            <div class="block w-1/4">
              <input type="checkbox" id="xsi_validate" name="xsi_validate" checked/>
              <br />
              <input type="checkbox" id="brex_validate" name="brex_validate" checked/>
            </div>
          </div>
        </div>
      </div>
      <!-- editor -->
      <div id="xml-editor-container" class="text-xl mb-2" style="background-color:rgba(0, 0, 0, 0.091)"></div>
      <div class="text-red-600" v-html="techpubStore.error('xmleditor')"></div>
      <br />
    </div>
  <!-- </form> -->
  </div>
    <!-- button -->
    <div class="flex justify-end">
      <button v-if="$props.utility != 'create'" type="submit" name="button" value="commit"
        class="button bg-violet-400 text-white hover:bg-violet-600">Commit</button>
      <button type="submit" name="button" :value="$props.utility ?? 'update'"
        class="button bg-violet-400 text-white hover:bg-violet-600"> {{ $props.utility === 'create' ? 'Create' :
          'Update' }} </button>
    </div>
  </form>

  <!-- entity viewer -->
  <div v-show="showEntityViewer" class="h-max mt-3">
    <span class="text-gray-900 dark:text-white text-light">Preview Entity</span><br />
    <span id="entity-name" class="block mb-2 text-gray-900 dark:text-white text-xl font-bold"></span><br />
    <embed id="entity-viewer" class="max-w-full" />
  </div>
</template>