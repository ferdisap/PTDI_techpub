<!-- 
  DEPRECATED,
  tidak dipakai lagi
 -->
<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
import CommentField from './CommentField.vue';
export default {
  components: {CommentField},
  data(){
    return {}
  },
  methods: {
  },
  mounted() {    
  },
}

</script>
<template>
  <div class="Preview overflow-auto h-[93%] w-full relative">
    
    <div class="h-[5%] mb-3 px-3">
      <h1 class="text-blue-500 w-full text-center">Comment</h1>

      <div class="mb-3">
        <CommentField/>
      </div>

  </div>
</div>
</template>