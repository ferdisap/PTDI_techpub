<script>
import { useRoute } from 'vue-router';
import { useTechpubStore } from '../../../techpub/techpubStore';
import axios from 'axios';

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      dmlEntryList: []
    }
  },
  methods: {
    submit(){
      const formData = new FormData(event.target);
      window.formData = formData;
      const route = this.techpubStore.getWebRoute('api.pushtostage',formData);
      axios({
        url: route.url,
        method: route.method[0],
        data: formData,
      })
      .then(response => this.$root.success(response))
      .catch(error => this.$root.error(error));
    },
    getDmlEntries() {
      const route = this.techpubStore.getWebRoute('api.get_entry', { filename: this.$route.params.filename });
      axios({
        url: route.url,
        method: route.method[0],
      })
        .then(response => this.dmlEntryList = response.data)
        .catch(error => this.$root.error(error));
    }
  }
}
</script>
<template>
  <div>
    <form @submit.prevent="submit()">
      <div class="w-1/2">
        <label for="dmlFilename" class="block mb-2 text-gray-900 dark:text-white text-lg font-bold">DML</label>
        <span>The DML is required to be filled.</span>
        <input type="text" id="dmlFilename" name="dmlFilename" :value="$route.params.filename" placeholder="type the DML file.."
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
        <div class="text-red-600" v-html="techpubStore.error('dml')"></div>
      </div>
      <div class="mt-3">
        <h5>Choose Your Data Module <a @click="getDmlEntries()" class="font-normal underline hover:text-blue-500"
            href="#">click to open list</a></h5>
        <table>
          <thead>
            <tr>
              <th>DML Entry
                <Sort />
              </th>
              <th>Object available</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="entry in dmlEntryList">
              <td> {{ entry.code }} </td>
              <td>
                <div v-for="obj in entry.objects" class="flex">
                  <input type="checkbox" name="objects[]" :value="obj.filename" class="mx-1" />
                  {{ obj.filename }}
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <button class="button-violet">Push</button>
      </div>
    </form>
  </div>
</template>