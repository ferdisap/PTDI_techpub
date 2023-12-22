<script>
import { ref, reactive } from 'vue';
// import axios from 'axios';
import { useIetmStore } from '@/ietm/ietmStore.js';
// import Cookies from 'js-cookie';
import Topbar from './Topbar.vue';

export default {
  data(){
    return {
      token: reactive(undefined),
      ietmStore: useIetmStore(),
    }
  },
  components:{Topbar},
  methods: {
    async getRepo() {
      let tkn = token.value;
      let response = await this.ietmStore.getRepos(tkn);
      if(response.statusText == 'OK'){
        this.ietmStore.tokenRepo = tkn;
        // Cookies.set('tokenRepo', token.value, {path:'/'}); // tidak diperlukan karena sudah dilakukan oleh server. Malah lebih bagus di server (laravel)
        if(this.$route.query.redirect){
          this.$router.push({'path': this.$route.query.redirect});
        }
        else if(this.$route.query.repoName && this.$route.query.filename){
          this.$router.push({ name: 'Detail', params: { repoName: this.$route.query.repoName, filename: this.$route.query.filename} });
        }
        else {
          this.$router.push({name: 'ListRepo'});
        }
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
  <div class="flex justify-center">
    <div class="mt-10 text-center">
      <div class="material-icons" style="font-size:200px">key</div>
      <br/>

      <form action="" method="get" @submit.prevent="getRepo">
        <div class="mb-5">
          <label for="token" class="block mb-2 text-gray-900 dark:text-white text-xl font-bold">TOKEN REPOSITORY</label>
          <br/>
          <input type="token" id="token" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required placeholder="insert your token..">
        </div>
        
        <button type="submit" @click="getRepo" class="shadow-lg text-white font-semibold rounded-full px-5 py-1 bg-violet-500 hover:bg-violet-600 active:bg-violet-700 focus:outline-none focus:ring focus:ring-violet-300 ">GET</button>
      </form>

      <br/>
      <p>Need a help? <a href="">click here..</a></p>
  
    </div>
  </div>
</template>