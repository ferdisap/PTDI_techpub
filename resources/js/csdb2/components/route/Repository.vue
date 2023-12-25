<script>
import axios from 'axios';
import { useTechpubStore } from '../../../techpub/techpubStore';
import ObjectUpdate from './ObjectUpdate.vue';

export default {
  data(){
    return {
      techpubStore: useTechpubStore(),
      showIndex: true,
      Repos: [],
    }
  },
  methods: {
    decode_base64(str){
      return atob(str);
    }
  },
  async mounted(){
    let route = this.techpubStore.getWebRoute('api.get_repo_index');
    axios.get(route.path)
    .then(response => this.Repos = response.data)
    .catch(error => this.$root.error(error))
  },
  components: {ObjectUpdate},
}
</script>
<template>
  <h1 class="text-center">Repository</h1>
  <div class="w-full text-center mb-3">
    <button :class="[showIndex ? 'border-b-black border-b-4' : '' ,'button-nav']" @click="showIndex = !showIndex">Index</button>
  </div>


  <div class="w-full flex">
    <!-- Index -->
    <div class="w-full mr-3" v-show="showIndex">
      <h1>All Objects</h1>
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Project</th>
            <th>Token</th>
            <th>Date Created</th>
            <th class="text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="rp in Repos" class="hover:bg-blue-400">
            <td> <a :href="'/ietm/content/' + rp.name " class="hover:underline" target="__blank"> {{ rp.name }} </a> </td>
            <td> {{ rp.project_name }} </td>
            <td> {{ decode_base64(rp.token) }} </td>
            <td> {{ techpubStore.date(rp.created_at) }} </td>
            <td class="text-center"> <button class="button-danger">Delete</button> </td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>
</template>