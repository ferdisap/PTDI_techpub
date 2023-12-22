<script>
// import axios from 'axios';
import { useIetmStore } from '../ietmStore';
import Dropdown_pmEntry from './subcomponents/dropdown_pmEntry.vue';
import { reactive } from 'vue';
export default {
  data() {
    return {
      ietmStore: useIetmStore(),
      listPMC: useIetmStore().listPMC,
      data: reactive({}),
      open: {},
    };
  },
  components: { Dropdown_pmEntry },
  methods: {
  },
}
</script>

<template>
  <!-- <div class="bg-white text-black h-screen overflow-x-auto overflow-y-auto whitespace-nowrap py-3 w-100"> -->
  <div class="bg-white text-black h-screen text-left py-3 w-100">
    
    <!-- OPERATION MANUAL -->
    <div class="block mx-2 mb-5 text-left">
      <button class="text-xl font-bold hover:bg-sky-300" @click="open['om'] = !open['om']">OPERATION MANUAL</button>
      <hr/>
      <div v-if="ietmStore.listPMC.length > 0" v-for="object in ietmStore.listPMC" style="text-align:left;" v-show="open['om']">
        <Dropdown_pmEntry :title="object.title" :filename="object.filename" :pt="object.pt" v-if="(object.pt || object.pt != '') && object.pt.substr(2) > 50"/>
      </div>
    </div>

    <!-- MAINTENANCE MANUAL -->
    <div class="block mx-2 mb-5 text-left">
      <button class="text-xl font-bold hover:bg-sky-300" @click="open['mm'] = !open['mm']">MAINTENANCE MANUAL</button>
      <hr>
      <div v-if="ietmStore.listPMC.length > 0" v-for="object in ietmStore.listPMC" style="text-align:left;" v-show="open['mm']">
        <Dropdown_pmEntry :title="object.title" :filename="object.filename" :pt="object.pt" v-if="(object.pt || object.pt != '') && object.pt.substr(2) > 60"/>
      </div>
    </div>
  </div>
</template>
