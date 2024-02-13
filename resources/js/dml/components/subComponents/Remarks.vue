<script>
import { useTechpubStore } from '../../../techpub/techpubStore';

export default {
  data(){
    return {
      techpubStore: useTechpubStore(),
    }
  },
  methods:{
    addRemarks(){
      let parent = $(event.target).parents('div').eq(0);
      let input = parent.children("input[name='remarks[]']").eq(0).clone();
      input.val('');
      parent.append(input);
    },
  },
}
</script>
<template>
  <br/>
  <div>
    <label for="remarks">Remarks <button @click.prevent="addRemarks()">+</button></label>
    <input type="text" value="" name="remarks[]" placeholder="eg.: this DML is intended for..." />
    <!-- <textarea name="remarks[]">foo</textarea> -->
  </div>    
  <div class="text-red-600" v-html="techpubStore.error('remarks')"></div>
</template>