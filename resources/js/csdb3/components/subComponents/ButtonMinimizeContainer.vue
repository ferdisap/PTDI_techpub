<!-- syaratnya, ini harus ditempatkan di relativ position container -->
<script>
export default {
  data(){
    return {}
  },
  props: {
    variable: {
      type: String,
      required: true
    },
    direction: {
      type: String,
      default: 'right'
    },
    class:{
      type: String,
      default: 'right-0'
    },
  },
  computed:{
    click(){
      this.$parent[this.$props.variable] = !this.$parent[this.$props.variable]
    },
    icon(){
      switch (this.$props.direction) {
        case 'left':
          return this.$parent[this.$props.variable] ? 'chevron_left' : 'chevron_right';
          break;
        default:
          return this.$parent[this.$props.variable] ? 'chevron_right' : 'chevron_left';
          break;
      }
    }
  },
}
</script>
<template>
  <div :class="[ $props.class ,'rounded-full bg-gray-600 opacity-50 hover:bg-black hover:opacity-100 absolute t-0']">
    <button @click="click" class="material-symbols-outlined float-right">{{ icon }}</button>
  </div>
</template>