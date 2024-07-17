<script>
import RightClickMenu from '../RightClickMenu';
import randomstring from 'randomstring';
export default {
  data(){
    return{
      name: randomstring.generate({charset:'alphabetic'}),
    }
  },
  mounted(){
    let menu = document.querySelector('.'+this.name);
    new RightClickMenu(this.name + ' right click menu', menu, menu.parentElement);
  }
}
</script>
<template>
  <div :class="name" style="position:fixed;z-index:100">
  <!-- <div :class="[$props.name]"> -->
    <div class="bg-white w-60 border border-gray-300 rounded-lg flex flex-col text-sm py-4 px-2 text-gray-500 shadow-lg">
      <slot></slot>
    </div>
  </div>
</template>