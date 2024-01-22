<script>
import axios from 'axios';
import { useTechpubStore } from '../../../techpub/techpubStore';
import Remarks from '../subComponents/Remarks.vue';

export default {
  data(){
    return {
      techpubStore: useTechpubStore(),
    }
  },
  props:['dmlfilename'],
  components:{Remarks},
  methods: {
    submit(){
      const formData = new FormData(event.target);
      // let project_name = this.techpubStore.dml(formData.get('filename'));
      // formData.append('project_name', project_name);
      const route = this.techpubStore.getWebRoute('api.addEntry_dml',formData);
      
      axios({
        url: route.url,
        method: route.method[0],
        data: formData,
      })
      .then(response => this.$root.success(response))
      .catch(error => this.$root.error(error));
    },
  },
  mounted(){
    const route = this.techpubStore.getWebRoute('api.get_dml_list');
    axios({
      url: route.url,
      method: route.method[0],
    })
    .then(response => this.techpubStore.DMLList = response.data)
    .catch(error => this.$root.error(error));
  },
}
</script>
<template>
  <h1>Add DML Entry</h1>

  <form @submit.prevent="submit()">

    <!-- Select DML file -->
    <label for="filename">DML Filename</label>
    <input name="filename" :value="$props.dmlfilename ?? ''"/>

    <!-- issueType -->
    <label for="issueType">Issue Type</label>
    <select name="issueType" id="issueType">
      <option value="">--select issueType--</option>
      <option value="new">New</option>
      <option value="changed">Changed</option>
      <option value="deleted">Deleted</option>
      <option value="revised">Revised</option>
      <option value="status">Status</option>
      <option value="rinstate-changed">Reinstate-Changed</option>
      <option value="rinstate-revised">Reinstate-Revised</option>
      <option value="rinstate-status">Reinstate-Status</option>
    </select>
    <div class="text-red-600" v-html="techpubStore.error('issueType')"></div>

    <!-- dmlEntryType -->
    <label for="dmlEntryType">Entry Type</label>
    <select name="dmlEntryType" id="dmlEntryType">
      <option value="">--select dmlEntryType--</option>
      <option value="new">New</option>
      <option value="changed">Changed</option>
      <option value="deleted">Deleted</option>
    </select>
    <div class="text-red-600" v-html="techpubStore.error('dmlEntryType')"></div>
  
    <!-- dmlEntry -->
    <label for="entryIdent">Entry Ident</label>
    <input name="entryIdent" id="entryIdent" value="" placeholder="DMC, PMC, ICN, Comment, DML"/>
  
    <!-- securityLevel -->
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
    
    <!-- responsiblePartnerCompany -->
    <label for="enterpriseName">Responsible Partner Company</label>
    <input type="text" value="" name="enterpriseName" id="enterpriseName" placeholder="eg.: PT. Dirgantara Indonesia" />
    <div class="text-red-600" v-html="techpubStore.error('enterpriseName')"></div>

    <input type="text" value="" name="enterpriseCode" id="enterpriseCode" placeholder="eg.: 0001Z" />
    <div class="text-red-600" v-html="techpubStore.error('enterpriseCode')"></div>
  
    <!-- untuk answer -->
  
    <!-- untuk remarks -->
    <Remarks/>
      
    <button type="submit" class="button-violet">Submit</button>
  </form>

  
</template>