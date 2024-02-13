<script>
import axios from 'axios';
import { useTechpubStore } from '../../../techpub/techpubStore';
import Remarks from '../subComponents/Remarks.vue';

export default {
  data(){
    return{
      techpubStore: useTechpubStore(),
    }
  },
  components:{Remarks},
  methods:{
    submit(){
      this.techpubStore.Errors = [];
      const formData = new FormData(event.target);
      const route = this.techpubStore.getWebRoute('api.create_dml',formData);      
      axios({
        url: route.url,
        method: route.method[0],
        data: formData,
      })
      .then(response => {
        window.res = response;
        window.techpubStore = this.techpubStore;
        this.$root.success(response)
        this.techpubStore.DMLList[Object.keys(this.techpubStore.DMLList).length] = response.data.dml;
      })
      .catch(error => this.$root.error(error));
    },
  },
}
</script>
<template>
  <div class="CreateDML">
    <form @submit.prevent="submit()">
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

  </div>
</template>