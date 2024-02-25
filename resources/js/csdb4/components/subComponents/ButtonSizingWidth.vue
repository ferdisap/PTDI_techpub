<!-- syaratnya, ini harus ditempatkan di relativ position container -->
<script>
export default {
  data(){
    return {
      isFirst: true
    }
  },
  props: {
    fn: {
      type: Function,
      required: true,
    },
    direction: {
      type: String,
      default: 'right' // left or right
    },
    class:{
      type: String,
      default: 'right-0'
    },
  },
  computed:{
    click(){
      this.isFirst = !this.isFirst;
      return this.$props.fn;
    },
    icon(){
      let direction = this.isFirst ? this.$props.direction : this.flipDirection(this.$props.direction);
      return this.getIcon(direction);      
    }
  },
  methods:{
    flipDirection(direction){
      switch (direction) {
        case 'left':
          return 'right';
        case 'right':
          return 'left';
        default:
          return direction;
          break;
      }
    },
    getIcon(direction){
      switch (direction) {
        case 'left':
          return 'chevron_left'
        case 'right':
          return 'chevron_right';
        default:
          return '';
      }
    }
  },
}
</script>
<template>
  <div :class="[ $props.class ,'text-white rounded-full bg-gray-300 opacity-50 hover:bg-black hover:opacity-100 absolute t-0']">
    <button @click.prevent="click()" class="material-symbols-outlined float-right text-xs">{{ icon }}</button>
  </div>
</template>