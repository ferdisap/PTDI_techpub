<script>
import axios from 'axios';
import { useTechpubStore } from '../../techpub/techpubStore';
import IndexCSDB from '../components/subComponents/IndexCSDB.vue';
import AnalyzeDML from '../components/subComponents/AnalyzeDML.vue';

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      cslstaging: [],
      filenameAnalysis: undefined
    }
  },
  components: { IndexCSDB,  AnalyzeDML},
  computed:{
    getFilename(){
      return this.filenameAnalysis;
    }
  },
  methods: {
    async accept(filename) {
      let eventTarget = event.target;
      let response = await axios({
        route: {
          name: 'api.accept_dml',
          data: { filename: filename },
        }
      })
      if(response.statusText === 'OK'){
        $(eventTarget).parents('tr').eq(0).remove();
      }
    },
    async decline(filename) {
      let eventTarget = event.target;
      let response = await axios({
        route: {
          name: 'api.decline_csl_forstaging',
          data: { filename: filename },
        }
      })
      if(response.statusText === 'OK'){
        $(eventTarget).parents('tr').eq(0).remove();
      }
    },
    edit(dmlFilename) {

    },
    analyzeCSLStaging(filename){
      // show the detail of CSL staging, what any entry inside the CSL
      this.filenameAnalysis = filename
    }
  },
  mounted() {
    this.emitter.on('api.push_csl_forstaging', (data) => {
      this.techpubStore.get_list('csl_staging');
    });
  },
}

</script>
<template>
  <IndexCSDB type="csl_staging" :clickFilename="analyzeCSLStaging">
    <template #title>Index CSL:Staging</template>
    <template #actionColumn="actionColumnProps">
      <button @click="accept(actionColumnProps.filename)" class="has-tooltip-arrow" data-tooltip="Accept"><span
        class="material-icons text-green-400">check_circle</span></button>
      <button @click="decline(actionColumnProps.filename)" class="has-tooltip-arrow" data-tooltip="Cancel"><span
          class="material-icons text-red-500">cancel</span></button>
          <a class="material-icons text-blue-600 has-tooltip-arrow" data-tooltip="Detail"
          :href="techpubStore.getWebRoute('', { filename: actionColumnProps.filename }, Object.assign({}, $router.getRoutes().find((route) => route.name == 'DetailDML'))).path">details</a>
    </template>
  </IndexCSDB>

  <!-- <component v-if="filenameAnalysis" is="AnalyzeDML" :filename="filenameAnalysis"/> -->
  <AnalyzeDML v-if="filenameAnalysis" :filename="filenameAnalysis" />
  <!-- <AnalyzeDML :filename="filenameAnalysis" /> -->


  <!-- STAGING OBJECT
  <div>
    <table>
      <thead>
        <tr>
          <th>CSL filename</th>
          <th>SC</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody v-for="csl in cslstaging">
        <tr>
          <td> <a href="javascript:void(0)" @click="getDmlEntries(csl.filename);csl.show = !csl.show">{{ csl.filename }}</a></td>
          <td> {{ csl.remarks.securityClassification }} </td>
          <td> 
            <button @click="accept(csl.filename)"  class="has-tooltip-arrow" data-tooltip="Accept"><span class="material-icons text-green-400">check_circle</span></button>
            <button @click="decline(csl.filename)" class="has-tooltip-arrow" data-tooltip="Cancel"><span class="material-icons text-red-500">cancel</span></button>
            <a class="material-icons text-blue-600 has-tooltip-arrow" data-tooltip="Detail" :href="techpubStore.getWebRoute('',{filename: csl.filename}, Object.assign({},$router.getRoutes().find((route) => route.name == 'DetailDML'))).path">details</a>
            
          </td>
        </tr>
        <tr v-if="csl.dmlEntries" v-show="csl.show">
          <td colspan="3">
            <table>
              <tr v-for="entry in csl.dmlEntries" :entryposition="entry.position" :cslowner="csl.filename">
                <td> {{ entry.issueType }} </td>
                <td> {{ entry.code }}{{ entry.extension }}</td>
                <td> {{ entry.security.securityClassification }} </td>
                <td> {{ entry.responsiblePartnerCompany.enterpriseName }} </td>
                <td> {{ entry.responsiblePartnerCompany.enterpriseCode }} </td>
                <td> {{ entry.remark }} </td>
                <td> Available object: 
                  <table>
                    <tr v-for="object in entry.objects">
                      <td>
                        <a target="_blank" :href="techpubStore.getWebRoute('',{filename: object.filename},Object.assign({}, $router.getRoutes().find(v => v.name == 'DetailObject')))['path']"> {{ object.filename }} </a>
                      </td>
                      <td>
                        Stage: {{ object.remarks.stage }}
                      </td>
                    </tr>                      
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
  </div> --></template>