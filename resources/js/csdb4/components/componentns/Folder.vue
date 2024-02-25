<script>
import { sorter } from "../../../helper.js";
import { useTechpubStore } from "../../../techpub/techpubStore";
import Sort from "../../../techpub/components/Sort.vue";
export default {
  components:{ Sort },
  data() {
    return {
      techpubStore: useTechpubStore(),
      path: '',
      data: {},
      // objects: [],
      // model: {},
      open: {},

      type: '', // kayaknya ga perlu
    }
  },
  props: {
    dataProps: {
      type: Object,
      default: {},
    },
  },
  computed: {
    async setObject() {
      if (this.$props.dataProps.path) {
        let response = await axios({
          route: {
            name: 'api.requestbyfolder.get_allobject_list',
            data: {
              path: this.$props.dataProps.path,
            } // akan receive data: [model1, model2, ...]
          }
        });
        this.storingResponse(response);
      }
    },
    models() {
      return this.data.models;
    },
    folders() {
      return this.data.folders;
    },
    filenameSearch(){
      return this.data.filenameSearch;  
    },
    currentPath() {
      // return this.$props.dataProps.path ? this.$props.dataProps.path.slice(0, -1) : 
      // (this.data.current_path ? this.data.current_path.slice(0,-1) : '');
      // return this.$props.dataProps.path.slice(0, -1);
      return this.data.current_path ? this.data.current_path.slice(0,-1) : '';
    },
    pagination() {
      return this.data.paginationInfo;
    },
    pageless() {
      return this.data.paginationInfo['prev_page_url'];
      // return this.data.paginationInfo['current_page'] > 1 ? this.data.paginationInfo['current_page'] - 1 : 1;
    },
    pagemore() {
      return this.data.paginationInfo['next_page_url'];
      // return (this.data.paginationInfo['current_page'] < this.data.paginationInfo['last_page']) ? this.data.paginationInfo['current_page'] + 1 : this.data.paginationInfo['last_page']
    },
  },
  methods: {
    storingResponse(response) {
      if (response.statusText === 'OK') {
        this.data.models = response.data.data;
        this.data.folders = response.data.folder;
        this.data.current_path = response.data.current_path ?? this.$props.dataProps.path;
        delete response.data.data;
        delete response.data.folder;
        delete response.data.message;
        delete response.data.current_path;
        this.data.paginationInfo = response.data;
      }
    },
    async goto(url, page) {
      if (page) {
        url = new URL(this.pagination['path']);
        url.searchParams.set('page', page)
      }
      if (url) {
        let response = await axios.get(url);
        this.storingResponse(response);
      }
    },
    async back(path = undefined) {
      if(!path){
        path = this.currentPath.replace(/\w+\/?$/, "");
      }
      let response = await axios({
        route: {
          name: 'api.requestbyfolder.get_allobject_list',
          data: {
            path: path,
          } // akan receive data: [model1, model2, ...]
        }
      });
      this.storingResponse(response);
      this.data.current_path = path;
    },
    clickFolder(path){
      if(path){
        this.back(path);
      }
    },
    clickFile(){
      // run the event
    },
    sortTable(){
      const getCellValue = function (row, index) {
        return $(row).children('td').eq(index).text();
      };
      const comparer = function (index) {
        return function (a, b) {
          let valA = getCellValue(a, index), valB = getCellValue(b, index);
          return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB);
        }
      };
      let table = $(event.target).parents('table').eq(0);
      let th = $(event.target).parents('th').eq(0);
      if(th.index() === 0){
        let filerows = table.find('.file-row').toArray().sort(comparer(th.index()));
        let folderrows = table.find('.folder-row').toArray().sort(comparer(th.index()));
        this.asc = !this.asc;
        if (!this.asc) {
          filerows = filerows.reverse();
          folderrows = folderrows.reverse();
        }
        for (let i = 0; i < folderrows.length; i++) {
          table.append(folderrows[i]);
        }
        for (let i = 0; i < filerows.length; i++) {
          table.append(filerows[i]);
        }
      } else {
        let filerows = table.find('.file-row').toArray().sort(comparer(th.index()));
        this.asc = !this.asc;
        if (!this.asc) {
          filerows = filerows.reverse();
        }
        for (let i = 0; i < filerows.length; i++) {
          table.append(filerows[i]);
        }
      }
    },
    async search(){
      let response = await axios({
        route: {
          name: 'api.requestbyfolder.get_allobject_list',
          data: {
            filenameSearch: this.filenameSearch,
            path: this.currentPath + "/",
          } // akan receive data: [model1, model2, ...]
        }
      });
      this.storingResponse(response);
    },

  },
}
</script>
<template>
  <div v-show="false">{{ setObject }}</div>
  <div class="folder overflow-auto h-[93%] w-full">
    <div class="h-[10%] flex mb-3">
      <div class="w-8">
        <button @click="back()" class="material-symbols-outlined">keyboard_backspace</button>
      </div>
      <h1 class="text-blue-500 w-full text-center">Folder:/{{ currentPath }}</h1>
    </div>

    <div class="flex justify-center mb-3">
      <input @change="search()" v-model="this.data.filenameSearch" placeholder="find filename" type="text" class="w-48 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
      <button class="material-icons mx-3 text-gray-500 text-sm has-tooltip-arrow" data-tooltip="info" @click="$root.info({ name: 'searchCsdbObject' })">info</button>
    </div>

    <table class="text-left">
      <thead class="text-sm">
        <tr class="leading-3 text-sm">
          <th class="text-sm">Name <Sort :function="sortTable"></Sort></th>
          <th class="text-sm">Path <Sort :function="sortTable"></Sort></th>
          <th class="text-sm">Created At <Sort :function="sortTable"></Sort></th>
          <th class="text-sm">Updated At <Sort :function="sortTable"></Sort></th>
          <th class="text-sm">Initiator <Sort :function="sortTable"></Sort></th>
          <th class="text-sm">Editable <Sort :function="sortTable"></Sort></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="obj in folders" class="folder-row text-sm hover:bg-blue-500 hover:text-white">
          <td class="leading-3 text-sm" colspan="5">
            <span class="material-symbols-outlined text-sm mr-1">folder</span>
            <a href="#" @click.prevent="back(obj.path)" class="text-sm">{{ obj.path.split("/").at(-2) }} </a> 
            <!-- min -2 karena diujung folder ada '/' -->
          </td>
        </tr>
        <tr v-for="obj in models" class="file-row text-sm hover:bg-blue-500 hover:text-white">
          <td class="leading-3 text-sm">
            <span class="material-symbols-outlined text-sm mr-1">description</span>
            <a href="#" class="text-sm"> {{ obj.filename }} </a>
          </td>
          <td class="leading-3 text-sm"> {{ obj.path }} </td>
          <td class="leading-3 text-sm"> {{ techpubStore.date(obj.created_at) }} </td>
          <td class="leading-3 text-sm"> {{ techpubStore.date(obj.updated_at) }} </td>
          <td class="leading-3 text-sm"> {{ obj.initiator.name }} </td>
          <td class="leading-3 text-sm"> {{ obj.editable ? 'yes' : 'no' }} </td>
        </tr>        
      </tbody>
    </table>


  </div>

  <!-- pagination -->
  <div class="h-[6%] w-full border">
    <div v-if="pagination" class="flex justify-center items-center mt-2 h-[5%]">
      <button @click="goto(pageless)" class="material-symbols-outlined text-sm">navigate_before</button>
      <form @submit.prevent="goto('', pagination['current_page'])" class="flex">
        <input v-model="pagination['current_page']" class="w-6 border-none text-sm text-center bg-transparent" />
        <span class="text-sm"> of {{ pagination['last_page'] }} </span>
      </form>
      <button @click="goto(pagemore)" class="material-symbols-outlined text-sm">navigate_next</button>
    </div>
  </div>
</template>