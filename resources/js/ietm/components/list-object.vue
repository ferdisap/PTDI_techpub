<script>
import { useIetmStore } from '../ietmStore';

export default {
  name: 'ListObject',
  data(){
    return {
      ietmStore: useIetmStore(),
    }
  },
  props: ['repoName', 'listDMC'],
  methods: {
    async detail(filename){
      this.$router.push({name:'Detail', params: {repoName: this.$route.params.repoName, filename: filename}});
    }
  },
}
</script>

<template>
  <div class="mt-4">
    <h1>List Object of {{ $route.params.repoName }} </h1>
    <button @click="repoList()">back</button>
  </div>
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
            <button @click="detail(object.name)"> {{ object.name }} </button>
          </td>
        </tr>
      </table>
    </div>
  </div>
</template>