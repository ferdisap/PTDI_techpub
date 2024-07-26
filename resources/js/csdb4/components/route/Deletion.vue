<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
import {getObjs, storingResponse, goto, removeList, restore, permanentDelete, download, refresh, select, preview, clickFilename} from './DeletionVue.js';
import {CsdbObjectCheckboxSelector} from '../../CheckboxSelector';
import ContinuousLoadingCircle from "../../loadingProgress/ContinuousLoadingCircle.vue";
import RCMenu from "../../rightClickMenuComponents/RCMenu.vue";
import Sort from "../subComponents/Sort.vue";

export default {
  name: 'Deletion',
  components: {ContinuousLoadingCircle, RCMenu, Sort},
  data() {
    return {
      techpubStore: useTechpubStore(),
      data: {},
      showLoadingProgress: false,

      CbSelector: new CsdbObjectCheckboxSelector(),

      // selection view (becasuse clicked by user)
      selectedRow: undefined,
    }
  },
  computed: {
    list() {
      return this.data.csdb ?? [];
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
    getObjs: getObjs,
    storingResponse: storingResponse,
    goto: goto,
    removeList: removeList,
    restore: restore,
    permanentDelete: permanentDelete,
    download: download,
    clickFilename: clickFilename,
    refresh: refresh,
    select: select,
    preview: preview,
  },
  mounted() {
    this.getObjs({ filenameSearch: this.filenameSearch });
    this.emitter.on('Deletion-refresh', refresh.bind(this))
  }
}
</script>
<template>
  <div class="deletion overflow-auto h-full">

    <div class="bg-white px-3 py-3 2xl:h-[92%] xl:h-[90%] lg:h-[88%] md:h-[90%] sm:h-[90%] h-full" @contextmenu.prevent="CbSelector.isShowTriggerPanel = true">

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

        <div class="block relative oveflow-auto max-h-[80%] text-left">
          <table class="table" :id="CbSelector.id">
            <thead class="text-sm">
              <tr>
                <th v-show="CbSelector.selectionMode" class="w-[2%] text-sm"></th>
                <th class="w-[53%] text-sm">Filename <Sort/></th>
                <th class="w-[15%] text-sm">Path</th>
                <th class="w-[15%] text-sm">Last Update</th>
                <th class="w-[15%] text-sm">Deleted At</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="object in list" @click.stop.prevent="select($event)" @dblclick.prevent="clickFilename($event, path)" @mousemove="CbSelector.setCbHovered('cbdell'+object.filename)" class="text-sm hover:cursor-pointer" >
                <td v-show="CbSelector.selectionMode" class="flex p-2" @click.stop.prevent>
                  <input file="false" :id="'cbdell'+object.filename" type="checkbox" :value="object.filename">
                </td>
                <td class="text-sm">
                  <span class="material-symbols-outlined text-sm mr-1">description</span>
                  <span class="text-sm"> {{ object.filename }} </span>
                </td>
                <td class="text-sm">{{ object.path }}</td>
                <td class="text-sm">{{ techpubStore.date(object.updated_at) }}</td>
                <td class="text-sm">{{ techpubStore.date(object.last_history.created_at) }}</td>
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
      <div @click="CbSelector.copy()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Copy</div>
      </div>
      <div @click="download()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Download</div>
      </div>
      <div @click="preview()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Preview</div>
      </div>
      <hr class="border border-gray-300 block mt-1 my-1 border-solid"/>
      <div @click="restore()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Restore</div>
      </div>
      <div @click="permanentDelete()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Permanent Delete</div>
      </div>
      <hr class="border border-gray-300 block mt-1 my-1 border-solid"/>
      <div @click.prevent="CbSelector.cancel()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Cancel</div>
      </div>
    </RCMenu>
  </div>
</template>