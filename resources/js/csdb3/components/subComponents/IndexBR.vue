<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
import Editor from './Editor.vue';
import IndexCSDB from './IndexCSDB.vue'
export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      // showBREX: '',
      brex_list: undefined, // {}
      brex_page: 1,
      brdp_list: undefined, // {}
      brdp_page: 1,
    }
  },
  components: { IndexCSDB, Editor },
  methods: {
    // /**
    //  * @param {String} type is 'brex' or 'brdp'
    // */
    // async get_list(type, params = {}){
    //   params.filenameSearch = event.target.value || '';
    //   let response = await axios({
    //     route: {
    //       name: `api.get_${type}_list`,
    //       data: params
    //     }
    //   })
    //   if(response.statusText === 'OK'){
    //     this[`${type}_list`] = response.data;
    //   }
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
    // },
  },
  mounted() {
    // this.techpubStore.get_list('brex');
    // this.techpubStore.get_list('brdp');

    // pasang listener di sini
  },
}
</script>
<template>
  <!-- BREX -->
  <IndexCSDB type="brex">
    <template #title>Index BREX</template>
    <template #actionColumn="actionColumnProps">
      <a class="material-icons text-blue-600 has-tooltip-arrow" data-tooltip="Detail"
        :href="techpubStore.getWebRoute('', { filename: actionColumnProps.filename }, Object.assign({}, $router.getRoutes().find((route) => route.name == 'DetailObject'))).path">details</a>
      <button @click="deleteObject(actionColumnProps.filename)" class="material-icons text-red-500 has-tooltip-arrow"
        data-tooltip="Delete">delete</button>
    </template>
  </IndexCSDB>

  <!-- BRDP -->
  <IndexCSDB type="brdp">
    <template #title>Index BRDP</template>
    <template #actionColumn="actionColumnProps">
      <a class="material-icons text-blue-600 has-tooltip-arrow" data-tooltip="Detail"
        :href="techpubStore.getWebRoute('', { filename: actionColumnProps.filename }, Object.assign({}, $router.getRoutes().find((route) => route.name == 'DetailObject'))).path">details</a>
      <button @click="deleteObject(actionColumnProps.filename)" class="material-icons text-red-500 has-tooltip-arrow"
        data-tooltip="Delete">delete</button>
    </template>
  </IndexCSDB>

  <!-- <div class="IndexBREX" v-if="techpubStore.brex_list">
    <h1>Index BREX</h1>
    <div class="flex">
      <input @change="techpubStore.get_list('brex')" v-model="techpubStore.brex_filenameSearch" placeholder="find filename" type="text" class="w-48 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
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
          <tr v-for="brex in techpubStore.brex_list.data">
            <td><a href="#" @click="filenameAnalysis = brex.filename">{{ brex.filename }}</a></td>
            <td>{{ brex.editable ? 'yes' : 'no' }} </td>
            <td>{{ brex.initiator.name == techpubStore.Auth.name ? 'self' : brex.initiator.name }} </td>
            <td :class="[brex.remarks.stage == 'staged' ? 'bg-green-500' : 'bg-yellow-500']">{{ brex.remarks.stage }}</td>
            <td>
              <a class="material-icons text-blue-600 has-tooltip-arrow" data-tooltip="Detail"
                :href="techpubStore.getWebRoute('', { filename: brex.filename }, Object.assign({}, $router.getRoutes().find((route) => route.name == 'DetailObject'))).path">details</a>
              <button @click="deleteBREX(brex.filename)" class="material-icons text-red-500 has-tooltip-arrow"
                data-tooltip="Delete">delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="flex justify-center items-center">
      <button @click="techpubStore.goto('brex',techpubStore.brex_list.current_page > 0 ? techpubStore.brex_list.current_page - 1 : 1)"
        class="material-symbols-outlined">navigate_before</button>
      <form @submit.prevent="techpubStore.goto('brex', techpubStore.brex_page)" class="inline-block">
        <input v-model="techpubStore.brex_page" class="w-6" />
        <span> of {{ techpubStore.brex_list.last_page }} </span>
      </form>
      <button
        @click="techpubStore.goto('brex',techpubStore.brex_list.current_page < techpubStore.brex_list.last_page ? techpubStore.brex_list.current_page + 1 : techpubStore.brex_list.last_page)"
        class="material-symbols-outlined">navigate_next</button>
    </div>
  </div>

  <div class="IndexBRDP" v-if="techpubStore.brdp_list">
    <h1>Index BRDP</h1>
    <div class="flex">
      <input @change="techpubStore.get_list('brdp')" techpubStore.brex_filenameSearch placeholder="find filename" type="text" class="w-48 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
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
          <tr v-for="brdp in techpubStore.brdp_list.data">
            <td><a href="#" @click="filenameAnalysis = brdp.filename">{{ brdp.filename }}</a></td>
            <td>{{ brdp.editable ? 'yes' : 'no' }} </td>
            <td>{{ brdp.initiator.name == techpubStore.Auth.name ? 'self' : brdp.initiator.name }} </td>
            <td :class="[brdp.remarks.stage == 'staged' ? 'bg-green-500' : 'bg-yellow-500']">{{ brdp.remarks.stage }}</td>
            <td>
              <a class="material-icons text-blue-600 has-tooltip-arrow" data-tooltip="Detail"
                :href="techpubStore.getWebRoute('', { filename: brdp.filename }, Object.assign({}, $router.getRoutes().find((route) => route.name == 'DetailObject'))).path">details</a>
              <button @click="deletebrdp(brdp.filename)" class="material-icons text-red-500 has-tooltip-arrow"
                data-tooltip="Delete">delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="flex justify-center items-center">
      <button @click="techpubStore.goto('brdp',techpubStore.brdp_list.current_page > 0 ? techpubStore.brdp_list.current_page - 1 : 1)"
        class="material-symbols-outlined">navigate_before</button>
      <form @submit.prevent="techpubStore.goto('brdp', techpubStore.brdp_page)" class="inline-block">
        <input v-model="techpubStore.brdp_page" class="w-6" />
        <span> of {{ techpubStore.brdp_list.last_page }} </span>
      </form>
      <button
        @click="techpubStore.goto('brdp',techpubStore.brdp_list.current_page < techpubStore.brdp_list.last_page ? techpubStore.brdp_list.current_page + 1 : techpubStore.brdp_list.last_page)"
        class="material-symbols-outlined">navigate_next</button>
    </div>
  </div> -->
</template>