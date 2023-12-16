<script>
import axios from 'axios';
import { useIetmStore } from '../ietmStore';
import Dropdown_pmEntry from './subcomponents/dropdown_pmEntry.vue';
import { reactive } from 'vue';
export default {
  data() {
    return {
      ietmStore: useIetmStore(),
      listPMC: useIetmStore().listPMC,
      data: reactive({}),
    };
  },
  components: { Dropdown_pmEntry },
  props: {filename: String},
  methods: {
  },
}
</script>

<template>
  <div class="bg-zinc-500 text-neutral-200 h-screen">
    <h1 class="text-2xl font-bold my-3">Sidenav</h1>
    
    <!-- OPERATION MANUAL -->
    <div class="block mx-2 mb-5 text-start">
      <button class="text-xl" @click="data[filename+'_OPERATION MANUAL'] = !data[filename+'_OPERATION MANUAL']">OPERATION MANUAL</button>
      <hr/>
      <div v-if="ietmStore.listPMC.length > 0" v-for="object in ietmStore.listPMC" style="text-align:left;" v-show="data[filename+'_OPERATION MANUAL']">
        <Dropdown_pmEntry :title="object.title" :filename="object.filename" :pt="object.pt" v-if="(object.pt || object.pt != '') && object.pt.substr(2) > 50"/>
      </div>
    </div>

    <!-- MAINTENANCE MANUAL -->
    <!-- <div class="block mx-2 mb-5">
      <button class="text-xl" @click="data[filename+'_MAINTENANCE MANUAL'] = !data[filename+'_MAINTENANCE MANUAL']">MAINTENANCE MANUAL</button>
      <hr>
      <div v-if="ietmStore.listPMC.length > 0" v-for="object in ietmStore.listPMC" style="text-align:left;" v-show="data[filename+'_MAINTENANCE MANUAL']">
        <Dropdown_pmEntry :title="object.title" :filename="object.filename" :pt="object.pt" v-if="(object.pt || object.pt != '') && object.pt.substr(2) > 60"/>
      </div>
    </div> -->
  </div>
</template>
