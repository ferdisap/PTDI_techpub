<script>
import axios from 'axios';
import { useTechpubStore } from '../../../techpub/techpubStore';

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      data: {},
    }
  },
  computed: {
    list() {
      return this.data.list ?? {};
    },
    filenameSearch() {
      return this.data.filenameSearch;
    },
    pagination() {
      return this.data.paginationInfo;
    },
    pageless() {
      return this.data.paginationInfo['prev_page_url'];
    },
    pagemore() {
      return this.data.paginationInfo['next_page_url'];
    },
  },
  methods: {
    async getObjs(data = {}) {
      data = Object.keys(data).forEach(key => data[key] === undefined && delete data[key]);
      let response = await axios({
        route: {
          name: 'api.get_deletion_list',
          data: data,
        }
      });
      if (response.statusText === 'OK') {
        this.storingResponse(response);
      }
    },
    storingResponse(response) {
      this.data.list = response.data.data;
      delete response.data.data;
      this.data.paginationInfo = response.data;
    },
    async goto(url, page) {
      if (page) {
        url = new URL(this.pagination['path']);
        url.searchParams.set('page', page)
      }
      if (url) {
        let response = await axios.get(url);
        if (response.statusText === 'OK') {
          this.storingResponse(response);
        }
      }
    },
    removeList(filename) {
      let index = this.data.list.indexOf(this.data.list.find(o => o.filename === filename));
      this.data.list.splice(index, 1);
    },
    async restore(filename) {
      let response = await axios({
        route: {
          name: 'api.restore_object',
          data: { filename: filename }
        },
      })
      if (response.statusText === 'OK') {
        this.removeList(filename);
        this.emitter.emit('RestoreCSDBobejctFromDeletion', response.data.data);
      }
    },
    async permanentDelete(filename) {
      if (!(await this.$root.alert({ name: 'beforePermanentDeleteCsdbObject', filename: filename }))) {
        return;
      }
      let response = await axios({
        route: {
          name: 'api.permanentdelete_object',
          data: { filename: filename }
        }
      });
      if (response.statusText === 'OK') {
        this.removeList(filename);
      }
    },
    async download(filename) {
      let response = await axios({
        route: {
          name: 'api.get_deletion_object',
          data: { filename: filename },
        },
        responseType: 'blob',
      });
      if (response.statusText === 'OK') {
        let typeblob = response.headers.getContentType();
        if (typeblob.includes('xml')) {
          // let raw = await response.data.text(); // tidak dipakai
          let srcblob = URL.createObjectURL(await response.data);

          let a = $('<a/>')
          a.attr('download', filename);
          a.attr('href', srcblob);
          a[0].click();
        }
      }
    }
  },
  mounted() {
    this.getObjs({ filenameSearch: this.filenameSearch });

    this.emitter.on('Deletion-refresh', (data) => {
      // data adalah Deletion Object
      this.data.list.push(data);
    })
  },
}
</script>
<template>
  <div class="deletion overflow-auto h-full">

    <div class="bg-white px-3 py-3 2xl:h-[92%] xl:h-[90%] lg:h-[88%] md:h-[90%] sm:h-[90%] h-full">

      <div class="2xl:h-[5%] xl:h-[6%] lg:h-[8%] md:h-[9%] sm:h-[11%]">
        <h1 class="text-blue-500">DELETION</h1>
        <hr class="border-2 border-blue-500" />
      </div>

      <div class="2xl:h-[95%] xl:h-[94%] lg:h-[92%] md:h-[91%] sm:h-[89%]">

        <div class="flex max-h-[10%]">
          <input @change="get_list()" placeholder="find filename" type="text"
            class="w-48 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
          <button class="material-icons mx-3 text-gray-500 text-sm has-tooltip-arrow" data-tooltip="info"
            @click="$root.info({ name: 'searchCsdbObject' })">info</button>
        </div>

        <div class="flex justify-start flex-col p-5 max-h-[80%] text-left">
          <table class="w-full">
            <thead class="h-10 border-b-4 border-black">
              <tr>
                <th class="w-[50%]">Filename</th>
                <th class="w-[30%]">Date</th>
                <th class="w-[20%]">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="object in list">
                <td><a href="#" @click="filenameAnalysis = object.filename">{{ object.filename }}</a></td>
                <td>{{ techpubStore.date(object.created_at) }}</td>
                <td>
                  <button @click="restore(object.filename)" class="material-icons text-green-700 has-tooltip-arrow"
                    data-tooltip="Restore">restore_from_trash</button>
                  <button @click="permanentDelete(object.filename)" class="material-icons text-red-700 has-tooltip-arrow"
                    data-tooltip="Permanent Delete">delete_forever</button>
                  <button @click="download(object.filename)" class="material-icons text-black has-tooltip-arrow"
                    data-tooltip="Download">download</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="max-h-[10%] w-full mt-3">
          <div v-if="pagination" class="flex justify-center items-center mt-2 h-[5%]">
            <button @click="goto(pageless)" class="material-symbols-outlined text-sm">navigate_before</button>
            <form @submit.prevent="goto('', pagination['current_page'])" class="flex">
              <input v-model="pagination['current_page']" class="w-6 border-none text-sm text-center bg-transparent" />
              <span class="text-sm"> of {{ pagination['last_page'] }} </span>
            </form>
            <button @click="goto(pagemore)" class="material-symbols-outlined text-sm">navigate_next</button>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>