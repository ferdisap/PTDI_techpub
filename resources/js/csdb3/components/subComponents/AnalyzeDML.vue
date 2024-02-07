<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      fname: '',
      dmlEntryList: [],
      availableFile: '',
    }
  },
  props: ['filename'],
  methods: {
    getDmlEntries(name) {
      let filename = name ? name : this.filenameAnalysis;
      const route = this.techpubStore.getWebRoute('api.get_entry', { filename: filename });
      axios({
        url: route.url,
        method: route.method[0],
      })
        .then(response => this.dmlEntryList = response.data)
        .catch(error => this.$root.error(error));
    },
  },
  mounted() {
    if(this.fname != this.$props.filename){
      this.fname = this.$props.filename;
      this.getDmlEntries(this.$props.filename);
    }
  },
}
</script>
<template>
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
        Filename: {{ obj.filename }} <br/>
        Stage: {{ obj.remarks.stage }}
      </div>
    </div>
    <div class="mb-3 bg-red-500" v-else>
      The {{ availableFile }} has no object model available in database.
    </div>
  </div>
  


</template>