<script>
import Topbar from '../techpub/components/Topbar.vue';
import Info from '../techpub/components/Info.vue';
import Loading from '../techpub/components/Loading.vue';
import Aside from './components/Aside.vue';
import Main from './components/Main.vue';
import axios from 'axios';
import { useTechpubStore } from '../techpub/techpubStore';


export default {
  data(){
    return {
      techpubStore: useTechpubStore(),
      messages: undefined,
      showMessages: true,
    }
  },
  components: {Topbar, Info, Loading, Aside, Main},
  async created(){
    this.techpubStore.WebRoutes = window.WebRoutes;
    window.WebRoutes = undefined;
    await axios.get('/auth/check')
      .then(response => {
        this.techpubStore.Auth.name = response.data.name;
        this.techpubStore.Auth.email = response.data.email;
      })
      .catch(response => {
        window.location.href = "/login";
      });
  },
}
</script>

<template>
  <Loading/>
  <Topbar/>
  <Info :messages="messages" :showMessages="showMessages"/>

  <div class="flex mx-auto">
    <Aside/>
    <Main/>
  </div>
</template>