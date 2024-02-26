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
          name: 'api.get_transformed_identstatus',
          // data: {filename: this.$props.dataProps.filename}
          data: {filename: 'DMC-MALE-A-00-00-00-00A-001A-A_000-02_EN-EN.xml'}
        }
      })
      this.storingResponse(response);
      // return this.$props.dataProps.filename;
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
}
</script>
<template>
  <div v-show="false">{{ requestTransformed }}</div>
  <div class="IdentStatus overflow-auto h-[93%] w-full">
    <div class="h-[5%] flex mb-3">
      <h1 class="text-blue-500 w-full text-center">IdentStatus</h1>
    </div>
    <div class="">
      <component v-if="data.transformed" :is="transformed"/>
    </div>
  </div>
</template>