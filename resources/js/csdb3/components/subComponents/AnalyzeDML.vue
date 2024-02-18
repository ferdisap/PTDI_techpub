<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      fname: '',
      dmlEntryList: undefined,
      availableFile: '',
    }
  },
  props: ['filename'],
  computed:{
    async getDmlEntries(){
      console.log(this.$props.filename);
      let response = await axios({
        route: {
          name: 'api.get_dmlentry',
          data: {filename: this.$props.filename}
        }
      });
      if(response.statusText === 'OK'){
        this.dmlEntryList = response.data;
      }
    }
  },
}
</script>
<template>
  <div v-show="false">{{ getDmlEntries }}</div>
  
  <div v-if="dmlEntryList">
    <h1>Detail</h1>
    <table>
      <thead>
        <tr>
          <th>Entry</th>
          <th>Type</th>
          <th>Issue Type</th>
          <th>Responsible Company</th>
        </tr>
      </thead>
      <tbody>
        <tr :id="entry.code" v-for="entry in dmlEntryList">
          <td><a href="#" @click="availableFile = entry.code">{{ entry.code }}</a></td>
          <td>{{ entry.dmlEntryType || '-' }}</td>
          <td>{{ entry.issueType || '-' }}</td>
          <td>{{ entry.responsiblePartnerCompany.enterpriseName || entry.responsiblePartnerCompany.enterpriseCode }} </td>
        </tr>
      </tbody>
    </table>
  
    <!-- available file -->
    <div v-if="availableFile" class="mt-5">
      <div class="mb-3" 
      v-if="Object.values(dmlEntryList).find(o => o.code == availableFile)['objects'].length"
      v-for="obj in Object.values(dmlEntryList).find(o => o.code == availableFile)['objects']">
        <div v-if="!obj.length">
          Filename: <a :href="techpubStore.getWebRoute('',{filename: obj.filename}, $router.getRoutes().find((route) => route.name == 'DetailObject')).path">{{ obj.filename }}</a> <br/>
          Stage: {{ obj.remarks.stage }} <br/>
          Initiator: {{ obj.initiator.name }}
        </div>
      </div>
      <div class="mb-3 bg-red-500" v-else>
        The {{ availableFile }} has no object model available in database.
      </div>
    </div>

  </div>
  


</template>