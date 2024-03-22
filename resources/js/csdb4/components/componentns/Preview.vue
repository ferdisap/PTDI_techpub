<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
import path from 'path';
import { contentType } from 'es-mime-types';
export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      view: 'html',
      isICN: false,
      data: {},
      path: path,
      filename: '',
    }
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
        const route = this.techpubStore.getWebRoute('api.request_icn_object', { filename: data.filename });
        setTimeout(() => {
          let path = this.path.extname(data.filename);
          this.data.mime = contentType(path);
          this.data.src = route.url.toString();
        }, 100);
      }
    },
    async datamoduleRenderer(data = {}) {
      this.isICN = false;
      this.filename = data.filename;
      let routeName = this.view === 'html' ? 'api.get_transformed_contentpreview' : (this.view === 'pdf' ? 'api.get_pdf_object' : '');
      if (routeName) {
        this.data.mime = this.view === 'html' ? 'text/html' : (this.view === 'pdf' ? 'application/pdf' : '');
        let src = await this.blobRequestTransformed(routeName, { filename: data.filename }, this.data.mime);
        this.data.src = src // blob:http://127.0.0.1:8000/1a7cdf64-c7f7-4dd3-b4b2-0d26a3f0bb52
      }
    },
    switchView(name){
      this.view = name;
      this.datamoduleRenderer({filename: this.filename});
    }
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
  }
}
</script>
<template>
  <div class="Preview overflow-auto h-[93%] w-full">
    <div class="h-[5%] flex mb-3">
      <h1 class="text-blue-500 w-full text-center">Preview</h1>
    </div>
    <div class="flex justify-center w-full px-3 h-[95%]">
      <div v-if="!isICN" id="datamodule-container" class="w-full h-full">
        <a v-if="view !== 'pdf'" href="#" class="text-sm" @click="switchView('pdf')">Switch to PDF</a>
        <a v-if="view !== 'html'" href="#" class="text-sm" @click="switchView('html')">Switch to IETM</a>
        <iframe id="datamodule-frame" class="w-full h-full" :src="data.src" />
      </div>
      <div v-else id="icn-container">
        <embed v-if="isICN" class="w-full h-full" :src="data.src" :type="data.mime" />
      </div>
    </div>
  </div>
</template>