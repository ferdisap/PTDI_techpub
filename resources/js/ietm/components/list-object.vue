<script>
import { useIetmStore } from '../ietmStore';
import { RouterLink } from 'vue-router';

export default {
  name: 'ListObject',
  data(){
    return {
      ietmStore: useIetmStore(),
      listDMC: useIetmStore().listDMC
    }
  },
  props: ['repoName'],
  methods: {
    async detail(filename){
      this.$router.push({name:'Detail', params: {repoName: this.$route.params.repoName, filename: filename}});
    }
  },
}
</script>

<template>
  <div class="flex justify-center mt-4" v-if="ietmStore.listDMC.length > 0">
    <div class="block">
      <h1 class="text-xl text-center">{{ repoName }}</h1>
      <br/>
      <table>
        <tr>
          <th>filename</th>
        </tr>
        <tr v-for="object in ietmStore.listDMC">
          <td>
            <!-- <button @click="detail(object.filename)"> {{ object.filename }} </button> -->
            <!-- <a :href="`/ietm/Content/${repoName}/${object.filename}`">{{ object.filename}}</a> -->
            <RouterLink :to="`/ietm/Content/${repoName}/${object.filename}`"> {{ object.filename }} </RouterLink>
          </td>
        </tr>
      </table>
    </div>
  </div>
</template>