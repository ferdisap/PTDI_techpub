<script>
import { useIetmStore } from '../ietmStore';
import Topbar from './topbar.vue';
import { reactive } from 'vue';
import { RouterLink } from 'vue-router'

export default {
  data() {
    return {
      ietmStore: useIetmStore(),
      // filename: undefined,
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
    link(filename){
      this.$router.push({ name: 'Detail', params: { repoName: this.$route.params.repoName, filename: filename } });
    }
  },
  mounted() {
    window.ietmBody = this;
  },
}
</script>


<template>
  <div class="container mx-auto text-center">

    <div v-if="$props.transformed_html">
      {{ renderDetail($props.transformed_html) }}
    </div>

    <div ref="content">
      <div ref="container" id="detail-container"></div>
    </div>
  </div>
</template>