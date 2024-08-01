<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
import { isString, isArray } from '../../helper';

const add = function(event){
  console.log('aaa');
}

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
    }
  },
  props: {
    class_label: {
      type: String,
      default: "text-lg font-bold"
    },
    placeholder: {
      type: String,
      default: 'eg.: this document is intended for...',
    },
    nameAttr: {
      type: String,
      default: 'remarks',
    },
    para: '',
  },
  computed:{
    remarksPara(){
      // console.log(window.p = this.$props.para);
      // return [''];
      // console.log(this.$props.para);
      let ret;
      if(isString(this.$props.para)){
        try {
          ret = JSON.parse(this.$props.para)
          ret = ret.length ? ret : ['']
        } catch (e) {
          ret = [''];
        }
      } 
      else if (isArray(this.$props.para)) ret = this.$props.para;
      else ret = [''];
      // console.log(ret);
      return ret;
    }
  },
  methods: {},
}
</script>

<template>
  <div class="RemarksVue">
    <label for="remarks" :class="['inline-block mb-2 text-gray-900 dark:text-white', $props.class_label]">Remarks:</label>
    <div class="remarks flex" v-for="p in remarksPara">
    <!-- <div class="remarks flex" v-for="p in $props.para"> -->
      <textarea :name="$props.nameAttr + '[]'" :placeholder="$props.placeholder">{{ p }}</textarea>
      <button @click.stop type="button" onclick="(() => {event.stopPropagation();const container = this.parentElement;const clonned = container.cloneNode(true);clonned.firstElementChild.value = '';container.after(clonned);})(this)" class="border text-white bg-green-500 rounded-md w-4">+</button>
      <button @click.stop type="button"
        onclick="(()=>{event.stopPropagation();(Object.values(this.parentElement.previousElementSibling.classList).includes('remarks')) ? this.parentElement.remove() : false})()"
        class="min-commentRefs border text-white bg-red-500 rounded-md w-4">-</button>
    </div>
  </div>
  <div class="text-red-600" v-html="techpubStore.error('remarks')"></div></template>