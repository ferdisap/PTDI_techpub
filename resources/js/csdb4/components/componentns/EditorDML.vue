<script>
// import axios from 'axios';
import { useTechpubStore } from '../../../techpub/techpubStore';
import Remarks from '../subComponents/Remarks.vue';
import ContinuousLoadingCircle from '../../loadingProgress/continuousLoadingCircle.vue';
import { submit, update, showDMLContent, searchBrex } from './EditorDMLVue.js';
// import DynamicDML from '../subComponents/DynamicDML.js';
import DropdownInputSearch from '../../DropdownInputSearch';
import DML from '../subComponents/DML.vue';
export default {
  data(){
    return{
      techpubStore: useTechpubStore(),
      showLoadingProgress: false,
      DropdownBrexSearch: new DropdownInputSearch('filename'),
      transformed: '',
      json: '',
    }
  },
  components:{Remarks, ContinuousLoadingCircle, DML},
  computed:{
    isUpdate(){
      if(this.$route.params.filename){
        this.showDMLContent();
        return true;
      }
    },
    // dynamic: DynamicDML,
  },
  methods:{
    submit: submit,
    update: update,
    showDMLContent: showDMLContent,
    searchBrex: searchBrex,
  },
  mounted(){}
}
</script>
<template>
  <div class="EditorDML px-3 relative">
    <h1 class="mt-2 mb-4 text-center underline">{{ isUpdate ? 'Update DML' : 'Create DML' }}</h1>
    
    <form @submit.prevent="submit($event)" v-if="!isUpdate">
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
          <label :for="DropdownBrexSearch.idInputText" class="inline-block text-gray-900 dark:text-white text-lg font-bold">Brex:&#160;</label>
        </div>
        <div class="mr-2 w-80 relative">
          <div class="w-80">
            <div v-show="!DropdownBrexSearch.isDone" class="mini_loading_buffer_dark right-[10px] top-[10px]"></div>
            <input @keyup.prevent="searchBrex($event)" :id="DropdownBrexSearch.idInputText" name="brexDmRef" placeholder="eg.: DMC-MALE-A-00-00-00-00A-022A-D_000-01_EN-EN" class="w-full" autocomplete="off" aria-autocomplete="none"/>
          </div>
          <div class="text-red-600" v-html="techpubStore.error('brexDmRef')"></div>  
          <div class="w-full" :id="DropdownBrexSearch.idDropdownListContainer">
            <div class="text-sm border-b px-2" v-show="DropdownBrexSearch.showList" v-for="(dmc) in DropdownBrexSearch.result" :filename="dmc.filename" @click.prevent="DropdownBrexSearch.keypress($event)" @keyup.prevent="DropdownBrexSearch.keypress($event)">
              {{ dmc.filename}}
            </div>
          </div>
        </div>
      </div>
      
      <!-- Remarks -->
      <div class="mb-2">
        <Remarks/>
      </div>

      <button type="submit" class="button-violet">Submit</button>
    </form>

    <!-- <form id="dml" v-if="isUpdate" @submit.prevent="update($event)">
      <input type="hidden" name="filename" :value="$route.params.filename" />

      <component :is="dynamic" v-if="transformed" />

      <div class="w-full text-center mb-3 mt-3">
        <button class="button-violet">Update</button>
      </div>
    </form> -->
    <DML v-if="json" :json="json"/>

    <ContinuousLoadingCircle :show="showLoadingProgress"/>
  </div>
</template>