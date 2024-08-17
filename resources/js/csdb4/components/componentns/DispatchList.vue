<script>
import Sort from '../subComponents/Sort.vue';
import { getObjs } from './FolderVue';
import { storingResponse, clickFilename } from './DispatchListVue';
import DispatchListVueCb from './DispatchListVueCb.js';
import ContinuousLoadingCircle from '../../loadingProgress/ContinuousLoadingCircle.vue';
import ContextMenu from '../subComponents/ContextMenu.vue';

export default {
  data(){
    return {
      data: {},
      showLoadingProgress: false,

      contextMenuId: 'cmDispatchListVue',

      cbId: 'cbDispatchListVue',
      CB: {}
    }
  },
  components:{ Sort, ContextMenu, ContinuousLoadingCircle },
  methods:{
    getObjs: getObjs,
    storingResponse: storingResponse,
    clickFilename: clickFilename
  },
  mounted(){
    this.getObjs({routeName: 'api.get_ddn_list'})

    this.CB = new DispatchListVueCb(this.cbId)
    this.CB.register();
  }
}
</script>
<template>
  <div class="h-[100%] w-full relative">

    <h1>Dispatch Note List</h1>

    <div class="h-[75%] block relative overflow-scroll">
      <table class="table" :id="cbId">
        <thead class="text-sm text-left">
          <tr class="leading-3 text-sm">
            <th v-show="CB.selectionMode"></th>
            <th class="text-sm">Filename <Sort/></th>
            <th class="text-sm">Date <Sort/></th>
            <th class="text-sm">From <Sort/></th>
            <th class="text-sm">Email <Sort/></th>
            <th class="text-sm">Enterprise <Sort/></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="obj in data.list" cb-room @dblclick.prevent="clickFilename($event, obj.csdb.filename)"
            class="file-row text-sm hover:bg-blue-300 cursor-pointer">
            <td cb-window>
              <input file type="checkbox" :value="obj.filename">
            </td>
            <td class="leading-3 text-sm">
              <span class="material-symbols-outlined text-sm mr-1">description</span>
              <span class="text-sm"> {{ obj.csdb.filename }} </span>
            </td>
            <td class="leading-3 text-sm">
              {{ obj.year }}-{{ obj.month }}-{{ obj.day }}
            </td>
            <td class="leading-3 text-sm">
              {{ obj.csdb.owner.first_name }} {{ obj.csdb.owner.middle_name }} {{ obj.csdb.owner.last_name }}
            </td>
            <td class="leading-3 text-sm">
              {{ obj.csdb.owner.email }}
            </td>
            <td class="leading-3 text-sm">
              {{ obj.csdb.owner.work_enterprise.name }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <ContinuousLoadingCircle :show="showLoadingProgress" />

    <ContextMenu :id="contextMenuId">
      <div @click.stop.prevent="CB.push" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Select</div>
      </div>
      <hr class="border border-gray-300 block mt-1 my-1 border-solid" />
      <div @click.prevent="CB.cancel()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Cancel</div>
      </div>
    </ContextMenu>
  </div>
</template>