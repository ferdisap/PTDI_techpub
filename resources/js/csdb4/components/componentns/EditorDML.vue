<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
import Remarks from '../subComponents/Remarks.vue';
import ContinuousLoadingCircle from '../../loadingProgress/continuousLoadingCircle.vue';
import { submit, update, showDMLContent, searchBrex } from './EditorDMLVue.js';
import DropdownInputSearch from '../../DropdownInputSearch';
import DML from '../subComponents/DML.vue';
import ContextMenu from '../subComponents/ContextMenu.vue';

export default {
  data(){
    return{
      techpubStore: useTechpubStore(),
      showLoadingProgress: false,
      // DropdownBrexSearch: new DropdownInputSearch('filename'),
      
      // transformed: '',
      // json: '',

      isUpdate: '',

      contextMenuId:'cmEditorDMLVue',
    }
  },
  components:{Remarks, ContinuousLoadingCircle, DML, ContextMenu},
  computed:{},
  methods:{
    submit: submit,
    update: update,
    showDMLContent: showDMLContent,
    searchBrex: searchBrex,
  },
  mounted(){
    if(this.$route.params.filename && (this.$route.params.filename.substring(0,3) === 'DML')) {
      this.isUpdate = true;
      // this.showDMLContent();
    }
    
    if(this.ContextMenu.register(this.contextMenuId)) this.ContextMenu.toggle(false, this.contextMenuId);
    this.Dropdown.register('dmlForm')
  }
}
</script>
<template>
  <div class="EditorDML px-3 relative">
    <h1 class="mt-2 mb-4 text-center underline">{{ isUpdate ? 'Update DML' : 'Create DML' }}</h1>
    
    <DML v-if="isUpdate" :filename="$route.params.filename"/>

    <form v-show="!isUpdate" @submit.prevent="submit($event)" id="dmlForm">
      <!-- untuk DML Type -->
      <input type="hidden" value="p" name="dmlType"/>

      <!-- untuk Model Ident Code -->
      <div class="mb-2">
        <label for="modelIdentCode" class="inline-block text-gray-900 dark:text-white text-lg font-bold">Model Ident Code (Project): </label>
        <input type="text" value="" name="modelIdentCode" id="modelIdentCode" placeholder="eg.: MALE" class="ml-3"/>
        <div class="text-red-600" v-html="techpubStore.error('modelIdentCode')"></div>
      </div>

      <!-- Originator -->
      <div class="mb-2">
        <label for="originator" class="inline-block text-gray-900 dark:text-white text-lg font-bold">Sender / Originator CAGE Code: </label>
        <input type="text" value="" name="originator" id="originator" placeholder="eg.: 0001Z" class="ml-3"/>
        <div class="text-red-600" v-html="techpubStore.error('originator')"></div>
      </div>
  
      <!-- Security Classification -->
      <div class="mb-2">
        <label for="securityClassification" class="inline-block text-gray-900 dark:text-white text-lg font-bold">Security Level: </label>
        <select name="securityClassification" id="securityClassification" class="ml-3">
          <option value="01">Unclassified</option>
          <option value="02">Restricted</option>
          <option value="03">Confidential</option>
          <option value="04">Secret</option>
          <option value="05">Top Secret</option>
        </select>
        <div class="text-red-600" v-html="techpubStore.error('securityClassification')"></div>
      </div>
  
      <!-- BREX -->
      <div class="mb-2 mt-2 flex">
        <div class="mr-2">
          <label class="inline-block text-gray-900 dark:text-white text-lg font-bold">Brex: </label>
        </div>
        <div class="mr-2 w-80 relative">
          <div class="w-80">
            <!-- <div v-show="!DropdownBrexSearch.isDone" class="mini_loading_buffer_dark right-[10px] top-[10px]"></div> -->
            <input id="tes" dd-input="filename" dd-target="self" dd-type="csdbs" dd-route="api.get_object_csdbs" name="brexDmRef" placeholder="eg.: DMC-MALE-A-00-00-00-00A-022A-D_000-01_EN-EN" class="w-full" autocomplete="off" aria-autocomplete="none"/>
          </div>
          <div class="text-red-600" v-html="techpubStore.error('brexDmRef')"></div>  
        </div>
      </div>
      
      <!-- Remarks -->
      <div class="mb-2">
        <Remarks class="text-sm border-gray-300 border rounded-md p-2 bg-gray-50"/>
      </div>

      <button type="submit" class="button-violet">Submit</button>
    </form>

    <ContinuousLoadingCircle :show="showLoadingProgress"/>

    <ContextMenu :id="contextMenuId">
      <div @click.stop.prevent="$parent.editorComponent = 'EditorXML'"
        class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">XML Editor</div>
      </div>
      <div @click.stop.prevent="$parent.editorComponent = 'EditorICN'"
        class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Upload ICN</div>
      </div>
      <div v-if="$route.params.filename" @click.stop.prevent="isUpdate = true"
        class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Update DML</div>
      </div>
    </ContextMenu>

  </div>
</template>