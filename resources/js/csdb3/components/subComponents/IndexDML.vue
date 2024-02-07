<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
import CreateDML from './CreateDML.vue';
import AnalyzeDML from './AnalyzeDML.vue';

export default {
  data(){
    return {
      techpubStore: useTechpubStore(),
      filenameAnalysis: '',
      // filenameAnalysis: 'DML-MALE-0001Z-P-2024-00001_000-01.xml',

      filenameSearch: '',
      responsedata_get_dml_list: undefined, // {}
      page: 1,
    }
  },
  components: { CreateDML, AnalyzeDML },
  props:['isInEditing'],
  methods:{
    get_list() {
      let send;
      let params = { filenameSearch: this.filenameSearch };
      const route = this.techpubStore.getWebRoute('api.get_dml_list', params);
      send = axios({
        url: route.url,
        method: route.method[0],
      });
      send
        // .then(response => this.techpubStore.DMLList = response.data)
        .then(response => this.responsedata_get_dml_list = response.data)
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
  },
  mounted(){
    this.get_list();
  },
}
</script>
<template>

  <div class="IndexDML" v-if="responsedata_get_dml_list">
    <h1>Index DML</h1>
    <input @change="get_list()" v-model="filenameSearch" placeholder="find filename" type="text" class="w-48 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
    <div class="flex">      
      <table class="w-full table-cell">
        <h3>Editable DML</h3>
        <thead class="h-10">
          <tr>
            <th>Filename</th>
            <th>Editable</th>
            <th>Initiator</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="dml in responsedata_get_dml_list.data">
            <td><a href="#" @click="filenameAnalysis = dml.filename">{{ dml.filename }}</a></td>
            <td>{{ dml.editable ? 'yes' : 'no' }} </td>
            <td>{{ dml.initiator.name == techpubStore.Auth.name ? 'self' : dml.initiator.name }} </td>
            <td><a class="material-icons text-blue-600" :href="techpubStore.getWebRoute('',{filename: dml.filename}, Object.assign({},$router.getRoutes().find((route) => route.name == 'DetailDML'))).path">details</a></td>
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

  <div>
    <component v-if="filenameAnalysis" is="AnalyzeDML" :filename="filenameAnalysis"/>
  </div>
</template>