<script>
import Modal from '../../Modal.js';

export default {
  data() {
    return {
      
    }
  },
  props: {
    id: {type: String},
  },
  methods: {
    async start(){
      this.Modal = new Modal('modal_dmlEntry_form',document.querySelector(".dmlEntry"));
    },
    ok(){
      this.Modal.button(true);
    },
    cancel(){
      this.Modal.button(false);
    }
  },
  mounted() {
    this.Dropdown.register(this.$props.id);
    this.Modal.register(this.$props.id);
  },
}
</script>
<template>
  <div :id="$props.id" v-show="Modal.getShow($props.id)" class="absolute top-[25%] w-[80%] h-max-[70%] left-[10%] border-8 border-black rounded-lg p-8 bg-slate-200">
    
    <slot name="title">
      <h1 class="text-center font-bold mb-2">Modal</h1>
    </slot>
    
    <slot/>


    <slot name="button_ok">
      <button modal-button="ok" type="button" class="button bg-violet-400 text-white hover:bg-violet-600">OK</button>
    </slot>
    <slot name="button_not_ok">
      <button modal-button="not_ok" type="button" class="button bg-red-400 text-white hover:bg-red-600">Cancel</button>
    </slot>
  </div>
</template>