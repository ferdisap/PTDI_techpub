<script>
export default {
  data(){
    return {
      data: {},
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
      if(!this.$props.dataProps.filename){
        return '';
      }
      let response = await axios({
        route: {
          name: 'api.get_transformed_contentpreview',
          data: {filename: this.$props.dataProps.filename}
        }
      })
      this.storingResponse(response);
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
    }
  },
  mounted(){
    this.emitter.on('Preview-refresh', async (data) => {
      let response = await axios({
        route: {
          name: 'api.get_transformed_contentpreview',
          // data: {filename: this.$props.dataProps.filename}
          data: {filename: data.filename}
        }
      });
      this.storingResponse(response);
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
      <component v-if="data.transformed" :is="transformed"/>
    </div>
  </div>
</template>