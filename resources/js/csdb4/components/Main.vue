<script>
// import ButtonMinimizeContainer from './subComponents/ButtonMinimizeContainer.vue';
// import DetailAll from './subComponents/DetailAll.vue';

export default {
  data() {
    return {
      showRightAside: false
    };
  },
  components: { 
    // DetailAll, 
    // ButtonMinimizeContainer 
  },
  mounted() {
    this.emitter.on('openfile', () =>  this.showRightAside = true)
  },
}
</script>
<template>
  <div class="pt-5 pl-2 h-full w-full overflow-auto">
    <router-view v-slot="{ Component }">
      <keep-alive>
        <component :is="Component" />
      </keep-alive>
    </router-view>
  </div>
</template>