<script>
import axios from 'axios';
import { useTechpubStore } from '../../../techpub/techpubStore';
import Remarks from '../subComponents/Remarks.vue';
import ContinuousLoadingCircle from '../../loadingProgress/continuousLoadingCircle.vue';
import DmlEntryForm from '../subComponents/DmlEntryFrom.vue';
import Sort from '../../../techpub/components/Sort.vue';
export default {
  data(){
    return{
      techpubStore: useTechpubStore(),
      isUpdate: false,
      showLoadingProgress: false,
      transformed: '',
    }
  },
  components:{Remarks, ContinuousLoadingCircle, DmlEntryForm},
  computed:{
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
    }
  },
  methods:{
    async submit(event){
      // this.showLoadingProgress = true;

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
    }
  },
  mounted(){
    if(this.$route.params.filename){
      this.isUpdate = true;
      this.showDMLContent();
    } 
  }
}
</script>
<template>
  <div class="EditorDML">
    <form @submit.prevent="submit($event)" v-if="!isUpdate">
      <!-- untuk DML Type -->
      <input type="hidden" value="p" name="dmlType"/>

      <!-- untuk Model Ident Code -->
      <label for="modelIdentCode" class="inline-block mb-2 text-gray-900 dark:text-white text-lg font-bold">Model Ident Code (Project)</label>
      <input type="text" value="" name="modelIdentCode" id="modelIdentCode" placeholder="eg.: MALE" class="ml-3"/>
      <div class="text-red-600" v-html="techpubStore.error('modelIdentCode')"></div>

      <!-- Originator -->
      <label for="originator" class="inline-block mb-2 text-gray-900 dark:text-white text-lg font-bold">Sender / Originator CAGE Code</label>
      <input type="text" value="" name="originator" id="originator" placeholder="eg.: 0001Z" class="ml-3"/>
      <div class="text-red-600" v-html="techpubStore.error('originator')"></div>
  
      <!-- Security Classification -->
      <label for="securityClassification" class="inline-block mb-2 text-gray-900 dark:text-white text-lg font-bold">Choose Security Level</label>
      <select name="securityClassification" id="securityClassification" class="ml-3">
        <option value="">--Required to choose--</option>
        <option value="01">Unclassified</option>
        <option value="02">Restricted</option>
        <option value="03">Confidential</option>
        <option value="04">Secret</option>
        <option value="05">Top Secret</option>
      </select>
      <div class="text-red-600" v-html="techpubStore.error('securityClassification')"></div>
  
      <!-- BREX -->
      <br/>
      <label for="brexDmRef" class="inline-block mb-2 text-gray-900 dark:text-white text-lg font-bold">BREX Data Module Ref</label>
      <input type="text" value="" name="brexDmRef" id="brexDmRef" placeholder="eg.: DMC-MALE-A-00-00-00-00A-022A-D_000-01_EN-EN" class="ml-3" />
      <div class="text-red-600" v-html="techpubStore.error('brexDmRef')"></div>
      
      <!-- Remarks -->
      <Remarks/>

      <button type="submit" class="button-violet">Submit</button>
    </form>

    <form id="dml" @submit.prevent="update" v-if="isUpdate">
      <input type="hidden" name="filename" :value="$route.params.filename" />

      <component :is="dynamic" v-if="transformed" />

      <div class="w-full text-center mb-3 mt-3">
        <button class="button-violet" type="submit">Update</button>
      </div>
    </form>

    <ContinuousLoadingCircle :show="showLoadingProgress"/>
  </div>
</template>