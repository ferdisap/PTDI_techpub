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
    },
    decline(filename){
      this.$root.showMessages = false;
      const route = this.techpubStore.getWebRoute('api.decline_csl_forstaging', { filename: filename });
      axios({
      url: route.url,
      method: route.method[0],
      })
        .then(response => this.$root.success(response))
        .catch(error => this.$root.error(error));
    },
    edit(dmlFilename){},
  },
  mounted(){
    const route = this.techpubStore.getWebRoute('api.get_csl_staging');
    axios({
      url: route.url,
      method: route.method[0],
    })
    .then(response => this.cslstaging = response.data)
    .catch(error => this.$root.error(error));

    window.router = this.$router;
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
            <button @click="accept(csl.filename)"><span class="material-icons text-green-400">check_circle</span>Accept</button>
            <button @click="decline(csl.filename)"><span class="material-icons text-red-500">cancel</span>Decline</button>
            <button @click="edit(csl.filename)"><span class="material-icons text-black">edit</span>Edit</button>
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