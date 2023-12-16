<script>
import { useIetmStore } from '../ietmStore';
import { reactive } from 'vue';

import InsertToken from './insert-token.vue';
import ListRepo  from './list-repo.vue';
import ListObject from './list-object.vue';
import Sidenav from './sidenav.vue';
import Topbar from './topbar.vue';
import Body from './body.vue';

export default {
  name: 'Content',
  data(){
    return {
      ietmStore: useIetmStore(),
      transformed_html: '',
      filename: '',
      showListObject: false,
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
  async updated(){
    if(this.$route.params.filename != this.filename){
      this.filename = this.$route.params.filename;
      let response = await ietm.getDetailObject(this.$route.params.repoName, this.$route.params.filename);
      if(response.statusText == 'OK'){
        this.ietmStore.detailObject = response.data;
        this.transformed_html = this.ietmStore.detailObject.repos[0].objects[0].transformed_html;
      }
    }
  },
}
</script>


<template>
  <Topbar/>
  <div class="container mx-auto text-center">

    <div class="flex">
      <div class="w-1/4">
        <Sidenav :filename="filename"/>
      </div>
      <div class="w-3/4 block">
        <button class="sticky top-20 text-underline z-20 shadow-md bg-blue-600 rounded-full p-2 text-white text-underline float-right" @click="showListObject = !showListObject">List Object</button>
        <div v-show="showListObject" class="sticky top-10 z-10 bg-white border-4 rounded-lg p-4">
          <ListObject :repoName="$route.params.repoName"/>
        </div>
        <Body :filename="$route.params.filename" :transformed_html="transformed_html"/>
      </div>
    </div>
  </div>
</template>