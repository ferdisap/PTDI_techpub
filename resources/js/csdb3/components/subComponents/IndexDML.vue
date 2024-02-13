<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
import CreateDML from './CreateDML.vue';
import AnalyzeDML from './AnalyzeDML.vue';
import axios from 'axios';

export default {
  data(){
    return {
      techpubStore: useTechpubStore(),
      filenameAnalysis: '',
      // filenameAnalysis: 'DML-MALE-0001Z-P-2024-00001_000-01.xml',

      responsedata_get_dml_list: undefined, // {}
      responsedata_get_csl_list: undefined, // {}
      page: 1,

      // tes: {0: 'foo', 1:'bar', '3':'baz'}
    }
  },
  components: { CreateDML, AnalyzeDML },
  props:['isInEditing'],
  methods:{
    get_list(params = {}) {
      let send;
      params = Object.assign(params, { filenameSearch: event.target.value || '' });
      const route = this.techpubStore.getWebRoute('api.get_dml_list', params);
      send = axios({
        url: route.url,
        method: route.method[0],
      });
      send
        // .then(response => this.techpubStore.DMLList = response.data)
        .then(response => {
          if(params.csl){
            this.responsedata_get_csl_list = response.data
          } else {
            this.responsedata_get_dml_list = response.data
          }
        })
        .catch(error => this.$root.error(error));
    },
    goto(page = undefined){
      let url = new URL(this.responsedata_get_dml_list.path);
      if(!page){
        url.searchParams.set('page',this.page);
      } else {
        url.searchParams.set('page',page);
      }
      axios.get(url)
      .then(response => this.responsedata_get_dml_list = response.data)
      .catch(error => this.$root.error(error));
    },
    async deleteDML(filename){
      let eventTarget = event.target;
      if(!(await this.alert({message:`Are you sure to <b>DELETE</b> ${filename}?`}).result)){
        return;
      }
      const route = this.techpubStore.getWebRoute('api.delete_object',{filename: filename});
      axios({
        url: route.url,
        method: route.method[0],
      })
      .then(rsp => {
        this.$root.success(rsp);
        $(eventTarget).parents('tr').eq(0).remove();
      })
      .catch(e => this.$root.error(e));
    }
  },
  mounted(){
    this.get_list({dml:1});
    this.get_list({csl:1});
  },
}
</script>
<template>

  <!-- <div>
    <div v-for="t in tes">
      {{ t }}
    </div>
    <button class="button" @click="delete tes['1']">del</button>
  </div> -->

  <div class="IndexDML" v-if="responsedata_get_dml_list">
    <h1>Index DML</h1>
    <input @change="get_list({dml:1})" placeholder="find filename" type="text" class="w-48 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
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
          <tr v-for="dml in responsedata_get_dml_list.data">
            <td><a href="#" @click="filenameAnalysis = dml.filename">{{ dml.filename }}</a></td>
            <td>{{ dml.editable ? 'yes' : 'no' }} </td>
            <td>{{ dml.initiator.name == techpubStore.Auth.name ? 'self' : dml.initiator.name }} </td>
            <td :class="[dml.remarks.stage == 'staged' ? 'bg-green-500' : 'bg-yellow-500']">{{ dml.remarks.stage }}</td>
            <td>
              <a class="material-icons text-blue-600 has-tooltip-arrow" data-tooltip="Detail" :href="techpubStore.getWebRoute('',{filename: dml.filename}, Object.assign({},$router.getRoutes().find((route) => route.name == 'DetailDML'))).path">details</a>
              <button @click="deleteDML(dml.filename)" class="material-icons text-red-500 has-tooltip-arrow" data-tooltip="Delete">delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="flex justify-center items-center">
      <button @click="goto(responsedata_get_dml_list.current_page > 0 ? responsedata_get_dml_list.current_page-1 : 1 )" class="material-symbols-outlined">navigate_before</button>
      <form @submit.prevent="goto" class="inline-block">
        <input v-model="page" class="w-6"/>
        <span> of {{ responsedata_get_dml_list.last_page }} </span>
      </form>
      <button @click="goto(responsedata_get_dml_list.current_page < responsedata_get_dml_list.last_page ? responsedata_get_dml_list.current_page+1 : responsedata_get_dml_list.last_page )" class="material-symbols-outlined">navigate_next</button>
    </div>
  </div>

  <div class="IndexCSL" v-if="responsedata_get_csl_list">
    <h1>Index CSL</h1>
    <input @change="get_list({csl:1})" placeholder="find filename" type="text" class="w-48 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
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
          <tr v-for="dml in responsedata_get_csl_list.data">
            <td><a href="#" @click="filenameAnalysis = dml.filename">{{ dml.filename }}</a></td>
            <td>{{ dml.editable ? 'yes' : 'no' }} </td>
            <td>{{ dml.initiator.name == techpubStore.Auth.name ? 'self' : dml.initiator.name }} </td>
            <td :class="[dml.remarks.stage == 'staged' ? 'bg-green-500' : 'bg-yellow-500']">{{ dml.remarks.stage }}</td>
            <td><a class="material-icons text-blue-600 has-tooltip-arrow" data-tooltip="Detail" :href="techpubStore.getWebRoute('',{filename: dml.filename}, Object.assign({},$router.getRoutes().find((route) => route.name == 'DetailDML'))).path">details</a></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="flex justify-center items-center">
      <button @click="goto(responsedata_get_csl_list.current_page > 0 ? responsedata_get_csl_list.current_page-1 : 1 )" class="material-symbols-outlined">navigate_before</button>
      <form @submit.prevent="goto" class="inline-block">
        <input v-model="page" class="w-6"/>
        <span> of {{ responsedata_get_csl_list.last_page }} </span>
      </form>
      <button @click="goto(responsedata_get_csl_list.current_page < responsedata_get_csl_list.last_page ? responsedata_get_csl_list.current_page+1 : responsedata_get_csl_list.last_page )" class="material-symbols-outlined">navigate_next</button>
    </div>
  </div>

  <!-- <div> -->
    <!-- <component v-if="filenameAnalysis" is="AnalyzeDML" :filename="filenameAnalysis"/> -->
  <!-- </div> -->
  <AnalyzeDML v-if="filenameAnalysis" :filename="filenameAnalysis"/>
</template>