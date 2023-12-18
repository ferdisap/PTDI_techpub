<script>
import { useIetmStore } from '../ietmStore';
// import { ref, reactive } from 'vue';

// import InsertToken from './insert-token.vue';
// import ListRepo  from './list-repo.vue';
import ListObject from './list-object.vue';
import Sidenav from './sidenav.vue';
import Topbar from './Topbar.vue';
import Body from './body.vue';
// import Index from './Index.vue';

export default {
  name: 'Content',
  data(){
    return {
      ietmStore: useIetmStore(),
      transformed_html: '',
      filename: '',
      showSidenav: true,
    }
  },
  methods:{
    async getListObject(){
      let response = await ietm.getObjects(this.$route.params.repoName);
      // alert('getListObject');
      if(response.statusText == 'OK'){
        this.ietmStore.listPMC = [];
        this.ietmStore.listDMC = [];
        let repo = response.data.repos[0];
        for (const object of repo.objects) {
          let prefix = object.filename.split('-')[0];
          if(prefix == 'PMC'){
            this.ietmStore.listPMC.push(object);
          }
          else if(prefix == 'DMC'){
            this.ietmStore.listDMC.push(object);
          }
        }
      }
    },
  },
  components: {Topbar, ListObject, Sidenav, Body},
  async beforeMount(){
    if(!this.$route.params.repoName){
      return this.$router.push({name:'ListRepo'});
    }
    if(!this.$route.params.filename || this.ietmStore.listDMC.length < 1 || this.ietmStore.listPMC.length < 1){
      this.getListObject();
    }
    if(this.$route.params.filename){
      let response = await ietm.getDetailObject(this.$route.params.repoName, this.$route.params.filename);
      this.data = response.data; 
      this.transformed_html = response.data.repos[0].objects[0].transformed_html;
    }
  },
  mounted(){
    this.filename = this.$route.params.filename;
  },
  
  async beforeUpdate(){
    if(this.$route.params.filename != this.filename){
      this.filename = this.$route.params.filename;
      let response = await ietm.getDetailObject(this.$route.params.repoName, this.$route.params.filename);
      if(response.statusText == 'OK'){
        this.ietmStore.detailObject = response.data;
        this.transformed_html = this.ietmStore.detailObject.repos[0].objects[0].transformed_html;
      }
      else{
        this.$root.messages = response.data.messages;
        this.$root.showMessages = true;
      }

    }
  },
}
</script>


<template>
  <div class="mx-auto text-center flex">
    <div :class="[showSidenav ? 'w-1/5' : '']">
      <div class="border sticky top-0">
        <div class="text-2xl font-bold bg-slate-900 text-white py-3">
          <span class="" v-show="showSidenav">Left Side Panel</span>
          <button class="float-right material-icons pb-3 bg-slate-900 text-white" @click="showSidenav = !showSidenav">{{ showSidenav ? 'chevron_left' : 'chevron_right'}}</button>
        </div>
        <Sidenav v-show="showSidenav"/>
      </div>
    </div>
    <div class="block w-full relative text-left">
      <Body :filename="$route.params.filename" :transformed_html="transformed_html"/>
    </div>
  </div>
</template>