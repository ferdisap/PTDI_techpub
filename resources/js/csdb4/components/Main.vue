<script>
// import ButtonMinimizeContainer from './subComponents/ButtonMinimizeContainer.vue';
// import DetailAll from './subComponents/DetailAll.vue';

export default {
  data() {
    return {
      showRightAside: false
    };
  },
  components: { 
    // DetailAll, 
    // ButtonMinimizeContainer 
  },
  methods:{
    // available action towards CSDB    
    /**
     * 
     * @param {*} data; if data.filenames is array then it'll be joined, otherwise data.filename is choosen 
     */
     async deleteCSDBs(data){
      if(!data) return;
      let filenames = data.filenames || data.filename;
      if (Array.isArray(filenames)) filenames = filenames.join(', ');
      if (!(await this.$root.alert({ name: 'beforeDeleteCsdbObject', filename: filenames }))) {
        return;
      }
      let response = await axios({
        route: {
          name: 'api.delete_objects',
          data: {filenames: filenames},
        }
      });
      this.emitter.emit('DeleteMultipleCSDBObject', response.data.models);

    }
  },
  mounted() {
    this.emitter.on('openfile', () =>  this.showRightAside = true)
    
    this.emitter.on('DeleteCSDBObjectFromEveryWhere', (data) => {
      this.deleteCSDBs(data)
    })
    
    window.rt = this.$route;
    window.rtr = this.$router;

  },
}
</script>
<template>
  <div class="pt-5 pl-2 h-full w-full overflow-auto">
    <router-view v-slot="{ Component }">
      <keep-alive>
        <component :is="Component" />
      </keep-alive>
    </router-view>
  </div>
</template>