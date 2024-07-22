<!-- 
  props.filename is depreciated
 -->
 <script>
import { useTechpubStore } from '../../../techpub/techpubStore';
import path from 'path';
import { contentType } from 'es-mime-types';
import PreviewRCMenu from '../../rightClickMenuComponents/PreviewRCMenu.vue';
import ContinuousLoadingCircle from '../../loadingProgress/ContinuousLoadingCircle.vue';

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      view: 'html',
      isICN: false,
      data: {},
      path: path,
      filename: '',
      showLoadingProgress: false,
    }
  },
  components:{
    PreviewRCMenu, ContinuousLoadingCircle
  },
  props: {
    dataProps: {
      type: Object,
      required: true
    }
  },
  methods: {
    /**
     * return string blob url
     * return false
    */
    async blobRequestTransformed(routename, data, type) {
      data = Object.assign(data);
      delete (data.update_at);
      let responseType = !type.includes('text') ? 'arraybuffer' : 'json';
      data.updated_at = 'Thu Mar 14 2024 15:29:29 GMT+0700';
      let response = await axios({
        route: {
          name: routename,
          data: data,
        },
        useMainLoadingBar: false,
        responseType: responseType,
      });
      if (response.statusText === 'OK') {
        let blob = new Blob([response.data], { type: type });
        let url = URL.createObjectURL(blob);
        return url;
      } else {
        return false
      }
    },
    /*
     * data bisa berisi tentang mime, source, sourceType, filename
     * jika ada filename, maka akan request ke server
    */
    icnRenderer(data = {}) {
      this.showLoadingProgress = true;
      this.isICN = true;
      this.filename = data.filename;
      // jika dari readFileURLFromEditor
      if (data.sourceType === 'url') {
        setTimeout(() => {
          this.data.mime = data.mime;
          this.data.src = data.source;
        }, 0);
      }
      else {
        URL.revokeObjectURL(this.data.src);
        const route = this.techpubStore.getWebRoute('api.get_icn_raw', { filename: data.filename });
        setTimeout(() => {
          let path = this.path.extname(data.filename);
          this.data.mime = contentType(path);
          this.data.src = route.url.toString();
        }, 100);
      }
      this.showLoadingProgress = false;
    },
    async datamoduleRenderer(data = {}) {
      this.showLoadingProgress = true;
      this.isICN = false;
      this.filename = data.filename;
      let routeName;
      if(data.viewType === 'html') routeName = 'api.get_transformed_contentpreview';
      else if(data.viewType === 'pdf') routeName = 'api.get_pdf_object';
      else if(this.view === 'html') routeName = 'api.get_transformed_contentpreview';
      else if(this.view === 'pdf') routeName = 'api.get_pdf_object';
      if (routeName) {
        this.data.mime = this.view === 'html' ? 'text/html' : (this.view === 'pdf' ? 'application/pdf' : '');
        let src = await this.blobRequestTransformed(routeName, { filename: data.filename }, this.data.mime);
        if(src){
          this.data.src = src // blob:http://127.0.0.1:8000/1a7cdf64-c7f7-4dd3-b4b2-0d26a3f0bb52
        }
      }
      this.showLoadingProgress = false;
    },
    switchView(name){
      this.view = name;
      this.datamoduleRenderer({filename: this.filename});
      this.$router.push({
          name: 'Explorer',
          params: {
              filename: this.$route.params.filename,
              viewType: name
          },
          query: this.$route.query
      });
    },
  },
  mounted() {
    if (this.$props.dataProps.filename) {
      this.filename = this.$props.dataProps.filename;
      this.$props.dataProps.filename.slice(0, 3) !== 'ICN' ? this.datamoduleRenderer(this.$props.dataProps) : this.icnRenderer(this.$props.dataProps);
    }

    this.emitter.on('Preview-refresh', async (data) => {
      if (data.sourceType) {
        this.icnRenderer(data);
      } else {
        (data.filename && data.filename.slice(0, 3) !== 'ICN') ? this.datamoduleRenderer(data) : this.icnRenderer(data);          
      }
    });
    
    if(this.$route.params.filename){
      this.filename = this.$route.params.filename;
      let data = {
        filename: this.$route.params.filename
      }
      if(this.$route.params.viewType) {
        data.view = this.$route.params.viewType;
        this.view = this.$route.params.viewType;
      }
      this.$route.params.filename.slice(0, 3) !== 'ICN' ? this.datamoduleRenderer(data) : this.icnRenderer(data);
    }
  }
}
</script>
<template>
  <div class="Preview overflow-auto h-[93%] w-full relative">
    <PreviewRCMenu v-if="!isICN">
      <!-- view IETM or PDF -->
      <div class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div v-if="view !== 'pdf'" href="#" class="text-sm" @click="switchView('pdf')">
          <span href="#" class="material-symbols-outlined bg-transparent text-sm mr-2">book_2</span>
          Switch to PDF
        </div>
        <div v-if="view !== 'html'" href="#" class="text-sm" @click="switchView('html')">
          <span href="#" class="material-symbols-outlined bg-transparent text-sm mr-2">devices</span>
          Switch to IETM</div>        
      </div>

      <!-- Action to be accomplished (delete, issue, commit) -->
      <div class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm" @click="()=>this.emitter.emit('DeleteCSDBObjectFromEveryWhere', {filename: filename})">
          <span href="#" class="material-symbols-outlined bg-transparent text-sm mr-2 text-red-600">delete</span>
          Delete</div>
      </div>
      <div class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">
          <span href="#" class="material-symbols-outlined bg-transparent text-sm mr-2 text-green-600">devices</span>
          Issue</div>
      </div>
      <div class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm" @click="()=>this.emitter.emit('CommitCSDBObjectFromEveryWhere', {filename: filename})">
          <span href="#" class="material-symbols-outlined bg-transparent text-sm mr-2">commit</span>
          Commit</div>
      </div>      
    </PreviewRCMenu>
    <div class="h-[5%] flex mb-3">
      <h1 class="text-blue-500 w-full text-center">Preview</h1>
    </div>
    <div class="flex justify-center w-full px-3 h-[95%]">
      <div v-if="!isICN" id="datamodule-container" class="w-full h-full">
        <iframe id="datamodule-frame" class="w-full h-full" :src="data.src" />
      </div>
      <div v-else id="icn-container">
        <embed v-if="isICN" class="w-full h-full" :src="data.src" :type="data.mime" />
      </div>
    </div>
    <!-- <div v-if="loadingbar" class="top-0 left-0 h-[100vh] w-[100%] z-50 absolute">
      <div style="
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      background:rgba(0,0,0,0.5);
      ">
        <div class="loading_buffer"></div>
      </div>
    </div> -->
    <ContinuousLoadingCircle :show="showLoadingProgress"/>
  </div>
</template>