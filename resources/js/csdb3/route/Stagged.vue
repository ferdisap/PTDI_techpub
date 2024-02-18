<script>
import { useTechpubStore } from '../../techpub/techpubStore';
import CreateDML from '../components/subComponents/CreateDML.vue';
import IndexDML from '../components/subComponents/IndexDML.vue';
import IndexBR from '../components/subComponents/IndexBR.vue';
import IndexCSDB from '../components/subComponents/IndexCSDB.vue';

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      showDML: '',
    }
  },
  components: { IndexCSDB },
  // components: {CreateDML, IndexDML, IndexBR},
  methods: {
    async deleteObject(filename) {
      let eventTarget = event.target;
      if (!(await this.$root.alert({ name: 'beforeDeleteCsdbObject', filename: filename }))) {
        return;
      }
      const config = {
        route: {
          name: 'api.delete_object',
          data: { filename: filename }
        }
      }
      let response = await axios(config)
      if (response.statusText === 'OK') {
        $(eventTarget).parents('tr').eq(0).remove();
      }
    },
  }
}
</script>
<template>
  <!-- DMC staged -->
  <IndexCSDB type="dmc_staged">
    <template #title>Index DMC:staged</template>
    <template #actionColumn="actionColumnProps">
      <a class="material-icons text-blue-600 has-tooltip-arrow" data-tooltip="Detail"
        :href="techpubStore.getWebRoute('', { filename: actionColumnProps.filename }, Object.assign({}, $router.getRoutes().find((route) => route.name == 'DetailObject'))).path">details</a>
      <button @click="deleteObject(actionColumnProps.filename)" class="material-icons text-red-500 has-tooltip-arrow"
        data-tooltip="Delete">delete</button>
    </template>
  </IndexCSDB>

  <!-- ICN staged -->
  <IndexCSDB type="icn_staged">
    <template #title>Index ICN:staged</template>
    <template #actionColumn="actionColumnProps">
      <a class="material-icons text-blue-600 has-tooltip-arrow" data-tooltip="Detail"
        :href="techpubStore.getWebRoute('', { filename: actionColumnProps.filename }, Object.assign({}, $router.getRoutes().find((route) => route.name == 'DetailObject'))).path">details</a>
      <button @click="deleteObject(actionColumnProps.filename)" class="material-icons text-red-500 has-tooltip-arrow"
        data-tooltip="Delete">delete</button>
    </template>
  </IndexCSDB>



  <!-- <div class="Committing"> -->

  <!-- <div class="w-full text-center mb-3 mt-3">
      <button :class="[showDML == 'createDML' ? 'border-b-black border-b-4' : '' ,'button-nav']" @click="showDML = 'createDML'">Create DML</button>
      <button :class="[showDML == 'createBrex' ? 'border-b-black border-b-4' : '' ,'button-nav']" @click="showDML = 'createBrex'">Create BREX</button>
    </div> -->



  <!-- <CreateDML v-if="showDML == 'createDML'"/> -->

  <!-- <div class="mb-3">
      <IndexDML/>
    </div>
    <div class="mb-3">
      <IndexBR/>
    </div> -->

  <!-- </div> -->
</template>