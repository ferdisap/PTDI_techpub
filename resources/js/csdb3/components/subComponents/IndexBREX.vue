<script>
import { useTechpubStore } from '../../../techpub/techpubStore';

export default {
  data(){
    return {
      techpubStore: useTechpubStore(),
    }
  },
  mounted(){
    const route = this.techpubStore.getWebRoute('api.get_brex_list');
    axios({
      url: route.url,
      method: route.method[0],
    })
    .then(response => this.techpubStore.BREXList = response.data)
    .catch(error => this.$root.error(error));
  },
}
</script>
<template>
  <div class="IndexDML">
    <h1>Index BREX</h1>
    <table>
      <thead>
        <tr>
          <th>Filename</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="brex in techpubStore.BREXList">
          <td><a :href="techpubStore.getWebRoute('',{filename: brex.filename}, $router.getRoutes().find((route) => route.name == 'DetailBREX')).path">{{ brex.filename }}</a> </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>