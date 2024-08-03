<script>
export default {
  props: ['id'],
}
</script>
<template>
  <div :id="id">
    <div class="bg-white w-60 border border-gray-300 rounded-lg flex flex-col text-sm py-4 px-2 text-gray-500 shadow-lg">
      <slot></slot>
    </div>
  </div>
</template>