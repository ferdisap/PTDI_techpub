<script>
import axios from 'axios';
import { useTechpubStore } from '../../../techpub/techpubStore';
import { ref } from 'vue';
export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      page: ref(1),
      responsedata_get_objects_list: undefined, // {}
      filenameSearch: '',


    }
  },
  props: {
    filter: String
  },
  methods: {
    async get_list(url = undefined, method = 'GET') {
      let send;
      const config = {};
      if(!url){
        let params = {
          filenameSearch: this.filenameSearch
        };
        if(this.$props.filter == 'inEditting') {
          params.initiator_email = this.techpubStore.Auth.email;
        } else {
          params.stage = 'staged';
        }
        config.route = {
          name: 'api.get_objects_list',
          data: params,
        }
      } else {
        config.url = url;
        config.method = method;
      }
      let response = await axios(config);
      if(response.statusText === 'OK'){
        this.responsedata_get_objects_list = response.data;
      }
    },
    goto(page = undefined){
      let url = new URL(this.responsedata_get_objects_list.path);
      if(!page){
        url.searchParams.set('page',this.page);
      } else {
        url.searchParams.set('page',page);
      }
      axios.get(url)
      .then(response => this.responsedata_get_objects_list = response.data)
      .catch(error => this.$root.error(error));
    },
    async deleteObject(filename) {
      let eventTarget = event.target;
      if (!(await this.$root.alert({ name: 'beforeDeleteCsdbObject', filename: filename }))) {
        return;
      }
      const config = {
        route: {
          name: 'api.delete_object',
          data: {filename: filename}
        }
      }
      let response = await axios(config)
      if(response.statusText === 'OK'){
        $(eventTarget).parents('tr').eq(0).remove();
      }        
    },
    detailObject(filename){
      this.$router.push({name: 'DetailObject',params:{filename: filename}});
      setTimeout(() => {
        this.emitter.emit('DetailObject', {filename: filename});
      }, 0);
    }
  },
  mounted() {
    this.get_list();
    this.emitter.on('api.restore_object', (data) => {
      if(['DMC','ICN'].includes(data.filename.substr(0,3))){
        this.get_list();
      }
    });
    this.emitter.on('api.edit_object', (data) => {
      if(['DMC','ICN'].includes(data.filename.substr(0,3))){
        this.get_list();
      }
    });
  },
}
</script>
<template>
  <div v-if="responsedata_get_objects_list" class="w-full">
    <h1 class="mb-2">Index Object</h1>
    <div class="flex">
      <input @change="get_list()" v-model="filenameSearch" placeholder="find filename" type="text" class="w-48 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
      <button class="material-icons mx-3 text-gray-500 text-sm has-tooltip-arrow" data-tooltip="info" @click="$root.info({name: 'searchCsdbObject'})">info</button>
    </div>
    <br/>
    <table class="table-cell">
      <thead class="h-10">
        <tr>
          <th>Filename</th>
          <th>Stage | Editable</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="obj in responsedata_get_objects_list.data">
          <td>
            <a href="javascript:void(0)">{{ obj.filename }}</a>
          </td>
          <td>
            {{ obj.remarks.stage }} | {{ obj.editable ? 'yes' : 'no' }}
          </td>
          <td>
            <a @click.prevent="detailObject(obj.filename)" class="material-icons text-blue-600 has-tooltip-arrow" data-tooltip="Detail" :href="techpubStore.getWebRoute('', { filename: obj.filename }, Object.assign({}, $router.getRoutes().find((route) => route.name == 'DetailObject'))).path">details</a>
            <button @click="deleteObject(obj.filename)" class="material-icons text-red-500 has-tooltip-arrow" data-tooltip="Delete">delete</button>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="flex justify-center items-center">
      <button @click="goto(responsedata_get_objects_list.current_page > 0 ? responsedata_get_objects_list.current_page-1 : 1 )" class="material-symbols-outlined">navigate_before</button>
      <form @submit.prevent="goto" class="inline-block">
        <input v-model="page" class="w-6"/>
        <span> of {{ responsedata_get_objects_list.last_page }} </span>
      </form>
      <button @click="goto(responsedata_get_objects_list.current_page < responsedata_get_objects_list.last_page ? responsedata_get_objects_list.current_page+1 : responsedata_get_objects_list.last_page )" class="material-symbols-outlined">navigate_next</button>
    </div>
  </div>
</template>