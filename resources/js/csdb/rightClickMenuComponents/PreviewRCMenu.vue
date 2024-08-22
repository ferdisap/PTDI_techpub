<!-- DEPRECIATED -->
<script>
import RightClickMenu from '../RightClickMenu';
export default {
  mounted(){
    let menu = document.querySelector('.preview-rcmenu');
    new RightClickMenu('preview right click menu', menu, menu.parentElement);
  }
}
</script>
<style>
  .preview-rcmenu{
    position: fixed;
    z-index: 100;
  }
</style>
<template>
  <div class="preview-rcmenu">
    <div class="bg-white w-60 border border-gray-300 rounded-lg flex flex-col text-sm py-4 px-2 text-gray-500 shadow-lg">
      <!-- slot template below -->
      <!-- <div class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="w-5 text-gray-900">
          logo here
        </div>
        <div class="block">
          tes
        </div>
      </div> -->
      <slot></slot>
    </div>
  </div>
</template>