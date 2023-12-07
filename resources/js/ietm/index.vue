<script>
import Topbar from './components/topbar.vue';
import Content from './components/content.vue';
import SideNav from './components/sideNav.vue';
export default {
  data(){
    return {
      currentPath: window.location.hash,
    }
  },
  components: {
    Topbar, Content, SideNav
  }
}
</script>

<template>
  <Topbar/>
  <SideNav/>
  <Content/>
</template>