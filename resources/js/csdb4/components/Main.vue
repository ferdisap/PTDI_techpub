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
  <!-- <main class="w-full">
    MAIN
    <section class="flex w-full p-3 m-3 rounded-xl border-slate-300 bg-slate-50 text-black text-left py-3 shadow-xl">
      <div :class="[showRightAside ? 'w-1/2' : 'w-full']">
        <router-view v-slot="{ Component }">
          <keep-alive>
            <component :is="Component" />
          </keep-alive>
        </router-view>
      </div>
      <div :class="[ showRightAside ? 'w-1/2' : 'w-1/12','relative']">
        <ButtonMinimizeContainer variable="showRightAside" direction="right" class="top-5 text-white left-0"/>
        <DetailAll />
      </div>
    </section>
  </main> -->
</template>