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
    <div class="block mt-3 mb-4">
      <table>
        <tr>
          <th class="text-lg h-10 text-left">Filename:</th>
          <th class="text-lg h-10 text-left">Title:</th>
        </tr>
        <tr v-for="object in ietmStore.listDMC">
          <td class="text-left">
            <RouterLink :to="`/ietm/content/${repoName}/${object.filename}`"> {{ object.filename }} </RouterLink>
          </td>
          <td class="text-left">{{ object.title }}</td>
        </tr>
      </table>
    </div>
    <!-- <div>
      {{ ietmStore.listDMC }}
    </div> -->
  </div>
</template>