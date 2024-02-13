<script>
import axios from 'axios';
import { useTechpubStore } from '../../techpub/techpubStore';

export default {
  data(){
    return {
      techpubStore: useTechpubStore(),
      cslstaging: [],
    }
  },
  methods:{
    get_csl_list(){
      const route = this.techpubStore.getWebRoute('api.get_csl_staging');
      axios({
        url: route.url,
        method: route.method[0],
      })
      .then(response => this.cslstaging = response.data)
      .catch(error => this.$root.error(error));
    },
    getDmlEntries(filename) {
      this.$root.showMessages = false;
      const route = this.techpubStore.getWebRoute('api.get_entry', { filename: filename });
      axios({
        url: route.url,
        method: route.method[0],
      })
      .then(response => this.cslstaging.find(csl_models => (csl_models.filename == filename))['dmlEntries'] = response.data)
      .catch(error => this.$root.error(error));
    },
    accept(filename){      
      const route = this.techpubStore.getWebRoute('api.accept_dml', { filename: filename });
      axios({
      url: route.url,
      method: route.method[0],
      })
        .then(response => {
          this.$root.success(response);
          let index = this.cslstaging.indexOf(this.cslstaging.find(csl => csl.filename == filename))
          this.cslstaging.splice(index,1);
        })
        .catch(error => this.$root.error(error));
    },
    decline(filename){
      this.$root.showMessages = false;
      const route = this.techpubStore.getWebRoute('api.decline_csl_forstaging', { filename: filename });
      axios({
      url: route.url,
      method: route.method[0],
      })
        .then(response => {
          this.$root.success(response);
          let index = this.cslstaging.indexOf(this.cslstaging.find(csl => csl.filename == filename))
          this.cslstaging.splice(index,1);
          this.emitter.emit('decline_csl_forstaging');
        })
        .catch(error => this.$root.error(error));
    },
    edit(dmlFilename){

    },
  },
  mounted(){
    this.emitter.on('csl_staging_pushed', this.get_csl_list);
    this.get_csl_list();
  },
}

</script>
<template>
  STAGING OBJECT
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
  </div>
</template>