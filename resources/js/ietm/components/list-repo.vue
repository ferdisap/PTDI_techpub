<script>
// import axios from 'axios';
import { useIetmStore } from '../ietmStore';
// import {ref} from 'vue';
import Cookies from 'js-cookie';

export default {
  name: 'ListRepo',
  data() {
    return {
      ietmStore: useIetmStore(),
      data: null,
    }
  },
  async beforeMount(){
    if(!this.data && !this.ietmStore.response){
      let response = await ietm.getRepos(Cookies.get('tokenRepo'));
      this.data = response.data;
    }
    else if(!this.data ){
      this.data = this.ietmStore.response.data;
    }
  },
  updated(){
    // console.log('updated');
  }
}
</script>

<template>
  <div v-if="data" class="mt-10 flex justify-center">
    <div class="m-8 text-center">
      <div class="material-icons" style="font-size:200px">library_books</div>
      <h1 class="block mb-2 text-gray-900 dark:text-white text-xl font-bold">REPO LIST</h1>
      <br/>
      <table class="border-separate border-spacing-x-5">
        <tr>
          <th class="text-lg px-3">Name</th>
          <th class="text-lg px-3">Date Create</th>
        </tr>
        <tr v-for="repo in data.repos">
          <td>
            <router-link :to="{name:'ListObject', params:{repoName: repo.name},}"> {{ repo.name }} </router-link>
          </td>
          <td>
            {{ (new Date(repo.created_at)).toLocaleDateString('en-EN', {
              year: 'numeric', month: 'short', day: 'numeric'
            })
            }}
          </td>
        </tr>
      </table>
    </div>
  </div>
</template>