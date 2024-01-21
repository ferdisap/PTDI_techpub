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
  mounted(){
    const route = this.techpubStore.getWebRoute('api.get_csl_staging');
    axios({
      url: route.url,
      method: route.method[0],
    })
    .then(response => this.cslstaging = response.data)
    .catch(error => this.$root.error(error));
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
        </tr>
      </thead>
      <tbody>
        <tr v-for="csl in cslstaging">
          <td> {{ csl.filename }} </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>