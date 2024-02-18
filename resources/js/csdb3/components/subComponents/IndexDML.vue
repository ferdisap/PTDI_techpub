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
    }
  },
  mounted() {
    // this.techpubStore.get_list('dml');
    // this.techpubStore.get_list('csl');
    this.emitter.on('api.restore_object', (data) => {
      if (data.filename.substr(0, 3) === 'DML') {
        this.techpubStore.get_list('dml')
      }
      else if (data.filename.substr(0, 3) === 'CSL') {
        this.techpubStore.get_list('csl')
      }
    });
  },
}
</script>
<template>
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

  <AnalyzeDML v-if="filenameAnalysis" :filename="filenameAnalysis" />


  <!-- <div class="IndexDML" v-if="techpubStore.dml_list">
    <h1>Index DML</h1>
    <div class="flex">
      <input @change="techpubStore.get_list('dml')" v-model="techpubStore.dml_filenameSearch" placeholder="find filename" type="text" class="w-48 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        <button class="material-icons mx-3 text-gray-500 text-sm has-tooltip-arrow" data-tooltip="info" @click="$root.info({name: 'searchCsdbObject'})">info</button>
    </div>
    <div class="flex">
      <table class="w-full table-cell">
        <thead class="h-10">
          <tr>
            <th>Filename</th>
            <th>Editable</th>
            <th>Initiator</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="dml in techpubStore.dml_list.data">
            <td><a href="#" @click="filenameAnalysis = dml.filename">{{ dml.filename }}</a></td>
            <td>{{ dml.editable ? 'yes' : 'no' }} </td>
            <td>{{ dml.initiator.name == techpubStore.Auth.name ? 'self' : dml.initiator.name }} </td>
            <td :class="[dml.remarks.stage == 'staged' ? 'bg-green-500' : 'bg-yellow-500']">{{ dml.remarks.stage }}</td>
            <td>
              <a class="material-icons text-blue-600 has-tooltip-arrow" data-tooltip="Detail"
                :href="techpubStore.getWebRoute('', { filename: dml.filename }, Object.assign({}, $router.getRoutes().find((route) => route.name == 'DetailDML'))).path">details</a>
              <button @click="deleteDML(dml.filename)" class="material-icons text-red-500 has-tooltip-arrow"
                data-tooltip="Delete">delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="flex justify-center items-center">
      <button @click="techpubStore.goto('dml',techpubStore.dml_list.current_page > 0 ? techpubStore.dml_list.current_page - 1 : 1)"
        class="material-symbols-outlined">navigate_before</button>
      <form @submit.prevent="techpubStore.goto('dml', techpubStore.dml_page)" class="inline-block">
        <input v-model="techpubStore.dml_page" class="w-6" />
        <span> of {{ techpubStore.dml_list.last_page }} </span>
      </form>
      <button
        @click="techpubStore.goto('dml',techpubStore.dml_list.current_page < techpubStore.dml_list.last_page ? techpubStore.dml_list.current_page + 1 : techpubStore.dml_list.last_page)"
        class="material-symbols-outlined">navigate_next</button>
    </div>
  </div> -->


  <!-- <div class="IndexCSL" v-if="techpubStore.csl_list">
    <h1>Index CSL</h1>
    <div class="flex">
      <input @change="techpubStore.get_list('csl')" v-model="techpubStore.csl_filenameSearch" placeholder="find filename" type="text" class="w-48 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        <button class="material-icons mx-3 text-gray-500 text-sm has-tooltip-arrow" data-tooltip="info" @click="$root.info({name: 'searchCsdbObject'})">info</button>
    </div>
    <div class="flex">
      <table class="w-full table-cell">
        <thead class="h-10">
          <tr>
            <th>Filename</th>
            <th>Editable</th>
            <th>Initiator</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="csl in techpubStore.csl_list.data">
            <td><a href="#" @click="filenameAnalysis = csl.filename">{{ csl.filename }}</a></td>
            <td>{{ csl.editable ? 'yes' : 'no' }} </td>
            <td>{{ csl.initiator.name == techpubStore.Auth.name ? 'self' : csl.initiator.name }} </td>
            <td :class="[csl.remarks.stage == 'staged' ? 'bg-green-500' : 'bg-yellow-500']">{{ csl.remarks.stage }}</td>
            <td>
              <a class="material-icons text-blue-600 has-tooltip-arrow" data-tooltip="Detail"
                :href="techpubStore.getWebRoute('', { filename: csl.filename }, Object.assign({}, $router.getRoutes().find((route) => route.name == 'DetailDML'))).path">details</a>
              <button @click="deletecsl(csl.filename)" class="material-icons text-red-500 has-tooltip-arrow"
                data-tooltip="Delete">delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="flex justify-center items-center">
      <button @click="techpubStore.goto('csl',techpubStore.csl_list.current_page > 0 ? techpubStore.csl_list.current_page - 1 : 1)"
        class="material-symbols-outlined">navigate_before</button>
      <form @submit.prevent="techpubStore.goto('csl', techpubStore.csl_page)" class="inline-block">
        <input v-model="techpubStore.csl_page" class="w-6" />
        <span> of {{ techpubStore.csl_list.last_page }} </span>
      </form>
      <button
        @click="techpubStore.goto('csl',techpubStore.csl_list.current_page < techpubStore.csl_list.last_page ? techpubStore.csl_list.current_page + 1 : techpubStore.csl_list.last_page)"
        class="material-symbols-outlined">navigate_next</button>
    </div>
</div> -->
</template>