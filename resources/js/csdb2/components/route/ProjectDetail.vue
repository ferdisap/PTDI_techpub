<script>
import { useTechpubStore } from '../../../techpub/techpubStore';

export default {
  data(){
    return {
      techpubStore: useTechpubStore(),
      showListObject: true
    }
  },
  methods: {
    // getListObject
  },
  async mounted() {
    this.techpubStore.setProject();
    console.log(this.techpubStore.Project);
    let repoName = this.$route.params.projectName;
    console.log(repoName);
    let route = this.techpubStore.getWebRoute('api.get_csdb_object_all',{projectName: repoName});
    let response = await axios.get(route.url.toString())
    if(response.statusText === 'OK'){
      // this.techpubStore.Project.find(v => v.name = repoName).objects = response.data;
      // console.log(this.techpubStore.Project);
      this.techpubStore.setObjects(repoName, response.data);
    }
  },
}
</script>
<template>
  <div class="w-full mt-3">
    <h1 class="text-center">Detail of {{ $route.params.projectName }}</h1>
    <div class="w-full text-center mb-3">
      <button :class="[showListObject ? 'border-b-black border-b-4' : '' ,'button-nav']" @click="showListObject = !showListObject">List Object</button>
    </div>
    
    <!-- List Object -->
    <div v-show="showListObject">
      <table>
        <thead>
          <tr>
            <th>Filename</th>
            <th>Title</th>
            <th>Description</th>
            <th>Status</th>
            <th>Last Modified</th>
            <th>Initiator</th>
            <th class="text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr class="hover:bg-blue-400" v-if="techpubStore.project($route.params.projectName)" v-for="obj in techpubStore.project($route.params.projectName).objects">
            <td> <router-link :to="{name:'ObjectDetail', params:{projectName: $route.params.projectName, filename: obj.filename},}">{{ obj.filename }}</router-link> </td>
            <td> not prepared yet </td>
            <td> {{ obj.description }} </td>
            <td> {{ obj.status }} </td>
            <td> {{ this.techpubStore.date(obj.updated_at) }} </td>
            <td> {{ obj.initiator.name }} </td>
            <td class="text-center"> 
              <router-link class="button" :to="{name:'ObjectUpdate', params:{projectName: $route.params.projectName, filename: obj.filename},}">update</router-link>
              | 
              <button class="button-danger">delete</button> </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>