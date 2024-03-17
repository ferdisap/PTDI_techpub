<script>
export default {
  data(){
    return {
      data: {},
      isICN: false,
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
          this.isICN = false;
          let response = await axios({
            route: {
              name: 'api.get_transformed_contentpreview',
              data: {filename: this.$props.dataProps.filename}
            }
          })
          this.storingResponse(response);
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
    }
  },
  mounted(){
    this.emitter.on('Preview-refresh', async (data) => {
      if(data.filename && data.filename.slice(0,3) !== 'ICN'){
        let response = await axios({
          route: {
            name: 'api.get_transformed_contentpreview',
            data: {filename: data.filename}
          }
        });
        this.storingResponse(response);
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
    <div class="flex justify-center w-[95%]">
      <component v-if="data.transformed && !isICN" :is="transformed"/>
      <div id="icn-container">

      </div>
    </div>
  </div>
</template>