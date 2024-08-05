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

      let bbi = this.$route.query.bbi;
      // jika ada bbi
      if(bbi){
        // jika component showed
        if(this.$props.items[componentName]['isShow']){
          // jika di bbi tidak ada componentName maka ditambahkan
          if(!bbi.includes(componentName)) bbi += (',' + componentName);
        } 
        // jika component unShowed
        else {
          // jika di bbi ada componentName maka di replace
          if(bbi.includes(componentName)) bbi = bbi.replace(new RegExp(componentName+',?'), '');
        }
      } 
      // jika tidak ada bbi
      else {
        bbi = componentName
      }
      console.log(bbi);
      this.$router.replace({
        path: this.$route.path,
        query: {
          bbi: bbi,
        }
      })
    }
  },
  activated(){
    let bbi = this.$route.query.bbi;
    if(!bbi) return;
    bbi = bbi.split(",");
    for (let i = 0; i < bbi.length; i++) {
      this.$props.items[bbi[i]]['isShow'] = true;
    }
  }

}
</script>

<template>
  <div :class="['mt-2 absolute flex h-max w-max  space-x-2 px-2 py-1 border-4 border-blue-500 bg-white rounded-xl', $props.class]">
    <div v-for="(item, componentName) in $props.items" :class="['relative h-max w-max flex items-center rounded-lg', item.isShow ? 'bg-blue-500' : 'bg-white']">
      <a @click.stop.prevent="click(componentName)" href="#" :class="['material-symbols-outlined bg-transparent  p-2 rounded-md has-tooltip-arrow', item.isShow ? 'text-white': 'text-blue-500']"
        :data-tooltip="item.tooltipName">{{ item.iconName }}</a>
    </div>
  </div>
</template>