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
      isSuccess: true,
    }
  },
  components: { Topbar, Info, Loading, Aside, Main },
  methods: {
    async error(axiosError) {
      window.axiosError = axiosError;
      this.showMessages = true;
      let messages = [];
      try {
        if(axiosError.response && axiosError.response.data.type == 'application/json'){
          messages = JSON.parse(await axiosError.response.data.text()).messages ?? [JSON.parse(await axiosError.response.data.text()).message];
        }
        else {
          messages = axiosError.response.data.messages;
        }
      } catch (error) {}
      this.messages = messages;
      console.log(this.messages, axiosError);
      this.isSuccess = false,
      this.techpubStore.showLoadingBar = false;
    },
    success(response) {
      window.successResponse = response;
      console.log('aa');
      this.showMessages = true;
      let messages = response.data.messages;
      console.log(messages);
      this.messages = messages;
      this.isSuccess = true,
      this.techpubStore.showLoadingBar = false;
      this.techpubStore.Errors = [];
    }
  },
}
</script>

<template>
  <Loading />
  <Topbar />
  <Info :messages="messages" :showMessages="showMessages" :isSuccess="isSuccess"/>

  <div class="flex mx-auto">
    <Aside/>
    <Main/>
  </div>
</template>