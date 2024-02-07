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
      currentRouteName: '',
      filenameSearch: '',


    }
  },
  props: {
    filter: String
  },
  methods: {
    get_list(url = undefined, method = 'GET') {
      let send;
      if(!url){
        let params = {
          filenameSearch: this.filenameSearch
        };
        if(this.$props.filter == 'inEditting') {
          params.initiator_email = this.techpubStore.Auth.email;
        } else {
          params.stage = 'staged';
        }
        const route = this.techpubStore.getWebRoute('api.get_objects_list', params);
        send = axios({
          url: route.url,
          method: route.method[0],
        })
      } else {
        send = axios({
          url: url,
          method: method,
        })
      }
      send.then(response => {
          // tidak melakukan ini karena jika ada ribuan object di database, maka request akan lama, dan ram makin berat
          // this.techpubStore.OBJECTList = response.data.data
          this.responsedata_get_objects_list = response.data;
        })
        .catch(error => this.$root.error(error));
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
    }
  },
  mounted() {
    this.currentRouteName = this.$route.fullPath;
    this.get_list();
  },
  updated(){
    if(this.currentRouteName != this.$route.fullPath){
      this.get_list();
      this.currentRouteName = this.$route.fullPath;
    }
  }
}
</script>
<template>
  <div v-if="responsedata_get_objects_list" class="w-full">
    <h1 class="mb-2">Index Object</h1>
    <input @change="get_list()" v-model="filenameSearch" placeholder="find filename" type="text" class="w-48 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
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
          <td><a class="material-icons text-blue-600" :href="techpubStore.getWebRoute('', { filename: obj.filename }, Object.assign({}, $router.getRoutes().find((route) => route.name == 'DetailObject'))).path">details</a></td>
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