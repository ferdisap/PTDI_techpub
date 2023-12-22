<script>
import { useTechpubStore } from '../techpubStore.js';
export default {
  data(){
    return {
      techpubStore: useTechpubStore()
    }
  },
}
</script>

<template>
  <div class="h-1 sticky top-0" style="z-index: 100;">
    <div class="wrapper" v-show="techpubStore.showLoadingBar">
      <div class="loadingBar"></div>
    </div>
  </div>
</template>