<script>
import { sorter } from "../../../helper.js";
import { useTechpubStore } from "../../../techpub/techpubStore";
import Sort from "../../../techpub/components/Sort.vue";
import ContinuousLoadingCircle from "../../loadingProgress/continuousLoadingCircle.vue";
import RCMenu from "../../rightClickMenuComponents/RCMenu.vue";
import {CsdbObjectCheckboxSelector} from "../../CheckboxSelector";
import { isProxy, toRaw } from "vue";

function findAncestor (el, sel) {
    while ((el = el.parentElement) && !((el.matches || el.matchesSelector).call(el,sel)));
    return el;
}

export default {
  components:{ Sort, ContinuousLoadingCircle, RCMenu },
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

      // selectionMode: false,
      // isShowRcMenu: false,
      // checkboxId: '',
      // isSelectAll: false,
      CbSelector: new CsdbObjectCheckboxSelector(this),

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
          data.sc += " typeonly::DML";
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
        this.data.csdb = response.data.csdb; // array contain object model
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
      if(!this.CbSelector.selectionMode) this.back(path);
      else {
        this.CbSelector.select();
      }
    },
    clickFilename(event, filename){
      if(!this.CbSelector.selectionMode){
        this.techpubStore.currentObjectModel = Object.values(this.data.csdb).find((obj) => obj.filename === filename);
        this.$root.gotoExplorer(filename, 'pdf');
        this.emitter.emit('clickFilenameFromFolder', {filename: filename}) // key filename saja karena bisa diambil dari techpubstore atau server jika perlu
      } else {
        this.CbSelector.select();
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
    // sepertinya ini bisa dipindah ke CsdbObjectCheckboxSelector class
    async dispatch(){
      let models = []; // berisi string filename
      let paths = []; // berisii string path
      let o = undefined;
      if(this.CbSelector.selectionMode){
        this.CbSelector.getAllSelectionElement(true, `.folder input[type="checkbox"]:checked`).forEach((input)=>{
          if(o = this.data.folders.find((path) => path === input.value)){
            paths.push(o);
          } 
          else if(o = this.data.csdb.find((obj) => obj.filename === input.value)){
            models.push(o.filename);
          }
        })
      } else {
        let cbid = this.CbSelector.cbHovered;
        await new Promise(res => {
          let value = (document.getElementById(cbid).value);
          Object.values(this.data.csdb).forEach((obj) => {
            if(obj.filename === value){
              models.push(obj.filename);
              res(true);
            }
          });
        });
        // let cbid = this.CbSelector.cbHovered;
        // this.CbSelector.selectionMode = true;
        // await new Promise(res => 
        // setTimeout(()=>{
        //   let value = (document.getElementById(cbid).value);
        //   this.CbSelector.selectionMode = false;
        //   Object.values(this.data.csdb).forEach((obj) => {
        //     if(obj.filename === value){
        //       models.push(obj.filename);
        //       res(true);
        //     }
        //   })
        // },0));
      }
      // if(paths.length !== 0){
      //   let sc = 'path::';
      //   paths.forEach(p => sc += p);
      //   switch (this.$props.routeName) {
      //     case 'Explorer':
      //       sc += " typeonly::DMC,PMC,ICN";
      //       break;      
      //     case 'ManagementData':
      //       sc += " typeonly::DML";
      //       break;      
      //     default:
      //       break;
      //   }
      //   let response = await axios({
      //     route: {
      //       name: 'api.requestbyfolder.get_allobject_list',
      //       data: {sc:sc}
      //     },
      //     useMainLoadingBar: false,
      //   });
      //   if(response.statusText === 'OK'){
      //     models = models.concat(response.data.data);
      //   }
      // }
      this.emitter.emit('dispatchTo', {filenames: models, paths: paths});
      this.CbSelector.isShowTriggerPanel = false;      
    },
    /**
     * hanya untuk membirukan background table row
    */
    select(event){
      let el;
      if(this.CbSelector.selectionMode) {
        this.CbSelector.select();
        el = document.getElementById(this.CbSelector.cbHovered);
      } else el = event.target;
      this.selectedRow ? this.selectedRow.classList.remove('bg-blue-300') : null;
      this.selectedRow = findAncestor(el, 'tr');
      this.selectedRow.classList.add('bg-blue-300');
    },
    async changePath(event){
      const fd = new FormData(event.target)
      const path = fd.get('path');

      // change Path
      let values = await this.CbSelector.changePath(event, {data: fd}, this.CbSelector.cancel); // output array contains filename
      if(!values) return;
      
      // hapus list di folder, tidak seperti listtree yang ada level dan list model
      const csdbChangedPath = [];
      values.forEach((filename) => {
        let csdb = this.data.csdb.find((obj) => obj.filename === filename);
        let index = this.data.csdb.indexOf(csdb);
        this.data.csdb.splice(index,1);
        csdb.path = path;
        csdbChangedPath.push(isProxy(csdb) ? toRaw(csdb) : csdb);
      });

      // emit
      this.emitter.emit('ChangePathCSDBObjectFromFolder',csdbChangedPath);
    }
  },
  mounted(){
    this.emitter.on('TES', (data) => {
      alert('TES emit')
    });
    
    // dari Listtree via Explorer/Management data data berisi path doang,
    this.emitter.on('Folder-refresh', (data) => {
      if(data.path === this.data.current_path){
        this.getObjs({path: data.current_path})
      }
    });
    if(this.$route.params.filename && this.techpubStore.currentObjectModel.csdb){
      this.getObjs({path: this.techpubStore.currentObjectModel.csdb.path});
      this.data.current_path = this.techpubStore.currentObjectModel.csdb.path;
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
        <table class="table" :id="CbSelector.id">
          <thead class="text-sm">
            <tr class="leading-3 text-sm">
              <th v-show="CbSelector.selectionMode"></th>
              <th class="text-sm">Name <Sort :function="sortTable"></Sort></th>
              <th class="text-sm">Path <Sort :function="sortTable"></Sort></th>
              <th class="text-sm">Created At <Sort :function="sortTable"></Sort></th>
              <th class="text-sm">Updated At <Sort :function="sortTable"></Sort></th>
            </tr>
          </thead>
          <tbody>
            <tr @click.stop.prevent="select($event)" @dblclick.prevent="clickFolder($event, path)" @contextmenu.prevent="CbSelector.isShowTriggerPanel = true" @mouseover="()=>{if(!CbSelector.isShowTriggerPanel) CbSelector.cbHovered = 'cb'+path}" v-for="path in folders" class="folder-row text-sm hover:bg-blue-300 cursor-pointer">
              <td v-show="CbSelector.selectionMode" class="flex" @click.stop.prevent>
                <input file="false" :id="'cb'+path" type="checkbox" :value="path">
              </td>
              <td class="leading-3 text-sm" colspan="6">
                <span class="material-symbols-outlined text-sm mr-1">folder</span>
                <span class="text-sm">{{ path.split("/").at(-1) }} </span> 
                <!-- min -2 karena diujung folder ada '/' -->
              </td>
            </tr>
            <!-- <tr @click.stop.prevent="select($event)" @dblclick.prevent="clickFilename($event, {filename: obj.filename, path: obj.path})" @contextmenu.prevent="CbSelector.isShowTriggerPanel = true" @mouseover="()=>{if(!CbSelector.isShowTriggerPanel) CbSelector.cbHovered = 'cb'+obj.filename}" v-for="obj in models" class="file-row text-sm hover:bg-blue-300 cursor-pointer"> -->
            <tr @click.stop.prevent="select($event)" @dblclick.prevent="clickFilename($event, obj.filename)" @contextmenu.prevent="CbSelector.isShowTriggerPanel = true" @mouseover="()=>{if(!CbSelector.isShowTriggerPanel) CbSelector.cbHovered = 'cb'+obj.filename}" v-for="obj in models" class="file-row text-sm hover:bg-blue-300 cursor-pointer">
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
      <!-- <div @click="selectAll(!isSelectAll)" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900"> -->
      <div @click="CbSelector.selectAll(!CbSelector.isSelectAll)" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">{{ CbSelector.isSelectAll ? 'Deselect' : 'Select' }} All</div>
      </div>
      <div @click="CbSelector.selectAll(!CbSelector.isSelectAll, `.folder input[file='true']`)" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">{{ CbSelector.isSelectAll ? 'Deselect' : 'Select' }} All Files</div>
      </div>
      <hr class="border border-gray-300 block mt-1 my-1 border-solid"/>
      <div @click="dispatch" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Dispatch</div>
      </div>
      <div class="flex flex-col hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <form class="text-sm" @submit.prevent="changePath($event)">
          <label class="text-sm">Move&#160;</label>
          <input type="text" class="w-[65%] rounded-sm h-0" name="path" @keydown.enter.prevent/>
          <button class="material-icons text-sm ml-2 hover:bg-blue-300 hover:border rounded-full px-1">send</button>
        </form>
      </div>
      <hr class="border border-gray-300 block mt-1 my-1 border-solid"/>
      <div @click.prevent="CbSelector.cancel()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Cancel</div>
      </div>
    </RCMenu>
  </div>
</template>