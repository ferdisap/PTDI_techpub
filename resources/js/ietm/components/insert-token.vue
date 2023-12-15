<script>
import { ref, reactive } from 'vue';
import axios from 'axios';
import { useIetmStore } from '@/ietm/ietmStore.js';
import Cookies from 'js-cookie';
import Topbar from './topbar.vue';

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
      let response = await ietm.getRepos(tkn);
      if(response.statusText == 'OK'){
        this.ietmStore.tokenRepo = tkn;
        // Cookies.set('tokenRepo', token.value, {path:'/'}); // tidak diperlukan karena sudah dilakukan oleh server. Malah lebih bagus di server (laravel)
        if(this.$route.query.redirect){
          this.$router.push({'path': this.$route.query.redirect});
        } else {
          this.$router.push({name: 'ListRepo'});
        }
      }
    }
  },
}
</script>

<template>
  <Topbar/>
  <div class="flex justify-center">
    <div class="mt-10">
      <label for="token" class="text-xl font-bold">Repo Token</label>
      <br/>
      <input type="text" placeholder="token" name="token" id="token" :value="token">
      <button @click="getRepo">GET</button>
  
    </div>
  </div>
</template>