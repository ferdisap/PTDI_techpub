<script>
import Topbar from '../techpub/components/Topbar.vue';
import Info from '../techpub/components/Info.vue';
import Loading from '../techpub/components/Loading.vue';
import Aside from './components/Aside.vue';
import Main from './components/Main.vue';
import axios from 'axios';
import { useTechpubStore } from '../techpub/techpubStore';


export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      messages: undefined,
      showMessages: true,
    }
  },
  components: { Topbar, Info, Loading, Aside, Main },
  methods: {
    async error(axiosError) {
      window.axiosError = axiosError;
      this.$root.showMessages = true;
      let messages = [];
      if(axiosError.response.data.type == 'application/json'){
        messages = JSON.parse(await axiosError.response.data.text()).messages ?? [JSON.parse(await axiosError.response.data.text()).message];
      }
      else {
        messages = axiosError.response.data.messages;
      }      
      messages.unshift(axiosError.message);
      this.$root.messages = messages;
      this.techpubStore.showLoadingBar = false;
    },
    success(response) {
      this.$root.showMessages = true;
      let messages = response.data.messages;
      this.messages = messages;
      this.techpubStore.showLoadingBar = false;
      this.techpubStore.Errors = [];
    }
  },
  async created() {
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
  <Loading />
  <Topbar />
  <Info :messages="messages" :showMessages="showMessages" />

  <div class="flex mx-auto">
    <Aside />
    <Main />
  </div>
</template>