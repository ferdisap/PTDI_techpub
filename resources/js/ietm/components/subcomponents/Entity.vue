<script>
import { useIetmStore } from '../../ietmStore';

export default {
  data(){
    return {
      ietmStore: useIetmStore(),
    }
  },
}
</script>
<template>
  <div class="">
    <img :src="ietmStore.entity.filename"/>
  </div>
</template>