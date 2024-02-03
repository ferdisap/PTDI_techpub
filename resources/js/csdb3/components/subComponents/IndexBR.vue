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
    const route = this.techpubStore.getWebRoute('api.get_list');
    axios({
      url: route.url,
      method: route.method[0],
    })
    .then(response => {
      response.data.forEach(obj => {
        if(obj.filename.includes("-022")){
          this.techpubStore.BREXList.push(obj);
        }
        else if(obj.filename.includes("-024")){
          this.techpubStore.BRDPList.push(obj);
        }
      })
    })
    .catch(error => this.$root.error(error));
  },
}
</script>
<template>
  <!-- <div class="mb-3"> -->
    <!-- <button @click="(showBREX = (showBREX == 'createBREX' ? !showBREX : 'createBREX'))" :class="[showBREX == 'createBREX' ? 'border-b-black border-b-4' : '' ,'button-nav']">Create BREX</button> -->
    <!-- <CreateBREX v-if="showBREX == 'createBREX'"/> -->
    <!-- <Editor v-if="showBREX == 'createBREX'"/> -->
  <!-- </div> -->

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
          <td><a :href="techpubStore.getWebRoute('',{filename: brex.filename}, $router.getRoutes().find((route) => route.name == 'DetailObject')).path">{{ brex.filename }}</a> </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="IndexBRDP">
    <h1>Index BRDP</h1>
    <table>
      <thead>
        <tr>
          <th>Filename</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="brdp in techpubStore.BRDPList">
          <td><a :href="techpubStore.getWebRoute('',{filename: brdp.filename}, $router.getRoutes().find((route) => route.name == 'DetailObject')).path">{{ brdp.filename }}</a> </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>