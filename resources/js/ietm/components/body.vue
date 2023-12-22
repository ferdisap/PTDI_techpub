<script>
import { useIetmStore } from '../ietmStore';
// import Topbar from './Topbar.vue';
import { ref, reactive } from 'vue';
// import { RouterLink } from 'vue-router';
// import $ from 'jquery';
import Entity from './subcomponents/Entity.vue';
import ListObject from './list-object.vue';
import Index from './Index.vue';

export default {
  data() {
    return {
      ietmStore: useIetmStore(),
      showIndex: false,
      // current: reactive({
      //   repoName: '',
      //   filename: ''
      // }),

    }
  },
  // props: ['data', 'transformed_html'],
  // props: ['filename'], // penting agar jika nanti ada update didalam body, compoent tidak akan merender lagi (beforeUpdate hanya check saja)
  props: ['repoName', 'filename'],
  methods: {
    renderDetail(htmlstring) {
      let html = new DOMParser();
      html = html.parseFromString(htmlstring, 'text/html');
      let body = html.body.firstElementChild;
      this.$refs.container.innerHTML = '';
      this.$refs.container.appendChild(body);
    },
  },
  components: { Entity, ListObject, Index },
  computed: {
    dynamic() {
      let repoName = this.$route.params.repoName;
      let filename = this.$route.params.filename;
      let obj = this.ietmStore.getObj(repoName, filename);
      return {
        template: obj ? obj.transformed_html : '',
        data() {
          return {
            ietmStore: useIetmStore(),
          }
        },
      }
    },
  },
  async beforeMount() {
    if (this.filename && this.repoName) {
      let response = await this.ietmStore.getDetailObject(this.repoName, this.filename);
      this.data = response.data;
      this.ietmStore.addObjects({
        repoName: this.repoName,
        filename: this.filename,
        transformed_html: response.data.repos[0].objects[0].transformed_html,
      });
    }

  },
  async beforeUpdate() {
    let obj;
    if (!(obj = this.ietmStore.getObj(this.$route.params.repoName, this.$route.params.filename))) {
      let response = await this.ietmStore.getDetailObject(this.$route.params.repoName, this.$route.params.filename);
      if (response.statusText == 'OK') {
        this.ietmStore.addObjects({
          repoName: this.$route.params.repoName,
          filename: this.$route.params.filename,
          transformed_html: response.data.repos[0].objects[0].transformed_html,
        });
      }
      else {
        this.$root.messages = response.data.messages;
        this.$root.showMessages = true;
      }
    }
  },
}
</script>


<template>
  <div class="mx-auto text-center flex" id="ietmBody">

    <div id="detail-container" class="w-full border ml-8">
      <div class="text-2xl py-3 font-bold bg-slate-950 text-white">CONTENT</div>
      <!-- <div class="mt-2 mb-2 bg-slate-400" v-show="ietmStore.show"> -->
      <div class="mt-2 mb-2 bg-slate-400">
        <button :class="[ietmStore.showIdentSection ? 'border-b-black border-b-4' : '', 'mx-1 text-xl font-bold shadow-lg']"
          @click="ietmStore.showIdentSection = !ietmStore.showIdentSection;">&nbsp; Ident Section &nbsp;</button>
        <button :class="[showIndex ? 'border-b-black border-b-4' : '', 'mx-1 text-xl font-bold shadow-lg']"
          @click="showIndex = !showIndex;">&nbsp; Index &nbsp;</button>
      </div>

      <Index v-show="showIndex"/>
      <component :is="dynamic" v-show="!showIndex" />
    </div>

    <div :class="[ietmStore.show ? 'w-full' : '', 'block ml-8 relative']">
      <div class="border w-full sticky top-0">
        <div class="text-2xl py-3 font-bold bg-slate-950 text-white">
          <span v-show="ietmStore.show">Right Side Panel</span>
          <button class="float-left material-icons bg-slate-950 text-white pb-3"
            @click="ietmStore.show = !ietmStore.show">{{ ietmStore.show ? 'chevron_right' : 'chevron_left' }}</button>
        </div>
        <div class="mt-2 mb-2 bg-slate-400" v-show="ietmStore.show">
          <button
            :class="[ietmStore.showListObject ? 'border-b-black border-b-4' : '', 'mx-1 text-xl font-bold shadow-lg']"
            @click="ietmStore.showListObject = !ietmStore.showListObject">&nbsp; List Object &nbsp;</button>
          <button :class="[ietmStore.showEntity ? 'border-b-black border-b-4' : '', 'mx-1 text-xl font-bold shadow-lg']"
            @click="ietmStore.showEntity = !ietmStore.showEntity;">&nbsp; Entity &nbsp;</button>
        </div>
        <div class="bg-white text-slate-950">
          <div v-show="ietmStore.show">
            <ListObject :repoName="$route.params.repoName" v-show="ietmStore.showListObject" />
            <Entity v-show="ietmStore.showEntity" />
            <!-- <Entity/> -->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>