<script>
import axios from 'axios';
import { useIetmStore } from '../ietmStore';
import {ref} from 'vue';
import Cookies from 'js-cookie';

export default {
  name: 'ListRepo',
  data() {
    return {
      ietmStore: useIetmStore(),
      data: null,
    }
  },
  methods: {
    async getObjects(repoName){
      // let response = await ietm.getObjects(repoName);
      // if(response.statusText == 'OK'){
      //   useIetmStore().setResponse(response);
      //   this.$router.push({name:'ListObject', params:{repoName: repoName}});
      // }
      this.$router.push({name:'ListObject', params:{repoName: repoName}});
    }
  },
  async beforeMount(){
    console.log('beforeMount listRepo', this.data);
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
  <div>list repo</div>
  <div v-if="data" class="mt-10 flex justify-center">
    <div class="m-8">
      <h1 class="text-xl font-bold mb-3">REPO LIST</h1>
      <table>
        <tr>
          <th>Name</th>
          <th>Date Create</th>
        </tr>
        <tr v-for="repo in data.repos">
          <td>
            <a v-on:click="getObjects(repo.name)" href="javascript:void(0)">{{ repo.name }}</a>
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
  <!-- <div> {{ repos }} </div>
  <div v-if="repos.length > 0" class="mt-10 flex justify-center">
    <div class="m-8">
      <h1 class="text-xl font-bold mb-3">REPO LIST</h1>
      <table>
        <tr>
          <th>Name</th>
          <th>Date Create</th>
        </tr>
        <tr v-for="repo in repos">
          <td>
            <a v-on:click="getObjects(repo.name)" href="javascript:void(0)">{{ repo.name }}</a>
          </td>
          <td>
            {{ (new Date(repo.created_at)).toLocaleDateString('en-EN', {
              year: 'numeric', month: 'short', day: 'numeric'
            })
            }}
          </td>
        </tr>
      </table>
    </div> -->
  </div>

  <!-- <p v-if="responseStore.response">{{ responseStore.response.data['repos'] }}</p> -->
  <!-- <p v-if="tokenStore.tokenRepo">{{ tokenStore.tokenRepo }}</p> -->
</template>