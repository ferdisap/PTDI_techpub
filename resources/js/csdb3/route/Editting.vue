<script>
import IndexDML from '../components/subComponents/IndexDML.vue';
import { useTechpubStore } from '../../techpub/techpubStore';
import Editor from '../components/subComponents/Editor.vue';
import IndexObject from '../components/subComponents/IndexObject.vue';
export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
    }
  },
  components: {IndexObject},
}
</script>
<template>

  <!-- ini tidak dipakai nanti -->

  <div class="mb-5">
    <IndexObject/>
  </div>
  
</template>