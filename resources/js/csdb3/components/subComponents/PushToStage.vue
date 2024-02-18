<script>
import { useRoute } from 'vue-router';
import { useTechpubStore } from '../../../techpub/techpubStore';
import axios from 'axios';
import Sort from '../../../techpub/components/Sort.vue';

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      dmlEntryList: [],
      cslEntryList: [],
      filename: ''
    }
  },
  components:{Sort},
  methods: {
    async createCSL() {
      // this.$root.showMessages = false;
      const formData = new FormData(event.target);
      if(!formData.get('filename')){
        formData.set('filename', this.filename);
      }
      let response = await axios({
        route: {
          name: 'api.tostaging',
          data: formData
        }
      });
      if(response.statusText === 'OK'){
        this.cslEntryList.push(response.data.csl);
      }
      // const route = this.techpubStore.getWebRoute('api.tostaging', formData);
      // axios({
      //   url: route.url,
      //   method: route.method[0],
      //   data: formData,
      // })
      //   .then(response => {
      //     this.$root.success(response);
      //     this.cslEntryList.push(response.data.csl);
      //     window.cslEntryList = this.cslEntryList;
      //   })
      //   .catch(error => this.$root.error(error));
    },
    async getCSL_forstaging(){
      let response = await axios({
        route: {
          name: 'api.get_csl_forstaging'
        }
      })
      if(response.statusText === 'OK'){
        this.cslEntryList = response.data;
      }
    },
    async pushToStaging(filename){
      console.log('pushToStaging');
      let eventTarget = event.target;
      let response = await axios({
        route: {
          name: 'api.push_csl_forstaging',
          data: {filename: filename}
        }
      })
      if(response.statusText === 'OK'){
        $(eventTarget).parents('tr').eq(0).remove();
      }
      // this.$root.showMessages = false;
      // const route = this.techpubStore.getWebRoute('api.push_csl_forstaging',{filename: filename});
      // axios({
      //   url: route.url,
      //   method: route.method[0],
      // })
      //   .then(response => {
      //     this.$root.success(response);
      //     let index = this.cslEntryList.indexOf(this.cslEntryList.find(csl => csl.filename == filename))
      //     this.cslEntryList.splice(index,1);
      //     this.emitter.emit("csl_staging_pushed");
      //   })
      //   .catch(error => this.$root.error(error));
    },
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
    async getDmlEntries() {
      let filename = this.$route.params.filename ? this.$route.params.filename : this.filename;
      if(!filename){
        this.techpubStore.Errors = [{filename: 'You should type the DML filename.'}]
        return;
      }
      let response = await axios({
        route: {
          name: 'api.get_dmlentry',
          data: {filename: filename}
        }
      });
      if(response.statusText === 'OK'){
        this.dmlEntryList = response.data;
      }
    }
  },
  mounted() {
    this.emitter.on('api.decline_csl_forstaging', this.getCSL_forstaging);
    this.filename = this.$route.params.filename ? this.$route.params.filename : this.filename;
    this.getCSL_forstaging();
  }
}
</script>
<template>
  <div>
    <!-- draft CSL ready to stage -->
    <h2>Draft CSL before stage</h2>
    <table>
      <thead>
        <tr>
          <th>Filename</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="csl in cslEntryList">
          <td> {{ csl.filename }} </td>
          <td> 
            <button @click="pushToStaging(csl.filename)"><span class="material-icons text-green-400">check_circle</span>Push</button>
            <a class="text-black" :href="techpubStore.getWebRoute('',{filename: csl.filename}, Object.assign({},$router.getRoutes().find(r => r.name =='DetailDML')))['path']"><span class="material-icons">edit</span>Edit</a>
            <button @click="deleteDML(csl.filename)"><span class="material-icons text-red-500">delete</span>Delete</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- push -->
    <div class="w-1/2">
      <label for="dmlFilename" class="block mb-2 text-gray-900 dark:text-white text-lg font-bold">DML</label>
      <span>The DML is required to be filled.</span>
      <form @submit.prevent="getDmlEntries">
        <input type="text" id="dmlFilename" name="filename" v-model="filename"
          placeholder="type the DML file.."
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
        <div class="text-red-600" v-html="techpubStore.error('filename')"></div>
      </form>
    </div>
    <div class="mt-3">
      <h5>Choose Your Data Module <a @click="getDmlEntries()" class="font-normal underline hover:text-blue-500"
          href="#">click to open list</a></h5>
      <form @submit.prevent="createCSL()">
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
                <div v-if="entry.code.substr(0,3) == 'ICN'">
                  <input type="checkbox" name="objects[]" :value="entry.code + entry.extension" class="mx-1" />                  
                  {{ entry.code + entry.extension }}
                  <!-- 
                    * tambahkan IMF nya di sini atau tidak perlu jika IMF tidak dilakukan validasi terhadap DMRL.
                    * konsekuensi nya jika DMRL di select di sini,  artinya kan remark stage:staged. Nanti ketika generate IETM, akan kesulitan untuk mencari IMF nya karena cara mencari nya berdasarkan 
                   -->
                </div>
                <div v-else v-for="obj in entry.objects" class="flex">
                  <input type="checkbox" name="objects[]" :value="obj.filename" class="mx-1" />
                  {{ obj.filename }}
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <button class="button-violet mt-3">Create CSL</button>
      </form>
    </div>
  </div>
</template>