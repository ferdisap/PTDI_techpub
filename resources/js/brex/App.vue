<script>
import Topbar from '../techpub/components/Topbar.vue';
import Info from '../techpub/components/Info.vue';
import Loading from '../techpub/components/Loading.vue';
import axios from 'axios';
import { RouterView } from 'vue-router';
import { useTechpubStore } from '../techpub/techpubStore';


export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      messages: undefined,
      showMessages: true,
      isSuccess: true,
    }
  },
  components: { Topbar, Info, Loading },
  methods: {
    async error(axiosError) {
      window.axiosError = axiosError;
      this.showMessages = true;
      let messages = [];
      if(axiosError.response.data.type == 'application/json'){
        messages = JSON.parse(await axiosError.response.data.text()).messages ?? [JSON.parse(await axiosError.response.data.text()).message];
      }
      else {
        messages = axiosError.response.data.messages;
      }      
      messages.unshift(axiosError.message);
      this.messages = messages;
      this.isSuccess = false,
      this.techpubStore.showLoadingBar = false;
    },
  },
  created() {
    this.techpubStore.WebRoutes = window.WebRoutes;
    window.WebRoutes = undefined;    
  },
  async mounted(){
    
    // let route = this.techpubStore.getWebRoute('get_brdp_transform',{project_name: this.$route.params.projectName, filename: this.$route.params.filename});
    // let response = axios({
    //   url: route.url
    // });
    // console.log(window.response = response);
  },
}
</script>

<template>
  <Loading />
  <Topbar />
  <Info :messages="messages" :showMessages="showMessages" :isSuccess="isSuccess"/>

  <div class="flex mx-auto">
    <router-view v-slot="{ Component }">
      <keep-alive>
        <component :is="Component"/>
      </keep-alive>
    </router-view>
  </div>
</template>