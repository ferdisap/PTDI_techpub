<script>
import { useTechpubStore } from '../../../techpub/techpubStore';

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
    }
  },
  props: ['function'],
}
</script>
<template>
  <a href="javascript:void(0)" @click="$props.function"><span class="material-icons text-sm ">swap_vert</span></a>
</template>