<script>
import { useIetmStore } from '../ietmStore';
// import Topbar from './Topbar.vue';
// import { ref, reactive } from 'vue';
// import { RouterLink } from 'vue-router';
// import $ from 'jquery';
import Entity from './subcomponents/Entity.vue';
import ListObject from './list-object.vue';
import Index from './Index.vue';

export default {
  name: 'body',
  data() {
    return {
      ietmStore: useIetmStore(),
    }
  },
  props: ['data', 'transformed_html'],
  methods: {
    renderDetail(htmlstring) {
      let html = new DOMParser();
      html = html.parseFromString(htmlstring, 'text/html');
      let body = html.body.firstElementChild;
      this.$refs.container.innerHTML = '';
      this.$refs.container.appendChild(body);
    },
  },
  components:{Entity, ListObject, Index},
  computed: {
    dynamic(){
      return {
        template: this.transformed_html,
        data(){
          return {
            ietmStore: useIetmStore(),
          }
        },
      }
    },
  },
}
</script>


<template>
  <div class="mx-auto text-center flex" id="ietmBody">

    <div id="detail-container" class="w-full border ml-8">
      <div class="text-2xl py-3 font-bold bg-slate-950 text-white">CONTENT</div>
      <component :is="dynamic" v-if="$props.transformed_html"/>
      <Index v-else/>
    </div>
    
    <div :class="[ietmStore.show ? 'w-full' : '', 'block ml-8 relative']">
      <div class="border w-full sticky top-0">
        <div class="text-2xl py-3 font-bold bg-slate-950 text-white">
          <span v-show="ietmStore.show">Right Side Panel</span>
          <button class="float-left material-icons bg-slate-950 text-white pb-3" @click="ietmStore.show = !ietmStore.show">{{ ietmStore.show ? 'chevron_right' : 'chevron_left' }}</button>
        </div>
        <div class="mt-2 mb-2 bg-slate-400" v-show="ietmStore.show">
          <button :class="[ietmStore.showListObject ? 'border-b-black border-b-4' : '', 'mx-1 text-xl font-bold shadow-lg']" @click="ietmStore.showListObject = !ietmStore.showListObject">&nbsp; List Object &nbsp;</button>
          <button :class="[ietmStore.showIdentSection ? 'border-b-black border-b-4' : '', 'mx-1 text-xl font-bold shadow-lg']" @click="ietmStore.showIdentSection = !ietmStore.showIdentSection;">&nbsp; Ident Section &nbsp;</button>
          <button :class="[ietmStore.showEntity ? 'border-b-black border-b-4' : '', 'mx-1 text-xl font-bold shadow-lg']" @click="ietmStore.showEntity = !ietmStore.showEntity;">&nbsp; Entity &nbsp;</button>
        </div>
        <div class="bg-white text-slate-950">
          <div v-show="ietmStore.show">
            <ListObject :repoName="$route.params.repoName" v-show="ietmStore.showListObject"/>
            <Entity v-show="ietmStore.showEntity"/>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>