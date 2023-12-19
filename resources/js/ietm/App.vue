<script>
import Topbar from './components/topbar.vue';
import Content from './components/content.vue';
import SideNav from './components/sideNav.vue';
import InsertToken from './components/insert-token.vue';
import ListRepo from './components/list-repo.vue';
import ListObject from './components/list-object.vue';
import { useIetmStore } from './ietmStore.js';
import Cookies from 'js-cookie';

export default {
  data(){
    return {
      ietmStore: useIetmStore(),
      messages: undefined,
      showMessages: false,
    }
  },
  props: ['repos'],
  components:{Topbar},
  mounted(){
    window.router = this.$router;
    if(!Cookies.get('tokenRepo')){
      let redirect = window.location.pathname;
      let exclude = ['foo', 'bar'];
      exclude.push(this.$router.getRoutes().filter((v) => v.name == 'InsertToken')[0].path);
      
      if(!exclude.includes(redirect)){
        this.$router.push({name:'InsertToken',query:{redirect: redirect}});
      }
    }
  },
}
</script>

<template>
  <div class="h-1 sticky top-0" style="z-index: 100;">
    <div class="wrapper">
      <div class="loadingBar"></div>
    </div>
  </div>
  <Topbar/>
  <div class="flex justify-center absolute h-2/3 z-50 w-1/2 left-1/4 top-1/4 shadow-2xl" v-if="messages" v-show="showMessages">
    <div class="bg-cyan-300 px-5 shadow-sm rounded-lg block text-left w-full">
      <div class="text-center text-xl p-3 font-bold">Message:
        <button class="float-right" @click="showMessages = false">X</button>
      </div>
      <div v-for="message in messages">
        <div v-html="message" class="mb-1" @click="showMessages = false"></div>
      </div>
    </div>
  </div>
  <div class="h-3"></div>
  <router-view v-slot="{ Component }">
    <keep-alive>
      <component :is="Component"/>
    </keep-alive>
  </router-view>
</template>