<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
import { isString, isArray } from '../../helper';
import Randomstring from "randomstring";
import TextEditor from '../../TextEditor';
import Rm from "../../element/Rm.js" ;

// const add = function (event) {
//   console.log('aaa');
// }

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      remarkElementName: 'rm-el',
      remarkId: Randomstring.generate({ charset: 'alphabetic' }),
      remarksEditor: {},
      // remarksEditorId: Randomstring.generate({ charset: 'alphabetic' }),
    }
  },
  props: {
    class_label: {
      type: String,
      default: "text-lg font-bold"
    },
    class_textarea: { // not applicable
      type: String,
    },
    class: { // not applicable
      type: String,
    },
    placeholder: {
      type: String, // not applicable
      default: 'eg.: this document is intended for...',
    },
    nameAttr: {
      type: String,
      default: 'remarks',
    },
    modalInputName: {
      type: String
    },
    para: '',
  },
  computed: {
    remarksPara() {
      if(this.$props.para){
        const rm = document.querySelector('#'+this.remarkId + ' ' + this.remarkElementName);
        if(rm) rm.value = this.$props.para;
      }
      return;
    }
  },
  methods: {
    getPara(){
      let ret;
      if (isString(this.$props.para)) {
        try {
          ret = JSON.parse(this.$props.para)
          ret = ret.length ? ret : ['']
        } catch (e) {
          ret = [''];
        }
      }
      else if (isArray(this.$props.para)) ret = this.$props.para;
      else ret = [''];
      return ret;
    }
  },
  mounted() {
    const editor = new TextEditor(this.remarkId);
    editor.attachEditor();
    
    // check if remark-editor has defined else define
    customElements.get(this.remarkElementName) || customElements.define(this.remarkElementName, Rm);    
    const rm = document.createElement(this.remarkElementName);
    rm.editor = editor;
    rm.name = this.$props.nameAttr;    
    rm.setAttribute('name', this.$props.nameAttr);
    rm.style.display = 'none';    
    if(this.$props.modalInputName) rm.setAttribute('modal-input-name', this.$props.modalInputName);
    document.getElementById(this.remarkId).appendChild(rm);
  },
}
</script>


<template>
  <div class="RemarksVue">
    {{ remarksPara }}
    <label for="remarks" :class="['inline-block mb-2 text-gray-900 dark:text-white', $props.class_label]">Remarks:</label>
    <div :id="remarkId" :class="$props.class">
    </div>
    <div class="text-red-600" v-html="techpubStore.error('remarks')"></div>
  </div>
</template>