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
      let container = $('#remarks-container');
      let textarea = container.children('textarea').eq(0).clone();
      textarea.val('');
      container.append(textarea);
    },
  },
}
</script>
<template>
  <br/>
  <div>
    <label for="remarks" class="inline-block mb-2 text-gray-900 dark:text-white text-lg font-bold">Remarks</label>
    <div class="flex flex-wrap" id="remarks-container">
      <textarea name="remarks[]" placeholder="eg.: this DML is intended for..." class="ml-3 w-96"></textarea>
    </div>
    <button @click.prevent="addRemarks()" class="text-sm my-3">Add more+</button>
  </div>    
  <div class="text-red-600" v-html="techpubStore.error('remarks')"></div>
</template>