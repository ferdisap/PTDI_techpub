<script>
import { sorter } from "../../../helper.js";
import { useTechpubStore } from "../../../techpub/techpubStore";
import Sort from "../../../techpub/components/Sort.vue";
import ContinuousLoadingCircle from "../../loadingProgress/ContinuousLoadingCircle.vue";
import RCMenu from "../../rightClickMenuComponents/RCMenu.vue";
import {CsdbObjectCheckboxSelector} from "../../CheckboxSelector";
import {getObjs, storingResponse, goto, back, clickFolder, clickFilename, 
  sortTable, search, removeList, dispatch, select, changePath, deleteObject, refresh} from './FolderVue'

export default {
  components:{ Sort, ContinuousLoadingCircle, RCMenu },
  data() {
    return {
      techpubStore: useTechpubStore(),
      path: '',
      data: {},
      open: {},
      
      type: '', // kayaknya ga perlu
      showLoadingProgress: false,
      
      CbSelector: new CsdbObjectCheckboxSelector(),

      // selection view (becasuse clicked by user)
      selectedRow: undefined,
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
      return this.data.csdb;
    },
    folders() {
      return this.data.folders;
    },
    sc(){
      return this.data.sc;  
    },
    currentPath() {
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
    getObjs: getObjs,
    storingResponse: storingResponse,
    goto: goto,
    back: back,
    clickFolder: clickFolder,
    clickFilename: clickFilename,
    sortTable: sortTable,
    search: search,
    removeList: removeList,
    dispatch: dispatch,
    select: select,
    changePath: changePath,
    deleteObject: deleteObject,
    
    // emit
    refresh: refresh,
  },
  mounted(){
    window.cb = this.CbSelector;
    
    // dari Listtree via Explorer/Management data data berisi path doang,
    let emitters =  this.emitter.all.get('Folder-refresh'); // 'emitter.length < 2' artinya emitter max. hanya dua kali di instance atau baru sekali di emit, check ManagementData.vue
    if(emitters){
      let indexEmitter = emitters.indexOf(emitters.find((v) => v.name === 'bound refresh')) // 'bound addObjects' adalah fungsi, lihat scrit dibawah ini. Jika fungsi anonymous, maka output = ''
      if(emitters.length < 1 && indexEmitter < 0) this.emitter.on('Folder-refresh', this.refresh); 
    }

    if(this.$route.params.filename && this.techpubStore.currentObjectModel.csdb){
      this.getObjs({path: this.techpubStore.currentObjectModel.csdb.path});
      this.data.current_path = this.techpubStore.currentObjectModel.csdb.path;
    } else {
      this.getObjs({path: 'CSDB'});
      this.data.current_path = 'CSDB';
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
  <div class="folder h-[100%] bg-white overflow-hidden"  @contextmenu.prevent="CbSelector.isShowTriggerPanel = true">
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
        <table class="table" :id="CbSelector.id">
          <thead class="text-sm text-left">
            <tr class="leading-3 text-sm">
              <th v-show="CbSelector.selectionMode"></th>
              <th class="text-sm">Name <Sort :function="sortTable"></Sort></th>
              <th class="text-sm">Path <Sort :function="sortTable"></Sort></th>
              <th class="text-sm">Created At <Sort :function="sortTable"></Sort></th>
              <th class="text-sm">Updated At <Sort :function="sortTable"></Sort></th>
              <th class="text-sm">Last History <Sort :function="sortTable"></Sort></th>
            </tr>
          </thead>
          <tbody>
            <tr @click.stop.prevent="select($event)" @dblclick.prevent="clickFolder($event, path)" @mousemove="CbSelector.setCbHovered('cb'+path)" v-for="path in folders" class="folder-row text-sm hover:bg-blue-300 cursor-pointer">
              <td v-show="CbSelector.selectionMode" class="flex" @click.stop.prevent>
                <input file="false" :id="'cb'+path" type="checkbox" :value="path">
              </td>
              <td class="leading-3 text-sm" colspan="6">
                <span class="material-symbols-outlined text-sm mr-1">folder</span>
                <span class="text-sm">{{ path.split("/").at(-1) }} </span> 
                <!-- min -2 karena diujung folder ada '/' -->
              </td>
            </tr>
            <tr @click.stop.prevent="select($event)" @dblclick.prevent="clickFilename($event, obj.filename)" @mousemove="CbSelector.setCbHovered('cb'+obj.filename)" v-for="obj in models" class="file-row text-sm hover:bg-blue-300 cursor-pointer">
              <td v-show="CbSelector.selectionMode" class="flex">
                <input file="true" :id="'cb'+obj.filename" type="checkbox" :value="obj.filename">
              </td>
              <td class="leading-3 text-sm">
                <span class="material-symbols-outlined text-sm mr-1">description</span>
                <span class="text-sm"> {{ obj.filename }} </span>
              </td>
              <td class="leading-3 text-sm"> {{ obj.path }} </td>
              <td class="leading-3 text-sm"> {{ techpubStore.date(obj.created_at) }} </td>
              <td class="leading-3 text-sm"> {{ techpubStore.date(obj.updated_at) }} </td>
              <td class="leading-3 text-sm"> {{ (obj.last_history.description) }}, {{ techpubStore.date(obj.last_history.created_at) }} </td>
            </tr>        
          </tbody>
        </table>
      </div>
            
      <!-- pagination -->
      <div class="w-full text-black absolute bottom-[10px] h-[30px] px-3 flex justify-center">
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
    <RCMenu v-if="CbSelector.isShowTriggerPanel">
      <div @click="CbSelector.select()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Select</div>
      </div>
      <div @click="CbSelector.selectAll(!CbSelector.isSelectAll)" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">{{ CbSelector.isSelectAll ? 'Deselect' : 'Select' }} All</div>
      </div>
      <div @click="CbSelector.selectAll(!CbSelector.isSelectAll, `.folder input[file='true']`)" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">{{ CbSelector.isSelectAll ? 'Deselect' : 'Select' }} All Files</div>
      </div>
      <hr class="border border-gray-300 block mt-1 my-1 border-solid"/>
      <div @click="dispatch(1)" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Add Dispatch</div>
      </div>
      <div @click="dispatch(2)" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Remove Dispatch</div>
      </div>
      <div @click="dispatch(0)" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Dispatch</div>
      </div>
      <hr class="border border-gray-300 block mt-1 my-1 border-solid"/>
      <div @click="CbSelector.copy()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Copy</div>
      </div>
      <div class="flex flex-col hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <form class="text-sm" @submit.prevent="changePath($event)">
          <label class="text-sm">Move&#160;</label>
          <input type="text" class="w-[65%] rounded-sm h-0" name="path" @keydown.enter.prevent/>
          <button class="material-icons text-sm ml-2 hover:bg-blue-300 hover:border rounded-full px-1">send</button>
        </form>
      </div>
      <div @click="deleteObject()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Delete</div>
      </div>
      <hr class="border border-gray-300 block mt-1 my-1 border-solid"/>
      <div @click.prevent="CbSelector.cancel()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Cancel</div>
      </div>
    </RCMenu>
  </div>
</template>