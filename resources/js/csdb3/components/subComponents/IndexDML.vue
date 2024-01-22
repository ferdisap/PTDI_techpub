<script>
import { useTechpubStore } from '../../../techpub/techpubStore';

export default {
  data(){
    return {
      techpubStore: useTechpubStore(),
    }
  },
  props:['isInEditing'],
  mounted(){
    const route = this.techpubStore.getWebRoute('api.get_dml_list');
    axios({
      url: route.url,
      method: route.method[0],
    })
    .then(response => this.techpubStore.DMLList = response.data)
    .catch(error => this.$root.error(error));
  },
}
</script>
<template>
  <div class="IndexDML">
    <h1>Index DML</h1>
    <div class="flex">
      
      <table class="w-1/2 table-cell bg-red-500" v-if="!$props.isInEditing">
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

      <table class="w-1/2 table-cell bg-lime-500">
        <h3>Uneditable DML</h3>
        <thead class="h-10">
          <tr>
            <th>Filename</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="dml in techpubStore.DMLList">
            <td v-if="!dml.editable && ($props.isInEditing)">
              <a :href="techpubStore.getWebRoute('',{filename: dml.filename}, Object.assign({},$router.getRoutes().find((route) => route.name == 'DetailDMLEDIT'))).path">{{ dml.filename }}</a>
            </td>
            <td v-else-if="!dml.editable && (!$props.isInEditing)">
              <a :href="techpubStore.getWebRoute('',{filename: dml.filename}, Object.assign({},$router.getRoutes().find((route) => route.name == 'DetailDML'))).path">{{ dml.filename }}</a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>