<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
import CreateDML from './CreateDML.vue';
import AnalyzeDML from './AnalyzeDML.vue';
import axios from 'axios';
import ListObjectTree from './ListObjectTree.vue';
// import createApp
// import defineComponent
// import { VueElement } from '@vue/test-utils/dist/types';

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      filenameAnalysis: '',
    }
  },
  components: { CreateDML, AnalyzeDML, ListObjectTree },
  props: {
    type: {
      type: String,
      required: true,
      // value: 'dml','csl','brex','brdp', 'dmc_staging', 'dmc_unstaging', 'csl_unstaging', 'csl_staging'
    },
    clickFilename: {
      type: Function,
      default: () => 1,
    }
  },
  computed:{
    hasPath(){
      // const { __csdb } = this.techpubStore[`${this.$props.type}_list`];
      // console.log(__csdb, this.techpubStore[`${this.$props.type}_list`]);
      // return true;
      // const { __n219 } = this.techpubStore[`${this.$props.type}_list`];
      // console.log(__n219, this.techpubStore[`${this.$props.type}_list`]);
      // return __n219;
    }
  },
  methods:{
    list(path = 'foo', data = []){
      return this.dynamic;
    },
  },
  async mounted() {
    // console.log(window.v = VueElement);
    this.techpubStore.get_list(this.$props.type);
    // let objs = await this.techpubStore.get_list(this.$props.type);
    // console.log(window.objs = objs);
  },
}
</script>
<style>
.index-csdb table td:nth-child(2) {
  text-wrap: nowrap;
}
</style>
<template>
  <div v-if="techpubStore[`${$props.type}_list`]" class="index-csdb overflow-auto mb-5">
    <div class="bg-blue-500 py-3 px-2 text-white mb-3 border rounded-t-xl text-center">
      <span class="text-2xl">
        <slot name="title" />
      </span>
    </div>
    <div class="flex justify-center mb-3">
      <input @change="techpubStore.get_list($props.type)" v-model="techpubStore[`${$props.type}_filenameSearch`]"
        placeholder="find filename" type="text"
        class="w-48 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
      <button class="material-icons mx-3 text-gray-500 text-sm has-tooltip-arrow" data-tooltip="info"
        @click="$root.info({ name: 'searchCsdbObject' })">info</button>
    </div>
    <div class="mb-3">
      <ListObjectTree :type="$props.type"/>
    </div>
  </div>
  
  <!-- <div v-if="techpubStore[`${$props.type}_list`]" class="index-csdb overflow-auto mb-5">
    <div class="bg-blue-500 py-3 px-2 text-white mb-3 border rounded-t-xl text-center">
      <span class="text-2xl">
        <slot name="title" />
      </span>
    </div>
    <div class="flex justify-center mb-3">
      <input @change="techpubStore.get_list($props.type)" v-model="techpubStore[`${$props.type}_filenameSearch`]"
        placeholder="find filename" type="text"
        class="w-48 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
      <button class="material-icons mx-3 text-gray-500 text-sm has-tooltip-arrow" data-tooltip="info"
        @click="$root.info({ name: 'searchCsdbObject' })">info</button>
    </div>
    <div class="flex justify-center mb-3">
      <div>
        <table class="w-full table-cell">
          <thead class="h-10">
            <tr>
              <th>No.</th>
              <th>Filename</th>
              <th>Editable</th>
              <th>Initiator</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(obj, i) in techpubStore[`${$props.type}_list`].data">
              <td>{{ i + 1 }}</td>
              <td><a href="#" @click="$props.clickFilename ? $props.clickFilename(obj.filename) : ''">{{ obj.filename
              }}</a>
              </td>
              <td>{{ obj.editable ? 'yes' : 'no' }} </td>
              <td>{{ obj.initiator.name == techpubStore.Auth.name ? 'self' : obj.initiator.name }} </td>
              <td :class="[obj.remarks.stage == 'staged' ? 'bg-green-500' : 'bg-yellow-500']">{{ obj.remarks.stage }}</td>
              <td>
                <slot name="actionColumn" :filename="obj.filename" />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="flex justify-center items-center">
      <button
        @click="techpubStore.goto($props.type, techpubStore[`${$props.type}_list`]['current_page'] > 0 ? techpubStore[`${$props.type}_list`]['current_page'] - 1 : 1)"
        class="material-symbols-outlined">navigate_before</button>
      <form @submit.prevent="techpubStore.goto($props.type, techpubStore[`${$props.type}_page`])" class="inline-block">
        <input v-model="techpubStore[`${$props.type}_page`]" class="w-6" />
        <span> of {{ techpubStore[`${$props.type}_list`]['last_page'] }} </span>
      </form>
      <button
        @click="techpubStore.goto($props.type, techpubStore[`${$props.type}_list`]['current_page'] < techpubStore[`${$props.type}_list`]['last_page'] ? techpubStore[`${$props.type}_list`]['current_page'] + 1 : techpubStore[`${$props.type}_list`]['last_page'])"
        class="material-symbols-outlined">navigate_next</button>
    </div>
  </div> -->
</template>