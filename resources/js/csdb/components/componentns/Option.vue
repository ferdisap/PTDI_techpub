<!-- 
  VUE ini DEPRECIATED karena Option tidak lagi dipakai difrontEnd (terkahir di Explorer.vue) karena diganti oleh right click menu
 -->
<script>
import axios from 'axios';

export default {
  data(){
    return {
      data: {},
    }
  },
  props: {
    dataProps: {
      type: Object,
      required: true,
    }
  },
  computed:{
    modelPath(){
      return this.dataProps.path;
    },
    modelFilename(){
      return this.dataProps.filename;
    },
  },
  methods: {
    async submitChangePathForm(evt){
      window.evt = evt;
      let data = new FormData(evt.target);
      window.fd = data;
      let response = await axios({
        route: {
          name: 'api.change_object_path',
          data: data,
        }
      });
      if(response.statusText === 'OK'){
        this.emitter.emit('ChangePathCSDBObjectFromOption', response.data.data);
      }
    },
    async submitDeleteForm(){
      if (!(await this.$root.alert({ name: 'beforeDeleteCsdbObject', filename: this.modelFilename }))) {
        return;
      }
      let response = await axios({
        route: {
          name: 'api.delete_object',
          data: {filename: this.modelFilename},
        }
      });
      if(response.statusText === 'OK'){
        // console.log(response.data.data2);
        this.emitter.emit('DeleteCSDBObjectFromOption', [response.data.data, response.data.data2]);
      }
    },
    submitIssueeForm(){},
    submitCommitForm(){},
  },
}
</script>
<template>
  <div class="Option overflow-auto w-full">
    <div class="h-[5%] flex mb-3">
      <h1 class="text-blue-500 w-full text-center">Option</h1>
    </div>
    <div class="px-3">
      <div class="text-center mb-3 text-base">{{ $props.dataProps.filename }}</div>
      <form @submit.prevent="submitChangePathForm($event)" class="mb-3 flex flex-col">
        <label class="text-base" for="option-change-path">Change Path</label>
        <div class="flex">
          <input type="hidden" :value="modelFilename" name="filename"/>
          <input :value="modelPath" id="option-change-path" name="path" placeholder="type the fullpath eg. csdb/n219/amm" type="text" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
          <button type="submit" class="button-violet text-sm inline-block">update</button>
        </div>
      </form>
      <form @submit.prevent="submitDeleteForm($event)" class="mb-3 flex">
        <label class="text-base">Delete this CSDB Object</label>
        <button type="submit" name="delete" class="button-danger w-max">delete</button>
      </form>      
      <form @submit.prevent="submitIssueeForm($event)" class="mb-3 flex">
        <label class="text-base">Issue this CSDB Object</label>
        <button type="submit" name="issue" class="button-primary w-max">issue</button>
      </form>
      <form @submit.prevent="submitCommitForm($event)" class="mb-3 flex">
        <label class="text-base">Commit this CSDB Object</label>
        <button type="submit" name="commit" class="button-primary w-max">commit</button>
      </form>
    </div>
    <hr/>
  </div>
</template>