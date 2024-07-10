<script>
import { useTechpubStore } from '../../../techpub/techpubStore';

export default {
  data(){
    return {
      techpubStore: useTechpubStore(),
    }
  },
  props:{
    class_label: {
      type: String,
      default: "text-lg font-bold"
    },
    placeholder: {
      type: String,
      default: 'eg.: this document is intended for...',
    }
  },
  methods:{},
}
</script>
<template>
  <br/>
  <div>
    <label for="remarks" :class="['inline-block mb-2 text-gray-900 dark:text-white', $props.class_label]">Remarks:</label>
    <div class="remarks flex flex-wrap">
      <textarea name="remarks[]" :placeholder="$props.placeholder" class="ml-3 w-96"></textarea>
      <button onclick="(() => {
        let container = this.parentElement;
        let clonned = container.cloneNode(true);
        clonned.firstElementChild.value = '';
        container.after(clonned);
        })(this)" class="border text-white bg-green-500 rounded-md w-4">+</button>
      <button onclick="(()=>(Object.values(this.parentElement.previousElementSibling.classList).includes('remarks')) ? this.parentElement.remove() : false)()" class="min-commentRefs border text-white bg-red-500 rounded-md w-4">-</button>
    </div>
  </div>    
  <div class="text-red-600" v-html="techpubStore.error('remarks')"></div>
</template>