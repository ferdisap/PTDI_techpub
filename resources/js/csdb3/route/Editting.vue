<script>
import IndexDML from '../components/subComponents/IndexDML.vue';
import { useTechpubStore } from '../../techpub/techpubStore';
import Editor from '../components/subComponents/Editor.vue';
import IndexObject from '../components/subComponents/IndexObject.vue';
import IndexCSDB from '../components/subComponents/IndexCSDB.vue';

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
    }
  },
  components: {IndexCSDB, IndexObject},
}
</script>
<template>

  <!-- ini tidak dipakai nanti -->
  <!-- <div class="mb-5">
    <IndexObject/>
  </div> -->

  <IndexCSDB type="dmc_unstaged">
    <template #title>Index DMC:un-staged</template>
    <template #actionColumn="actionColumnProps">
      <a class="material-icons text-blue-600 has-tooltip-arrow" data-tooltip="Detail"
        :href="techpubStore.getWebRoute('', { filename: actionColumnProps.filename }, Object.assign({}, $router.getRoutes().find((route) => route.name == 'DetailObject'))).path">details</a>
      <button @click="deleteObject(actionColumnProps.filename)" class="material-icons text-red-500 has-tooltip-arrow"
        data-tooltip="Delete">delete</button>
    </template>
  </IndexCSDB>


  
</template>