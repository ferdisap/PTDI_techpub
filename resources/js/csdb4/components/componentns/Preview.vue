<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
export default {
  data(){
    return {
      data: {},
      isICN: false,
      techpubStore: useTechpubStore(),
    }
  },
  props:{
    dataProps: {
      type: Object,
      required: true
    }
  },
  computed:{
    async requestTransformed(){
      if(this.$props.dataProps.filename){
        if(this.$props.dataProps.filename.slice(0,3) !== 'ICN'){
          // this.isICN = false;
          // let response = await axios({
          //   route: {
          //     name: 'api.get_transformed_contentpreview',
          //     data: {filename: this.$props.dataProps.filename}
          //   }
          // })
          // this.storingResponse(response);
          this.datamoduleRenderer(this.$props.dataProps);
        }
        else {
          this.isICN = true;
        }
      }
    },
    transformed(){
      return {
        template: this.data.transformed,
      }
    },
  },
  methods:{
    storingResponse(response){
      if(response.statusText === 'OK'){
        this.data.transformed = response.data.transformed;
      }
    },
    /*
     * data bisa berisi tentang mime, source, sourceType, filename
     * jika ada filename, maka akan request ke server
    */
    icnRenderer(data = {}){
      if(data.mime.includes('image')){
        if(data.sourceType === 'url'){
          let html = `<img src="${data.source}"/>`
          $('#icn-container').html(html);
        }
      }
    },
    datamoduleRenderer(data){
      console.log(data);
      const route = this.techpubStore.getWebRoute('api.get_transformed_contentpreview', data);
      console.log(window.route);
      setTimeout(() => {
        let iframe = document.querySelector('#datamodule-frame');
        console.log(iframe);
        iframe.src = route.url;
      },0)

      // let blob = new Blob([this.data.transformed], {type: 'text/html'});
      // let blobURL = URL.createObjectURL(blob)
      // let iframe = document.querySelector('#datamodule-container').firstElementChild;
      // URL.revokeObjectURL(iframe.src)
      // iframe.src = blobURL;
    }
  },
  mounted(){
    window.Preview = this;
    this.emitter.on('Preview-refresh', async (data) => {
      // console.log('aaa',data);
      if(data.filename && data.filename.slice(0,3) !== 'ICN'){
        // let response = await axios({
        //   route: {
        //     name: 'api.get_transformed_contentpreview',
        //     data: {filename: data.filename}
        //   }
        // });
        // this.storingResponse(response);
        this.datamoduleRenderer(data);
      }
      else if(data.source){
        this.isICN = true;
        this.icnRenderer(data);
      }
    });
  }
}
</script>
<template>
  <div v-show="false">{{ requestTransformed }}</div>
  <div class="Preview overflow-auto h-[93%] w-full">
    <div class="h-[5%] flex mb-3">
      <h1 class="text-blue-500 w-full text-center">Preview</h1>
    </div>
    <div class="flex justify-center w-full px-3 h-[95%]">
      <div id="datamodule-container" class="w-full h-full">
        <iframe id="datamodule-frame" class="w-full h-full"/>
      </div>
      <!-- <component v-if="data.transformed && !isICN" :is="transformed"/> -->
      <!-- <div id="icn-container">

      </div> -->
    </div>
  </div>
</template>