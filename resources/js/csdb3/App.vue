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
      isSuccess: true,
      
      showMessages: false,
      errors: undefined, // {}
      message: undefined, // ""
    }
  },
  components: { Topbar, Info, Loading, Aside, Main },
  methods: {
    // ini sesuai ret2(). Bedanya, yang fungsi ini ada pesan axiosError.message dan errors untuk input name
    async error(axiosError) {
      console.log(axiosError);
      axiosError.response.data.errors ? (this.errors = axiosError.response.data.errors) : (this.errors = undefined);
      axiosError.response.data.message ? (this.message = axiosError.message + ': ' + axiosError.response.data.message) : (this.message = axiosError.message);
      this.isSuccess = false;
      this.techpubStore.showLoadingBar = false;
      this.showMessages = true;
    },
    // ini sesuai ret2(). 
    success(response, isSuccess = true) {
      window.rsp = response;
      this.errors = undefined;
      this.isSuccess = true;
      this.message = response.data.message ? response.data.message : '';
      this.techpubStore.showLoadingBar = false;
      this.showMessages = true;
    }
  },
  beforeCreate(){
    window.techpubStore = this.techpubStore
    this.References.defaultStore = useTechpubStore();
  },
  // async created(){
  //   await axios.get('/auth/check')
  //     .then(response => {
  //       this.techpubStore.Auth.name = response.data.name;
  //       this.techpubStore.Auth.email = response.data.email;
  //     })
  //     .catch(response => {
  //       window.location.href = "/login";
  //     });
  // },
  mounted(){
    window.route = this.$route;
    window.router = this.$router;
    window.techpubStore = this.techpubStore;
  }
}
</script>

<template>
  <Loading />
  <Topbar />
  <!-- <Info :messages="messages" :showMessages="showMessages" :isSuccess="isSuccess"/> -->
  <Info :isSuccess="isSuccess" :errors="errors" :message="message"/>

  <div class="flex mx-auto">
    <Aside />
    <Main />
  </div>

  <div v-if="techpubStore.showLoadingBar" class="absolute top-0 left-0 h-[100vh] w-[100%] bg-gray-700 z-50 opacity-50">
    <div style="
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    background:rgba(0,0,0,0.5);
    ">
      <div class="loading_buffer"></div>
    </div>
  </div>
  

</template>