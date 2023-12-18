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
  <div class="dump_red">
    FOOBARR
  </div>
</template>