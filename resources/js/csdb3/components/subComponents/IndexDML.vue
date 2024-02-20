<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
import CreateDML from './CreateDML.vue';
import AnalyzeDML from './AnalyzeDML.vue';
import axios from 'axios';
import IndexCSDB from './IndexCSDB.vue';

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      filenameAnalysis: '',
      // showAnalysis: false,
    }
  },
  components: { IndexCSDB, CreateDML, AnalyzeDML },
  props: ['isInEditing'],
  methods: {
    async deleteDML(filename) {
      let eventTarget = event.target;
      if (!(await this.$root.alert({ name: 'beforeDeleteDML', filename: filename }))) {
        return;
      }
      let response = await axios({
        route: {
          name: 'api.delete_object',
          data: { filename: filename },
        }
      });
      if (response.statusText === 'OK') {
        $(eventTarget).parents('tr').eq(0).remove();
      }
    },
    clickFilename(filename){
      this.filenameAnalysis = filename;
      // this.showAnalysis = true;
    }
  },
  mounted() {
    // this.techpubStore.get_list('dml');
    // this.techpubStore.get_list('csl');
    // this.emitter.on('api.restore_object', (data) => {
    //   if (data.filename.substr(0, 3) === 'DML') {
    //     this.techpubStore.get_list('dml')
    //   }
    //   else if (data.filename.substr(0, 3) === 'CSL') {
    //     this.techpubStore.get_list('csl')
    //   }
    // });
  },
}
</script>
<template>
  <div class="flex">
    <div :class="[filenameAnalysis ? 'w-2/3' : 'w-full', 'mr-3']">
      <!-- DML -->
      <IndexCSDB type="dml" :clickFilename="clickFilename">
        <template #title>Index DML</template>
        <template #actionColumn="actionColumnProps">
          <a class="material-icons text-blue-600 has-tooltip-arrow" data-tooltip="Detail"
            :href="techpubStore.getWebRoute('', { filename: actionColumnProps.filename }, Object.assign({}, $router.getRoutes().find((route) => route.name == 'DetailObject'))).path">details</a>
          <button @click="deleteDML(actionColumnProps.filename)" class="material-icons text-red-500 has-tooltip-arrow"
            data-tooltip="Delete">delete</button>
        </template>
      </IndexCSDB>
    
      <!-- CSL -->
      <IndexCSDB type="csl" :clickFilename="clickFilename">
        <template #title>Index CSL</template>
        <template #actionColumn="actionColumnProps">
          <a class="material-icons text-blue-600 has-tooltip-arrow" data-tooltip="Detail"
            :href="techpubStore.getWebRoute('', { filename: actionColumnProps.filename }, Object.assign({}, $router.getRoutes().find((route) => route.name == 'DetailObject'))).path">details</a>
          <button @click="deleteDML(actionColumnProps.filename)" class="material-icons text-red-500 has-tooltip-arrow"
            data-tooltip="Delete">delete</button>
        </template>
      </IndexCSDB>
    </div>
  
    <AnalyzeDML v-if="filenameAnalysis" :filename="filenameAnalysis" />
    
  </div>
</template>