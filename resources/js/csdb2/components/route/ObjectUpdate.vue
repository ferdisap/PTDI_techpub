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
      showUpload: false
    }
  },
  methods: {
    submit(evt) {
      this.techpubStore.showLoadingBar = true;
      const formData = new FormData(evt.target);
      if (!formData.get('upload')) {
        formData.append('xmleditor', this.editor.state.doc.toString());
        formData.append(evt.submitter.name, evt.submitter.value);
      }
      formData.append('filename', this.$route.params.filename);
      axios({
        url: evt.target.action,
        method: evt.target.method,
        data: formData,
        headers: { "Content-Type": "multipart/form-data" }
      })
        .then(response => this.$root.success(response))
        // .then(response => {
        //   this.$root.showMessages = true;
        //   let messages = response.data.messages;
        //   this.$root.messages = messages;
        //   this.techpubStore.showLoadingBar = false;
        // })
        .catch(error => this.$root.error(error));
    }
  },
  async mounted() {
    let filename = this.$route.params.filename;
    let projectName = this.$route.params.projectName;
    let route = this.techpubStore.getWebRoute('api.getobject', { projectName: projectName, filename: filename },);
    let response = await axios.get(route.url.toString());
    if (response.statusText === 'OK') {
      let editor = new EditorView({
        doc: response.data,
        extensions: [keymap.of(defaultKeymap), EditorView.lineWrapping, lineNumbers(), gutter({ class: "cm-mygutter" })],
        parent: document.body
      });
      this.editor = editor;
      // editor.state.doc.toString() // untuk ngambil isi text nya
      let container = $('#xml-editor-container');
      container.html(editor.dom);
    }
  },
}
</script>
<style>
.cm-editor {
  max-height: 50vh
}

.cm-scroller {
  overflow: auto
}
</style>
<template>
  <h1 class="text-center">Update</h1>
  <h4 class="text-center">{{ $route.params.filename }}</h4>
  <br />

  <div class="w-full text-center mb-3">
    <button :class="[showEditor ? 'border-b-black border-b-4' : '', 'button-nav']"
      @click="showEditor = !showEditor">Editor</button>
    <button :class="[showUpload ? 'border-b-black border-b-4' : '', 'button-nav']"
      @click="showUpload = !showUpload">Upload</button>
  </div>

  <div v-show="showUpload" class="mb-3">
    <!-- file upload -->
    <form :action="techpubStore.getWebRoute('api.post_update_csdb_object').path" method="POST"
      @submit.prevent="submit($event)" enctype="multipart/form-data">
      <input type="hidden" name="upload" value="1">
      <div class="mb-5">
        <label for="description" class="block mb-2 text-gray-900 dark:text-white text-xl font-bold">Upload File</label>
        <span>The file can be anything as csdb object.</span>
        <input type="file" id="description" name="entity"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
          required placeholder="describe your project.." />
      </div>
      <button type="submit" class="button bg-violet-400 text-white hover:bg-violet-600">Upload</button>
    </form>
  </div>

  <hr>

  <div v-show="showEditor" class="h-max mt-3">
    <h2 class="mb-2 ml-3">XML Editor</h2>
    <div id="xml-editor-container" class="text-xl mb-2"></div>
    <div class="text-red-600" v-html="techpubStore.error('xmleditor')"></div>
    <br />
    <div class="flex justify-end">
      <form method="POST" :action="techpubStore.getWebRoute('api.post_update_csdb_object').path"
        @submit.prevent="submit($event)">
        <button type="submit" name="button" value="commit"
          class="button bg-violet-400 text-white hover:bg-violet-600">Commit</button>
        <button type="submit" name="button" value="update"
          class="button bg-violet-400 text-white hover:bg-violet-600">Update</button>
      </form>
    </div>
  </div>
</template>