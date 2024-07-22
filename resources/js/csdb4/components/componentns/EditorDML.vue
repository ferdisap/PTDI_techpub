<script>
import axios from 'axios';
import { useTechpubStore } from '../../../techpub/techpubStore';
import Remarks from '../subComponents/Remarks.vue';
import ContinuousLoadingCircle from '../../loadingProgress/continuousLoadingCircle.vue';
import DmlEntryForm from '../subComponents/DmlEntryFrom.vue';
import Sort from '../../../techpub/components/Sort.vue';
import PreviewRCMenu from '../../rightClickMenuComponents/PreviewRCMenu.vue';
import DropdownInputSearch from '../../DropdownInputSearch';
export default {
  data(){
    return{
      techpubStore: useTechpubStore(),
      showLoadingProgress: false,
      transformed: '',
      filename: '',
      DropdownBrexSearch: new DropdownInputSearch('filename')
    }
  },
  components:{Remarks, ContinuousLoadingCircle, DmlEntryForm, PreviewRCMenu},
  computed:{
    isUpdate(){
      if(this.$route.params.filename){
        this.showDMLContent();
        return true;
      }
    },
    dynamic(){
      return {
        template: this.transformed,
        components: {DmlEntryForm, Sort},
        data(){
          return {
            store: useTechpubStore(),
          }
        },
        methods:{
          sort() {
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
            let rows = table.find('tr:gt(0)').toArray().sort(comparer(th.index()));
            this.asc = !this.asc;
            if (!this.asc) {
              rows = rows.reverse();
            }
            for (let i = 0; i < rows.length; i++) {
              table.append(rows[i]);
            }
          },
        }
      }
    },
  },
  methods:{
    async submit(event){
      this.showLoadingProgress = true;
      const formData = new FormData(event.target);
      let response = await axios({
        route: {
          name: 'api.create_dml',
          data: formData,
        },
        useMainLoadingBar: false,
      });
      if(response.statusText === 'OK'){
        // this.emitter.emit('createDMLFromEditorDML', { model: response.data.data });
        // do something here
      }
      this.showLoadingProgress = false;
    },
    async update(event){
      this.showLoadingProgress = true;
      const formData = new FormData(event.target);
      let response = await axios({
        route: {
          name: 'api.dmlupdate',
          data: formData,
        },
        useMainLoadingBar: false,
      });
      if(response.statusText === 'OK'){
        // this.emitter.emit('createDMLFromEditorDML', { model: response.data.data });
        // do something here
      }
      this.showLoadingProgress = false;
    },
    async showDMLContent(){
      this.showLoadingProgress = true;
      let response = await axios({
        route: {
          name: 'api.get_html_content',
          data: {filename: this.$route.params.filename}
        },
        useMainLoadingBar: false,
      });
      if(response.statusText === 'OK'){
        // do something here
        this.transformed = response.data.transformed
        this.showLoadingProgress = false;
        this.techpubStore.currentObjectModel = response.data.model;
      }
    },
    async searchBrex(event){
      if(event.target.id === this.DropdownBrexSearch.idInputText){
        let route = this.techpubStore.getWebRoute('api.dmc_search_model', {sc: "filename::" + event.target.value, limit:5});
        this.DropdownBrexSearch.keypress(event, route);
      }
    },
  },
  mounted(){
    // if(this.$route.params.filename){
    //   this.isUpdate = true;
    //   this.showDMLContent();
    // } 
    // this.showDMLContent;
  }
}
</script>
<template>
  <div class="EditorDML px-3">
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

    <form id="dml" v-if="isUpdate" @submit.prevent="update($event)">
      <input type="hidden" name="filename" :value="$route.params.filename" />

      <component :is="dynamic" v-if="transformed" />

      <div class="w-full text-center mb-3 mt-3">
        <button class="button-violet" type="submit">Update</button>
      </div>
    </form>

    <PreviewRCMenu v-if="isUpdate">
      <div class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm" @click="()=>this.emitter.emit('DeleteCSDBObjectFromEveryWhere', {filename: $route.params.filename})">
          <span href="#" class="material-symbols-outlined bg-transparent text-sm mr-2 text-red-600">delete</span>
          Delete</div>
      </div>
      <div class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">
          <span href="#" class="material-symbols-outlined bg-transparent text-sm mr-2 text-green-600">devices</span>
          Issue</div>
      </div>
      <div class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm" @click="()=>this.emitter.emit('CommitCSDBObjectFromEveryWhere', {filename: $route.params.filename})">
          <span href="#" class="material-symbols-outlined bg-transparent text-sm mr-2">commit</span>
          Commit</div>
      </div>  
    </PreviewRCMenu>

    <ContinuousLoadingCircle :show="showLoadingProgress"/>
  </div>
</template>