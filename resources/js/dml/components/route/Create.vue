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
      const formData = new FormData(event.target);
      const route = this.techpubStore.getWebRoute('api.post_create_dml',formData);
      
      axios({
        url: route.url,
        method: route.method[0],
        data: formData,
      })
      .then(response => this.$root.success(response))
      .catch(error => this.$root.error(error));
    },
  },
}
</script>
<template>
  Create DML
  <div>
    <form @submit.prevent="submit()">
      <!-- untuk DML Type -->
      <label for="dmlType">Choose DML Type</label>
      <select name="dmlType" id="dmlType">
        <option value="">--Required to choose--</option>
        <option value="p">Partial</option>
        <option value="c">Complete</option>
        <option value="s">Status List</option>
      </select>
      <div class="text-red-600" v-html="techpubStore.error('dmlType')"></div>

      <!-- untuk Model Ident Code -->
      <label for="modelIdentCode">Model Ident Code (Project)</label>
      <input type="text" value="" name="modelIdentCode" id="modelIdentCode" placeholder="eg.: MALE" />
      <div class="text-red-600" v-html="techpubStore.error('modelIdentCode')"></div>

      <!-- Originator -->
      <label for="originator">Sender / Originator CAGE Code</label>
      <input type="text" value="" name="originator" id="originator" placeholder="eg.: 0001Z" />
      <div class="text-red-600" v-html="techpubStore.error('originator')"></div>
  
      <!-- Security Classification -->
      <label for="securityClassification">Choose Security Level</label>
      <select name="securityClassification" id="securityClassification">
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
      <label for="brexDmRef">BREX Data Module Ref</label>
      <input type="text" value="" name="brexDmRef" id="brexDmRef" placeholder="eg.: DMC-MALE-A-00-00-00-00A-022A-D_000-01_EN-EN" />
      <label for="extension">Extension</label>
      <select name="extension" id="extension">
        <option value="xml">XML</option>
      </select>
      <div class="text-red-600" v-html="techpubStore.error('brexDmRef')"></div>
      
      <!-- Remarks -->
      <Remarks/>

      <button type="submit" class="button-violet">Submit</button>
    </form>

  </div>
</template>