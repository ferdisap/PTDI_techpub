
<script>
export default {
  props: ['id'],
  methods:{
    // copy(){
    //   window.s = window.getSelection();
    //   console.log(window.getSelection().anchorNode.nodeValue)      
    // }
  }
}
</script>
<template>
  <div :id="id">
    <div class="bg-white w-60 border border-gray-300 rounded-lg flex flex-col text-sm py-4 px-2 text-gray-500 shadow-lg">
      <slot></slot>
      <!-- <div @click.stop.prevent="copy()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Copy</div>
      </div> -->

      <!-- <div class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">{{ $props.id }}</div>
      </div> -->
    </div>
  </div>
</template>