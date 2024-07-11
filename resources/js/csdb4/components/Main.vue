<script>
import {useTechpubStore} from '../../techpub/techpubStore';
import ManagementData from './route/ManagementData.vue';
import Explorer from './route/ManagementData.vue';


export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      showRightAside: false
    };
  },
  components: {ManagementData,Explorer},
  methods:{
    // available action towards CSDB
    /**
     * @param {*} data; if data.filenames is array then it'll be joined, otherwise data.filename is choosen 
     */
     joinFilename(data){
      let filenames = data.filenames || data.filename;
      if (Array.isArray(filenames)) filenames = filenames.join(', ');
      return filenames;
    },
    async deleteCSDBs(data){
      if(!data) return;
      let filenames = this.joinFilename(data);
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
    },
    async commitCSDBs(data){
      if(!data) return;
      let filenames = this.joinFilename(data);
      if (!(await this.$root.alert({ name: 'beforeCommitCsdbObject', filename: filenames }))) {
        return;
      }
      let response = await axios({
        route: {
          name: 'api.commit_objects',
          data: {filenames: filenames},
        }
      });
      this.emitter.emit('CommitMultipleCSDBObject', response.data.models);
    }
  },
  mounted() {
    this.emitter.on('openfile', () =>  this.showRightAside = true)
    
    this.emitter.on('DeleteCSDBObjectFromEveryWhere', (data) => {
      this.deleteCSDBs(data)
    })
    this.emitter.on('CommitCSDBObjectFromEveryWhere', (data) => {
      this.commitCSDBs(data)
    })
    
    setTimeout(() => {
      if(this.$route.params.filename){
        const worker = this.createWorker('WorkerGetCsdbModel.js');
        if(worker){
          let route = this.techpubStore.getWebRoute('api.get_object_model', {filename: this.$route.params.filename});
          worker.onmessage = (e) => {
            this.techpubStore.currentObjectModel = e.data.model;
            worker.terminate();
          }
          worker.postMessage({route: route});
        }
      }
    }, 10);

    window.rt = this.$route;
    window.rtr = this.$router;

  },
}
</script>
<template>
  <div class="pt-5 pl-2 h-full w-full overflow-auto">
    <router-view v-slot="{ Component }">
      <keep-alive exclude="Explorer,ManagementData">
        <component :is="Component" />
      </keep-alive>
    </router-view>
  </div>
</template>