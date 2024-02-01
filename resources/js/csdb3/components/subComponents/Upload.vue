<script>
import { useTechpubStore } from '../../../techpub/techpubStore';

export default {
  data(){
    return {
      techpubStore: useTechpubStore(),
      showEntityViewer: false,
    }
  },
  methods: {
    readURL(evt) {
      let file = evt.target.files[0];
      if (file) {
        if (file.type == 'text/xml') {
          alert('you will be moved to editor page.');
          // push route to editor page here
          const reader = new FileReader();
          reader.onload = () => {
            this.techpubStore.readText = reader.result;
            this.$router.push({name: 'Editing-Editor'});
          }
          reader.readAsText(file);
        }
        else {
          this.showEntityViewer = true;
          $('#entity-viewer').attr('type', file.type).attr('src', URL.createObjectURL(file));
          this.showEntityViewer = true;
          this.showEditor = false;
        }
      }
    },
    uploadICN(){
      const formData = new FormData(event.target);
      const route = this.techpubStore.getWebRoute('api.upload_ICN', formData);
      axios({
        url: route.url,
        method: route.method[0],
        data: formData
      })
      .then(response => this.$root.success(response))
      .catch(error => this.$root.error(error));
    },
    
  },

}
</script>
<template>
  <div @click="tes">click test here</div>
  <form class="mb-3" enctype="multipart/form-data" @submit.prevent="uploadICN()">
    <h1>Upload File</h1>
    <div class="mb-5">
      <span>The file can be anything as csdb object.</span> <br/>
      <span class="text-sm">CAGE code atau Security Classification akan memakai latest jika kosong.</span>
      <input type="text" name="cagecode" placeholder="type the company cage Code" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
      <div class="text-red-600" v-html="techpubStore.error('cagecode')"></div>
      <input type="text" name="securityClassification" placeholder="type the security classificationa code" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
      <div class="text-red-600" v-html="techpubStore.error('securityClassification')"></div>
      
      <input type="file" id="entity" name="entity" @change="readURL($event)"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
      <div class="text-red-600" v-html="techpubStore.error('entity')"></div>
    </div>
    <div v-show="showEntityViewer" class="h-max mt-3">
      <div>
        <embed id="entity-viewer" style="max-height:300px; width:auto" width="100%" height="600" />
      </div>
    </div>
    <button type="submit" class="button-violet">Upload</button>
  </form>
</template>