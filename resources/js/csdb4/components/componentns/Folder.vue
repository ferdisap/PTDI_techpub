<script>
import { sorter } from "../../../helper.js";
import { useTechpubStore } from "../../../techpub/techpubStore";
import Sort from "../../../techpub/components/Sort.vue";
import ContinuousLoadingCircle from "../../loadingProgress/ContinuousLoadingCircle.vue";
import PreviewRCMenu from '../../rightClickMenuComponents/PreviewRCMenu.vue';

export default {
  components:{ Sort, ContinuousLoadingCircle, PreviewRCMenu },
  data() {
    return {
      techpubStore: useTechpubStore(),
      path: '',
      data: {},
      // objects: [],
      // model: {},
      open: {},

      type: '', // kayaknya ga perlu
      showLoadingProgress: false,

      selectionMode: false,
      isShowRcMenu: false,
      checkboxId: '',
      isSelectAll: false,
    }
  },
  props: {
    dataProps: {
      type: Object,
      default: {},
    },
    routeName: {
      type: String
    }
  },
  computed: {
    setObject() {
      if (this.$props.dataProps.path) {
        this.getObjs({path: this.$props.dataProps.path});
      }
    },
    models() {
      return this.data.models;
    },
    folders() {
      return this.data.folders;
    },
    sc(){
      return this.data.sc;  
    },
    currentPath() {
      // return this.$props.dataProps.path ? this.$props.dataProps.path.slice(0, -1) : 
      // (this.data.current_path ? this.data.current_path.slice(0,-1) : '');
      // return this.$props.dataProps.path.slice(0, -1);
      return this.data.current_path ? this.data.current_path : '';
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
    async getObjs(data = {}){
      this.showLoadingProgress = true;
      if(!data.sc) data.sc = '';
      if(data.path){
        if(data.sc.search(/path::\S+\s/) < 0) data.sc += " path::" + data.path; // nambah ' path::...' ke dalam sc
        else data.sc = data.sc.replace(/path::\S+\s/, "path::" + data.path + " "); // mengganti 'path::... ' dengan path data.path
        delete data.path;
      }
      switch (this.$props.routeName) {
        case 'Explorer':
          data.sc += " typeonly::DMC,PMC,ICN";
          break;      
        case 'ManagementData':
          data.sc += " typeonly::DML,COM";
          break;      
        default:
          break;
      }
      let response = await axios({
        route: {
          name: 'api.requestbyfolder.get_allobject_list',
          data: data// akan receive data: [model1, model2, ...]
        },
        useMainLoadingBar: false,
      });
      this.storingResponse(response);
      this.showLoadingProgress = false;
    },
    storingResponse(response) {
      if (response.statusText === 'OK') {
        this.data.models = response.data.data; // array contain object model
        this.data.folders = response.data.folder; // array contain string path
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
        if(response.statusText === 'OK'){
          this.storingResponse(response);
        }
      }
    },
    async back(path = undefined) {
      if(!this.selectionMode){
        if(!path){
          path = this.currentPath.replace(/\/\w+\/?$/, "");
        }
        this.getObjs({path: path});
        this.data.current_path = path;
      }
    },
    clickFolder(event, path){
      if(!this.selectionMode) this.back(path);
      else {
        switch (event.target.parentElement.tagName) {
          case 'TR':
            event.target.parentElement.firstElementChild.firstElementChild.checked = true;
            break;
          case 'TD':
            event.target.parentElement.previousElementSibling.firstElementChild. checked = true;
            break;
        }
      }
    },
    clickFilename(event, data){
      if(!this.selectionMode){
        this.techpubStore.currentObjectModel = data; // sementara karena data hanya ada filename dan path
        this.emitter.emit('RequireCSDBObjectModel', data); // masih perlu minta karena data tidak valid
        this.$root.gotoExplorer(data.filename);
        this.emitter.emit('clickFilenameFromFolder', data) // key path dan filename
      } else {
        // bisa juga ini diganti dengan fungsi select() dibawah, tapi harus mengirimkan idnya
        // dugaan saya code switch ini lebih cepat dari pada selector
        switch (event.target.parentElement.tagName) {
          case 'TR':
            event.target.parentElement.firstElementChild.firstElementChild.checked = true;
            break;
          case 'TD':
            event.target.parentElement.previousElementSibling.firstElementChild. checked = true;
            break;
        }        
      }
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
    search(){
      this.getObjs({sc: this.sc});
    },
    select(cbid){
      this.isSelectAll = false;
      this.selectionMode = true; 
      this.isShowRcMenu = false;
      setTimeout(()=>document.getElementById(cbid).checked = true,0);
    },
    selectAll(isSelect = true){
      this.selectionMode = true; 
      this.isShowRcMenu = false;
      this.isSelectAll = isSelect;
      setTimeout(() =>this.$el.querySelectorAll(".folder input[type='checkbox']").forEach((input) => input.checked = isSelect), 0);
    },
    selectAllFiles(isSelect = true){
      this.selectionMode = true; 
      this.isShowRcMenu = false;
      this.isSelectAll = isSelect;
      setTimeout(() =>this.$el.querySelectorAll(".folder input[file='true']").forEach((input) => input.checked = isSelect), 0);
    },
    async dispatch(){
      let models = [];
      let paths = [];
      let o = undefined;
      this.$el.querySelectorAll('.folder input[type="checkbox"]:checked').forEach((input) => {
        if(o = this.data.folders.find((path) => path === input.value)){
          paths.push(o);
        } 
        else if(o = this.data.models.find((obj) => obj.filename === input.value)){
          models.push(o);
        }
      });
      if(paths.length !== 0){
        let sc = 'path::';
        paths.forEach(p => sc += p);
        switch (this.$props.routeName) {
          case 'Explorer':
            sc += " typeonly::DMC,PMC,ICN";
            break;      
          case 'ManagementData':
            sc += " typeonly::DML,COM";
            break;      
          default:
            break;
        }
        let response = await axios({
          route: {
            name: 'api.requestbyfolder.get_allobject_list',
            data: {sc:sc}
          },
          useMainLoadingBar: false,
        });
        if(response.statusText === 'OK'){
          models = models.concat(response.data.data);
        }
      }
      console.log(window.models = models, window.paths = paths);
      this.emitter.emit('dispatchTo', models);
      // this.emitter.emit('dispatchTo',{})
    }
  },
  mounted(){
    this.emitter.on('Folder-refresh', (data) => {
      if(data.path === this.data.current_path){
        this.getObjs({path: data.current_path})
      }
    });
    if(this.$route.params.filename){
      // this.getObjs({filename: this.$route.params.filename});
      this.getObjs({path: this.techpubStore.currentObjectModel.path});
      this.data.current_path = this.techpubStore.currentObjectModel.path;
    } else {
      this.getObjs({path: 'csdb'});
      this.data.current_path = 'csdb';
    }
  },
}
</script>
<style>
.folder th, .folder td {
  white-space: nowrap;
}
</style>
<template>
  <div class="folder h-[100%] bg-white overflow-hidden">
    <div v-show="false">{{ setObject }}</div>
    <div class="folder h-[100%] w-full relative">
      <div class="h-[50px] relative text-center">
        <div class="absolute top-0">
          <button @click="back()" class="material-symbols-outlined has-tooltip-right" data-tooltip="back">keyboard_backspace</button>
        </div>
        <h1 class="relative inline">Folder:/{{ currentPath }}</h1>
      </div>
  
      <div class="text-center mb-3">
        <input @change="search()" v-model="this.data.sc" placeholder="find filename" type="text" class="w-48 inline bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        <button class="material-icons mx-3 text-gray-500 text-sm has-tooltip-arrow inline" data-tooltip="info" @click="$root.info({ name: 'searchCsdbObject' })">info</button>
      </div>

      <div class="h-[75%] block relative overflow-scroll">
        <table class="table">
          <thead class="text-sm">
            <tr class="leading-3 text-sm">
              <th v-if="selectionMode"></th>
              <th class="text-sm">Name <Sort :function="sortTable"></Sort></th>
              <th class="text-sm">Path <Sort :function="sortTable"></Sort></th>
              <th class="text-sm">Created At <Sort :function="sortTable"></Sort></th>
              <th class="text-sm">Updated At <Sort :function="sortTable"></Sort></th>
              <th class="text-sm">Initiator <Sort :function="sortTable"></Sort></th>
              <th class="text-sm">Editable <Sort :function="sortTable"></Sort></th>
            </tr>
          </thead>
          <tbody>
            <tr @contextmenu.prevent="isShowRcMenu = true" @mouseover="checkboxId = 'cb'+path" v-for="path in folders" class="folder-row text-sm hover:bg-blue-300">
              <td v-if="selectionMode" class="flex">
                <input file="false" :id="'cb'+path" type="checkbox" :value="path">
              </td>
              <td class="leading-3 text-sm" colspan="6" @click="clickFolder($event, path)">
                <span class="material-symbols-outlined text-sm mr-1">folder</span>
                <span class="text-sm">{{ path.split("/").at(-1) }} </span> 
                <!-- min -2 karena diujung folder ada '/' -->
              </td>
            </tr>
            <tr @contextmenu.prevent="isShowRcMenu = true" @mouseover="checkboxId = 'cb'+obj.filename" v-for="obj in models" class="file-row text-sm hover:bg-blue-300">
              <td v-if="selectionMode" class="flex">
                <input file="true" :id="'cb'+obj.filename" type="checkbox" :value="obj.filename">
              </td>
              <td class="leading-3 text-sm" @click="clickFilename($event, {filename: obj.filename, path: obj.path})">
                <span class="material-symbols-outlined text-sm mr-1">description</span>
                <span class="text-sm"> {{ obj.filename }} </span>
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
      <div class="w-full text-black absolute bottom-[30px] h-[30px] px-3 flex justify-center">
        <div v-if="pagination" class="flex justify-center items-center text-lg bg-gray-100 rounded-lg px-2 w-[300px]">
          <button @click="goto(pageless)" class="material-symbols-outlined">navigate_before</button>
          <form @submit.prevent="goto('', pagination['current_page'])" class="flex">
            <input v-model="pagination['current_page']" class="w-6 border-none text-center bg-transparent font-bold" />
            <span class="font-bold"> of {{ pagination['last_page'] }} </span>
          </form>
          <button @click="goto(pagemore)" class="material-symbols-outlined">navigate_next</button>
        </div>
      </div>  
    </div>
  
    <ContinuousLoadingCircle :show="showLoadingProgress"/>
    <PreviewRCMenu v-if="isShowRcMenu">
      <div @click="select(checkboxId)" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Select</div>
      </div>
      <div @click="selectAll(!isSelectAll)" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">{{ isSelectAll ? 'Deselect' : 'Select' }} All</div>
      </div>
      <div @click="selectAllFiles(!isSelectAll)" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">{{ isSelectAll ? 'Deselect' : 'Select' }} All Files</div>
      </div>
      <hr class="border border-gray-300 block mt-1 my-1 border-solid"/>
      <div @click="dispatch" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Dispatch</div>
      </div>
      <hr class="border border-gray-300 block mt-1 my-1 border-solid"/>
      <div @click.prevent="()=>{selectAll(false); selectionMode = false}" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Cancel</div>
      </div>
    </PreviewRCMenu>
  </div>
</template>