<script>
import { useIetmStore } from '../ietmStore';
export default {
  data(){
    return {
      ietmStore: useIetmStore(),
    }
  },
}
</script>

<template>
  <div>
    <h1>Sidenav</h1>
    <div v-if="ietmStore.listPMC.length > 0">
      <div v-for="object in ietmStore.listPMC">
        <div> {{ object.name }} </div>
      </div>
    </div>
  </div>
</template>