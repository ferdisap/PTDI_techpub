<script>
export default {
  data(){
    return {}
  },
  props: {
    items: {
      type: Object,
      defaults: {
        componentName: {
          iconName: '',
          tooltipName: '',
          isShow: '',
        }
      },
    },
    class: String,
  },
  methods:{
    click(componentName){
      this.$props.items[componentName]['isShow'] = !this.$props.items[componentName]['isShow'];
    }
  },
}
</script>

<template>
  <div :class="['absolute flex h-max w-max  space-x-2 px-2 py-1 border-4 border-blue-500 bg-white rounded-xl', $props.class]">
    <div v-for="(item, componentName) in $props.items" :class="['relative h-max w-max flex items-center rounded-lg', item.isShow ? 'bg-blue-500' : 'bg-white']">
      <a @click="click(componentName)" href="#" :class="['material-symbols-outlined bg-transparent  p-2 rounded-md has-tooltip-arrow', item.isShow ? 'text-white': 'text-blue-500']"
        :data-tooltip="item.tooltipName">{{ item.iconName }}</a>
    </div>
  </div>
</template>