<!-- 
  semua props emit yang ada filename is depreciated. Digantikan oleh $route.params
 -->
<script>
import BottomBar from '../subComponents/BottomBar.vue';
import ListTree from '../componentns/ListTree.vue'
import Folder from '../componentns/Folder.vue';
import { objectType } from '../../../helper.js';
import Preview from '../componentns/Preview.vue';
import Editor from '../componentns/Editor.vue';
import History from '../componentns/History.vue';
import DispatchTo from '../componentns/DispatchTo.vue';
import ContextMenu from '../subComponents/ContextMenu.vue';
import { bottomBarItems, colWidth, col1Width, col2Width, col3Width, turnOnSizing, turnOffSizing } from './ExplorerVue.js';

export default {
  name: 'Explorer',
  components: { BottomBar, ListTree, Folder, Preview, Editor, History, DispatchTo, ContextMenu },
  data() {
    return {
      bottomBarItems: bottomBarItems,
      colWidth: colWidth,
      contextMenuId: 'cmExplorerVue',
    }
  },
  computed: {
    col1Width: col1Width,
    col2Width: col2Width,
    col3Width: col3Width,
  },
  methods: {
    turnOnSizing: turnOnSizing,
    turnOffSizing: turnOffSizing,
  },
  mounted() {
    // window.ex = this;
    if (top.localStorage.colWidthExplorer) {
      this.colWidth = JSON.parse(top.localStorage.colWidthExplorer);
    }

    if(this.ContextMenu.register(this.contextMenuId)) this.ContextMenu.toggle(false,this.contextMenuId);

    this.emitter.on('clickFilenameFromListTree', (data) => {
      // hanya ada filename dan path di data
      this.bottomBarItems.Folder.data = data;
      this.bottomBarItems.Preview.isShow ? this.emitter.emit('Preview-refresh', data) : (this.bottomBarItems.Preview.isShow = true);
      this.bottomBarItems.Editor.data = data;
      this.bottomBarItems.History.data = data;
    });

    this.emitter.on('clickFolderFromListTree', (data) => {
      this.emitter.emit('Folder-refresh', data);
      this.bottomBarItems.Folder.isShow = true;
      this.bottomBarItems.Folder.data = data; // hanya ada path saja di data
    });

    this.emitter.on('clickFilenameFromFolder', (data) => {
      // hanya ada filename di data, bisa berguna jika perlu ambil data terbaru dari server
      this.emitter.emit('Preview-refresh', data);
      this.bottomBarItems.Preview.isShow = true;
      this.bottomBarItems.History.data = data;
    });

    this.emitter.on('createObjectFromEditor', (data) => {
      // data adalah csdb file sql, bukan model/meta object
      this.emitter.emit('ListTree-refresh', data);
      this.$root.gotoExplorer(data.filename);
      this.bottomBarItems.Preview.isShow = true;
      this.bottomBarItems.Preview.data = data;
      this.bottomBarItems.History.data = data;
    })

    this.emitter.on('createDMLFromEditorDML', (data) => {
      // data adalah csdb file sql, bukan model/meta object
      this.emitter.emit('ListTree-refresh', data);
      this.$root.gotoExplorer(data.filename);
      this.bottomBarItems.Preview.isShow = true;
      this.bottomBarItems.Preview.data = data;
      this.bottomBarItems.History.data = data.model;
    })

    this.emitter.on('uploadICNFromEditor', (data) => {
      // data adalah csdb file sql, bukan model/meta object
      this.emitter.emit('ListTree-refresh', data);
      this.$root.gotoExplorer(data.filename);
    });

    this.emitter.on('updateObjectFromEditor', (data) => {
      // data adalah csdb file sql, bukan model/meta object
      this.emitter.emit('Preview-refresh', data);
      this.emitter.emit('History-refresh', data); // sepertinya ini tidak usah. Biar ga kebanyakan request
    });

    this.emitter.on('readFileURLFromEditor', (data) => {
      // data berisi mime, source, sourceType
      this.bottomBarItems.Preview.isShow = true;
      setTimeout(() => this.emitter.emit('Preview-refresh', data), 0);
      this.bottomBarItems.History.isShow = false;
      // this.bottomBarItems.Analyzer.isShow = false;
    });

    this.emitter.on('readTextFileFromUploadICN', () => {
      this.bottomBarItems.Preview.isShow = false;
      this.bottomBarItems.History.isShow = false;
    });

    this.emitter.on('ChangePathCSDBObjectFromFolder', (data) => {
      // data adalah array berisis model SQL CSDB Object. Bisa juga cuma filename saja karena saat delete, folder bisa saja tidak punya data csdb lengkap (path) karena yang di delete bukan file tapi folder
      this.emitter.emit('ListTree-refresh', data); // tidak perlu kirim data karena nanti request ke server
    })

    this.emitter.on('dispatchTo', (data) => {
      // data adalah array contains models
      this.bottomBarItems.DispatchTo.data = data;
      this.bottomBarItems.DispatchTo.isShow = true;
    })

    this.emitter.on('AddDispatchTo', (data) => {
      // data adalah array contains models
      this.bottomBarItems.DispatchTo.data = data;
      this.bottomBarItems.DispatchTo.isShow = true;
    })

    this.emitter.on('RemoveDispatchTo', (data) => {
      // data adalah array contains models
      this.bottomBarItems.DispatchTo.data = data;
      this.bottomBarItems.DispatchTo.isShow = true;
    })

    this.emitter.on('DeleteCSDBObjectFromFolder', (data) => {
      // data adalah array berisi csdb SQL CSDB Object. Bisa juga cuma filename saja karena saat delete, folder bisa saja tidak punya data csdb lengkap (path) karena yang di delete bukan file tapi folder
      this.emitter.emit('ListTree-remove', data);
      this.emitter.emit('Deletion-refresh', data);
    })

    this.emitter.on('DeleteCSDBObjectFromListTree', (data) => {
      // data adalah array berisi csdb SQL CSDB Object. Bisa juga cuma filename saja karena saat delete, folder bisa saja tidak punya data csdb lengkap (path) karena yang di delete bukan file tapi folder
      this.emitter.emit('Folder-remove', data);
      this.emitter.emit('Deletion-refresh', data);
    })

    this.emitter.on('RestoreCSDBobejctFromDeletion', (data) => {
      // data adalah model SQL CSDB Object
      this.emitter.emit('ListTree-refresh', data);
    })

  }
}
</script>

