<script>
import Topbar from '../techpub/components/Topbar.vue';
import Info from '../techpub/components/Info.vue';
import Aside from './components/Aside.vue';
import Main from './components/Main.vue';
import { useTechpubStore } from '../techpub/techpubStore';
import alert from '../alert';


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
  components: { Topbar, Info, Aside, Main },
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
    },
  },
  beforeCreate(){
    this.References.defaultStore = useTechpubStore();
  },
}
</script>

<template>
  <Topbar />
  <!-- <Info :messages="messages" :showMessages="showMessages" :isSuccess="isSuccess"/> -->
  <Info :isSuccess="isSuccess" :errors="errors" :message="message"/>

  <div class="flex mx-auto">
    <Aside />
    <Main />
  </div>

  <div v-if="techpubStore.showLoadingBar" class="fixed top-0 left-0 h-[100vh] w-[100%] z-50">
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

  
  <dialog class="fixed top-0 left-0 h-[100vh] w-[100%] z-50 bg-[rgba(255,0,0,00)] font-mono">
    <div style="
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    background:rgba(0,0,0,0.5);
    ">
      <div id="#dialog" class="w-1/2 h-1/2 bg-white opacity-100 absolute top-1/4 left-1/4 rounded-xl border-[15px] border-red-500 border-dashed">
        <h1 class="text-center mt-3 mb-3">ALERT !</h1>
        <hr class="border-2"/>
        <div class="max-h-[65%] overflow-auto px-10" message></div>
        <div class="w-full text-center bottom-3 absolute">
          <button autofocus class="button-danger" alert-not-ok>X</button>
          <button class="button-safe" alert-ok>O</button>
        </div>
      </div>
    </div>
  </dialog>
  
  <!-- <div class="w-1/2 h-1/2 bg-white opacity-100 absolute top-1/4 left-1/4 rounded-xl border-[15px] border-red-500 border-dashed">
    <h1 class="text-center mt-3 mb-3">ALERT !</h1>
    <hr class="border-2"/>
    <div v-html="Alert.message" class="max-h-[65%] overflow-auto px-10"></div>
    <div class="w-full text-center bottom-3 absolute">
      <button class="button-danger" @click="reject()">X</button>
      <button class="button-safe" @click="resolve()">O</button>
      <button class="button-violet" @click="emitter.emit('alert',{message: 'messages tesmessages', fn: fun})">tes emit</button>
    </div>
  </div> -->

  
  <!-- <dialog open class="fixed top-0 left-0 h-[100vh] w-[100%] z-50">
    <div style="
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    background:rgba(0,0,0,0.5);
    ">
      <div id="#dialog" class="w-1/2 h-1/2 bg-white opacity-100 absolute top-1/4 left-1/4 rounded-xl border-[15px] border-red-500 border-dashed">
        <h1 class="text-center mt-3 mb-3">ALERT !</h1>
        <hr class="border-2"/>
        <div class="max-h-[65%] overflow-auto px-10" message>message</div>
        <div class="w-full text-center bottom-3 absolute">
          <button class="button-danger" onclick=run.getresult(0)>X</button>
          <button class="button-safe" onclick="run.getresult(1)">O</button>
        </div>
      </div>
    </div>
  </dialog> -->

</template>