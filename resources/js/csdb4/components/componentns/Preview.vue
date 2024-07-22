<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
import { contentType } from 'es-mime-types';
import path from 'path';
import ContinuousLoadingCircle from '../../loadingProgress/ContinuousLoadingCircle.vue';
export default {
  data(){
    return {
      techpubStore: useTechpubStore(),
      pathHelper: path,
      showLoadingProgress: false,
      inIframe: undefined,
      mime: undefined, // ini bisa PDF, HTML, IMG, VIDEO, mungkin FLASH, etc
      src: undefined
    }
  },
  components:{ContinuousLoadingCircle},
  methods:{
    async render(filename, viewType){
      URL.revokeObjectURL(this.src);
      this.mime, this.src = undefined;
      let routename;
      let extension = filename.substring(filename.length,filename.length-4);
      let doctype = filename.substring(0,3);

      // eg. DMC viewtype == 'html' (ietm);
      if(extension === '.xml' && viewType === 'html') {
        routename = 'api.read_html_object';
        this.mime = 'text/html';
        this.inIframe = true;
      }
      // eg. DMC viewType == 'pdf'
      else if(extension === '.xml' && viewType === 'pdf') {
        routename = 'api.read_pdf_object';
        this.mime = 'application/pdf';
        this.inIframe = true;
      }
      // eg. ICN
      else if(doctype === 'ICN' && (viewType === 'html' || viewType === 'other')) {
        routename = 'api.get_icn_raw';
        let path = this.pathHelper.extname(filename);
        this.mime = contentType(path);
        this.inIframe = false;
      }
      // eg. externalpubRef pdf
      else if(doctype != 'ICN' && extension != '.xml' && viewType === 'pdf') {
        routename = 'api.read_pdf_object';
        this.mime = 'application/pdf';
        this.inIframe = true;
      }
      // eg. external pubRef non pdf
      else if(doctype != 'ICN' && extension != '.xml' && viewType != 'pdf') {
        routename = 'api.read_other_object';
        let path = this.pathHelper.extname(filename);
        this.mime = contentType(path);
        this.inIframe = false;
      }
      // eg. ICN tapi viewType === 'pdf'
      else return Promise.reject(false);

      let route = this.techpubStore.getWebRoute(routename, {filename: filename});
      // ini untuk embed
      if(!this.inIframe) this.src = route.url.toString();
      // ini untuk iframe HTML dan PDF
      else {
        this.showLoadingProgress = true;
        this.src = await this.blobRequestTransformed(routename, { filename: filename }, this.mime)
        this.showLoadingProgress = false;
      };
      
      return Promise.reject(true);
    },
    renderFromBlob(src, mime){
      setTimeout(()=>{
        this.src = src;
        this.mime = mime;
      },0);
    },
    /**
     * aat ini belum dipakai karena halaman untuk ietm(html) belum difungsikan
     */
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
    /**
     * nanti ini diganti oleh worker
     * kalo worker cuma untuk fetch saja, tidak perlu pakai worker, kecuali ada proses pengolahan data response nya. Tapi karna blobURL yang dibuat di worker tidak bisa ditampilkan di window maka worker tidak perlu
    */
    async blobRequestTransformed(routename, data, mime) {
      data = Object.assign(data);
      delete (data.update_at);
      let responseType = !mime.includes('text') ? 'arraybuffer' : 'json';
      // masukkan cache If-None-Match jika perlu, di server sudah siap
      let response = await axios({
        route: {
          name: routename,
          data: data,
        },
        useMainLoadingBar: false,
        responseType: responseType,
      });
      if (response.statusText === 'OK') {
        let blob = new Blob([response.data], { type: mime });
        let url = URL.createObjectURL(blob);
        return url;
      } else {
        return false
      }
    },
  },
  mounted(){
    this.render(this.$route.params.filename, this.$route.params.viewType);
    this.emitter.on('Preview-refresh', async (data) => {
      if (data.sourceType === 'blobURL') {
        this.renderFromBlob(data.src, data.mime)
      } else {
        this.render(data.filename, data.viewType ? data.viewType : this.$route.params.viewType);
      }
    });
  }
}
</script>
<template>
  <div class="Preview overflow-auto h-[93%] w-full relative">
    <div class="h-[5%] flex mb-3">
      <h1 class="text-blue-500 w-full text-center">Preview</h1>
    </div>
    <div class="flex justify-center w-full px-3 h-[95%]">
      <!-- untuk HTML dan PDF-->
      <div v-if="inIframe" id="datamodule-container" class="w-full h-full">
        <iframe id="datamodule-frame" class="w-full h-full" :src="src" />
      </div>
      <!-- untuk non HTML dan non PDF-->
      <div v-else id="icn-container">
        <embed class="w-full h-full" :src="src" :type="mime" />
      </div>
    </div>
    <PreviewRCMenu>
      <!-- view IETM or PDF -->
      <div v-if="inIframe" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div v-if="$route.params.viewType === 'html'" href="#" class="text-sm" @click="switchView('pdf')">
          <span href="#" class="material-symbols-outlined bg-transparent text-sm mr-2">book_2</span>
          Switch to PDF
        </div>
        <div v-else-if="$route.params.viewType === 'pdf'" href="#" class="text-sm" @click="switchView('html')">
          <span href="#" class="material-symbols-outlined bg-transparent text-sm mr-2">devices</span>
          Switch to HTML</div>        
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
    <ContinuousLoadingCircle :show="showLoadingProgress"/>
  </div>
</template>