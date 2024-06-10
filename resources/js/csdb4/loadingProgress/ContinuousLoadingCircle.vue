<script>
  export default {
    props:{
      show:false
    },
  }
</script>
<template>
  <div v-if="$props.show" class="top-0 left-0 h-[100vh] w-[100%] z-50 absolute">
    <div style="
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background:rgba(0,0,0,0.5);
    ">
      <div class="loading_buffer"></div>
    </div>
  </div>
</template>