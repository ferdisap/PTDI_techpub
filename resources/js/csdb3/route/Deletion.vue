<script>
import { useTechpubStore } from '../../techpub/techpubStore';

export default {
  data(){
    return {
      techpubStore: useTechpubStore(),
      responsedata_get_deletion_list: {} // undefined
    }
  },
  methods: {
    get_list(params = {}) {
      let send;
      params = Object.assign(params, { filenameSearch: event.target.value || '' });
      const route = this.techpubStore.getWebRoute('api.get_deletion_list', params);
      send = axios({
        url: route.url,
        method: route.method[0],
      });
      send
        .then(response => this.responsedata_get_deletion_list = response.data)
        .catch(error => this.$root.error(error));
    },
  },
  mounted(){
    this.get_list();
  },
}
</script>
<template>
  <div class="Deletion">
    <h1>Index Deleted Object</h1>
    <div class="flex">
      <input @change="get_list()" placeholder="find filename" type="text" class="w-48 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
      <button class="material-icons mx-3 text-gray-500 text-sm has-tooltip-arrow" data-tooltip="info" @click="$root.infoData = {message: 'foobar',show:true}">info</button>
    </div>
    <div class="flex">      
      <table class="w-full table-cell">
        <thead class="h-10">
          <tr>
            <th>Filename</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="deletion in responsedata_get_deletion_list.data">
            <td><a href="#" @click="filenameAnalysis = deletion.filename">{{ deletion.filename }}</a></td>
            <td>{{ techpubStore.date(deletion.created_at) }}</td>
            <td>
              <button class="material-icons text-green-700 has-tooltip-arrow" data-tooltip="Restore">restore_from_trash</button>
              <button @click="deleteDeletion(deletion.filename)" class="material-icons text-red-700 has-tooltip-arrow" data-tooltip="Permanent Delete">delete_forever</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="flex justify-center items-center">
      <button @click="goto(responsedata_get_deletion_list.current_page > 0 ? responsedata_get_deletion_list.current_page-1 : 1 )" class="material-symbols-outlined">navigate_before</button>
      <form @submit.prevent="goto" class="inline-block">
        <input v-model="page" class="w-6"/>
        <span> of {{ responsedata_get_deletion_list.last_page }} </span>
      </form>
      <button @click="goto(responsedata_get_deletion_list.current_page < responsedata_get_deletion_list.last_page ? responsedata_get_deletion_list.current_page+1 : responsedata_get_deletion_list.last_page )" class="material-symbols-outlined">navigate_next</button>
    </div>
  </div>  
</template>