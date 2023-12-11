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
    }
  },
  components: {
    Topbar, Content, SideNav, InsertToken, ListRepo, 
  },
  props: ['repos'],
  mounted(){
    if(!Cookies.get('tokenRepo')){
      let redirect = window.location.pathname;
      this.$router.push({name:'InsertToken',query:{redirect: redirect}});
    }
  },
}
</script>

<template>
  <router-view v-slot="{ Component }">
    <keep-alive>
      <component :is="Component"/>
    </keep-alive>
  </router-view>
</template>