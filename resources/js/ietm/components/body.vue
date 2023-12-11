<script>
import { useIetmStore } from '../ietmStore';
import Topbar from './topbar.vue';
import { reactive } from 'vue';

export default {
  data() {
    return {
      ietmStore: useIetmStore(),
      filename: undefined,
    }
  },
  props: ['data', 'filename', 'transformed_html'],
  methods: {
    renderDetail(htmlstring) {
      let html = new DOMParser();
      html = html.parseFromString(htmlstring, 'text/html');
      this.$refs.container.innerHTML = '';
      this.$refs.container.appendChild(html.body);
    }
  },
  updated(){
    this.renderDetail(this.$props.transformed_html);
    // console.log(this.$props.transformed_html);
  }
}
</script>


<template>
  <div class="container mx-auto text-center">
    <h1 class="text-2xl font-bold mt-5">BODY CONTENT</h1>

    <!-- <div v-if="ietmStore.detailObject">
      {{ renderDetail(ietmStore.detailObject.repos[0].objects[0].transformed_html) }}
    </div> -->
    <div v-if="$props.transformed_html">
      {{ renderDetail($props.transformed_html) }}
    </div>


    <div ref="content">Body PAGE
      <div ref="container"></div>
    </div>
  </div>
</template>