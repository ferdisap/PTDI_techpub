<script>
import DropdownInputSearch from '../../DropdownInputSearch';
import {ref} from 'vue';

const input = ref(null);

export default {
  data() {
    return {
      DropdownCsdbSearch: new DropdownInputSearch('filename', 'api.csdb_search'),
    }
  },
  props:['callback'],
  methods: {
    atAction(event){
      this.$props.callback(this.DropdownCsdbSearch.keypress(event));
    },
  },
  mounted() {
    this.$refs.input.focus()
  },
}
</script>
<template>
  <div class="absolute top-[25%] w-[80%] h-max-[70%] left-[10%] border-8 border-black rounded-lg p-8 bg-slate-200">
    <h1 class="text-center font-bold mb-2">Search Csdbs</h1>
    <div class="w-full text-center mb-2 relative">
      <div class="relative">
        <span v-show="!DropdownCsdbSearch.isDone" class="mini_loading_buffer_dark right-[10px] top-[10px]"></span>
        <input ref="input" @keypress.enter.prevent @keyup.prevent="atAction($event)" :id="DropdownCsdbSearch.idInputText" placeholder="find filename" type="text" class="p-2.5 w-full inline bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
      </div>
    </div>
    <div :id="DropdownCsdbSearch.idDropdownListContainer">
      <div class="text-sm mb-2"  v-for="(csdb) in DropdownCsdbSearch.result" :filename="csdb.filename"
        @click.prevent="atAction($event)" 
        @keyup.prevent="atAction($event)">
        {{ csdb.filename }} <span class="font-mono text-sm text-pink-500 italic">{{ csdb.path }}</span>
      </div>
    </div>
  </div>
</template>