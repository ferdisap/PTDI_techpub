<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
import Editor from './Editor.vue';
export default {
  data(){
    return {
      techpubStore: useTechpubStore(),
      showBREX: '',
    }
  },
  components: { Editor },
  mounted(){
    window.techpubStore = this.techpubStore;
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
  <div class="mb-3">
    <button @click="(showBREX = (showBREX == 'createBREX' ? !showBREX : 'createBREX'))" :class="[showBREX == 'createBREX' ? 'border-b-black border-b-4' : '' ,'button-nav']">Create BREX</button>
    <!-- <CreateBREX v-if="showBREX == 'createBREX'"/> -->
    <Editor v-if="showBREX == 'createBREX'"/>
  </div>

  <div class="IndexBREX">
    <h1>Index BREX</h1>
    <table>
      <thead>
        <tr>
          <th>Filename</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="brex in techpubStore.BREXList">
          <!-- <td><a :href="techpubStore.getWebRoute('',{filename: brex.filename}, $router.getRoutes().find((route) => route.name == 'DetailBREX')).path">{{ brex.filename }}</a> </td> -->
          <!-- <td><a :href="techpubStore.getWebRoute('',{filename: brex.filename}, Object.assign({},$router.getRoutes().find((route) => route.name == 'DetailObject'))).path">{{ brex.filename }}</a> </td> -->
          <!-- <td> {{ brex.filename }} </td> -->
          <!-- <td> {{ brex.filename }} </td> -->
          <td><a :href="techpubStore.getWebRoute('',{filename: brex.filename}, $router.getRoutes().find((route) => route.name == 'DetailObject')).path">{{ brex.filename }}</a> </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>