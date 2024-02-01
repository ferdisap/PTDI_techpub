<script>
import { useTechpubStore } from '../../../techpub/techpubStore';

export default {
  data(){
    return {
      techpubStore: useTechpubStore(),
    }
  },
  mounted(){
    const route = this.techpubStore.getWebRoute('api.get_objects');
    axios({
      url: route.url,
      method: route.method[0],
    })
    .then(response => this.techpubStore.OBJECTList = response.data)
    .catch(error => this.$root.error(error));
  },
}
</script>
<template>
  <h1>Index Object</h1>
  <table class="w-1/2 table-cell">
    <h3>Editable DML</h3>
    <thead class="h-10">
      <tr>
        <th>Filename</th>
        <th>Stage | Editable</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="obj in techpubStore.OBJECTList">
        <td>
          <a :href="techpubStore.getWebRoute('',{filename: obj.filename}, Object.assign({},$router.getRoutes().find((route) => route.name == 'DetailObject'))).path">{{ obj.filename }}</a>
        </td>
        <td>
          {{ obj.remarks.stage }} | {{ obj.editable ? 'yes' : 'no' }}
        </td>
      </tr>
    </tbody>
  </table>
  
  <!-- <div class="IndexDML">
    <h1>Index DML</h1>
    <div class="flex">
      <table class="w-1/2 table-cell bg-red-500" v-if="!$props.only_uneditable || $props.both">
        <h3>Editable DML</h3>
        <thead class="h-10">
          <tr>
            <th>Filename</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="dml in techpubStore.DMLList">
            <td v-if="dml.editable">
              <a :href="techpubStore.getWebRoute('',{filename: dml.filename}, Object.assign({},$router.getRoutes().find((route) => route.name == 'DetailDML'))).path">{{ dml.filename }}</a>
            </td>
          </tr>
        </tbody>
      </table>
      <table class="w-1/2 table-cell bg-lime-500" v-if="$props.only_uneditable || $props.both">
        <h3>Uneditable DML</h3>
        <thead class="h-10">
          <tr>
            <th>Filename</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="dml in techpubStore.DMLList">
            <td v-if="!dml.editable">
              <a :href="techpubStore.getWebRoute('',{filename: dml.filename}, Object.assign({},$router.getRoutes().find((route) => route.name == 'DetailDML'))).path">{{ dml.filename }}</a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div> -->
</template>