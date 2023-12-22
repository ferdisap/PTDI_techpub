<script>
export default {
  data(){
    return {
      foo: 'bar'
    }
  },
}
</script>
<template>
  <main class="w-full">
    <section class="m-3 rounded-xl border-slate-300 bg-slate-50 text-black text-left py-3 shadow-xl">
      <router-view v-slot="{ Component }">
        <keep-alive>
          <component :is="Component"/>
        </keep-alive>
      </router-view>
    </section>
  </main>
</template>