<template>
  <div class="explorer overflow-auto h-full">
    <div class="bg-white px-3 py-3 2xl:h-[92%] xl:h-[90%] lg:h-[88%] md:h-[90%] sm:h-[90%] h-full">

      <div class="2xl:h-[5%] xl:h-[6%] lg:h-[8%] md:h-[9%] sm:h-[11%] border-b-4 border-blue-500 grid items-center">
        <h1>EXPLORER</h1>
      </div>

      <div class="explorer-content flex 2xl:h-[95%] xl:h-[94%] lg:h-[92%] md:h-[91%] sm:h-[89%]">
        <!-- col 1 -->
        <div class="flex" :style="[col1Width]">
          <div class="overflow-auto text-nowrap relative h-full w-full">
            <ListTree type="allobjects" routeName="Explorer" />
          </div>
          <div class="v-line h-full border-l-4 border-blue-500 cursor-ew-resize"
            @mousedown.prevent="turnOnSizing($event, 'satu')"></div>
        </div>

        <!-- col 2 -->
        <div class="flex" :style="[col2Width]">
          <!-- <div class="overflow-auto" id="col2tes"> -->
          <div id="col2tes" class="overflow-auto text-wrap relative h-full w-full">
            <!-- <div v-for="(component, seq) in bottomBarItems.col2" class="overflow-auto text-wrap relative h-full w-full">
              <component v-if="component.isShow" :is="component.name" :data-props="component.data" :filename="component.data.filename"/>
            </div> -->
            <!-- <div v-for="(component, seq) in bottomBarItems.col2">
              {{ component.isShow }}
            </div> -->
            <Folder v-if="bottomBarItems.Folder.isShow" :data-props="bottomBarItems.Folder.data" routeName="Explorer" />
            <Editor v-if="bottomBarItems.Editor.isShow" :filename="bottomBarItems.Editor.data.filename" text="" />
            <History v-if="bottomBarItems.History.isShow" :filename="bottomBarItems.History.data.filename" />
          </div>
        </div>
        <div class="v-line h-full border-l-[4px] border-blue-500 w-0 cursor-ew-resize"
          @mousedown.prevent="turnOnSizing($event, 'dua')"></div>

        <!-- col 3 -->
        <div class="flex" :style="[col3Width]">
          <!-- <div class="overflow-auto text-wrap relative h-full w-full">
            <Preview v-if="bottomBarItems.Preview.isShow" :dataProps="bottomBarItems.Preview.data" />
            <DispatchTo v-if="bottomBarItems.DispatchTo.isShow" :objectsToDispatch="bottomBarItems.DispatchTo.data" />
          </div> -->
        </div>
      </div>

    </div>

    <div class="w-full relative flex justify-center 2xl:h-[8%] xl:h-[10%] lg:h-[12%] md:h-[10%] sm:h-[10%]">
      <BottomBar :items="bottomBarItems" class="" />
    </div>
    
    <ContextMenu :id="contextMenuId">
      <!-- <hr class="border border-gray-300 block mt-1 my-1 border-solid"/> -->
      <div @click.prevent="emitter.emit('bottom-bar-switch','Folder')" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Folder</div>
      </div> 
      <div @click.prevent="emitter.emit('bottom-bar-switch','Editor')" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Editor</div>
      </div> 
      <div @click.prevent="emitter.emit('bottom-bar-switch','History')" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">History</div>
      </div> 
    </ContextMenu>
  </div>
</template>