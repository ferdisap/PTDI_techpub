<script>
export default {
  data() {
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
  methods: {
    click(componentName) {
      const show = (this.$props.items[componentName]['isShow'] = !this.$props.items[componentName]['isShow']);
      
      let bbi = this.$route.query.bbi; // ?bbi=History,Folder,Editor
      if(bbi) bbi = bbi.split(",");
      else bbi = [];

      (show ? bbi.unshift(componentName) : bbi.splice(bbi.indexOf(componentName),1));
      this.$router.replace({
        path: this.$route.path,
        query: {
          bbi: bbi.join(","),
        }
      });

      // sort view;
      if(show){
        setTimeout(()=>{
          bbi = bbi.map(v => v = v.toLowerCase());
          const target = document.querySelector("."+componentName.toLowerCase());
          const CSSSelector = Object.keys(this.$props.items).map(componentName => "."+componentName.toLowerCase()).join(", "); // ".folder .explorer .preview ...etc"
          [...document.querySelectorAll(CSSSelector)].sort(function (a, b) {
            return bbi.indexOf(a.classList[0]) - bbi.indexOf(b.classList[0])
          }).forEach(function (item) {
            if(item && (item.parentElement == target.parentElement)) target.parentElement.appendChild(item);
          })
        });
      }
    },
  },
  activated() {
    let bbi = this.$route.query.bbi;
    if (!bbi) return;
    bbi = bbi.split(",");
    for (let i = 0; i < bbi.length; i++) {
      // this.$props.items[bbi[i]]['isShow'] = true;
      if(this.$props.items[bbi[i]]) this.$props.items[bbi[i]]['isShow'] = true;
    }
  },
  mounted() {
    window.bb = this;
    let emitters = this.emitter.all.get('bottom-bar-switch'); // 'emitter.length < 2' artinya emitter max. hanya dua kali di instance atau baru sekali di emit, check Explorer.vue
    if (emitters) {
      let indexEmitter = emitters.indexOf(emitters.find((v) => v.name === 'bound click')) // 'bound addObjects' adalah fungsi, lihat scrit dibawah ini. Jika fungsi anonymous, maka output = ''
      if (emitters.length < 1 && indexEmitter < 0) this.emitter.on('bottom-bar-switch', this.click.bind(this));
    } else this.emitter.on('bottom-bar-switch', this.click.bind(this));
  }

}
</script>

<template>
  <div
    :class="['mt-2 absolute flex h-max w-max  space-x-2 px-2 py-1 border-4 border-blue-500 bg-white rounded-xl', $props.class]">
    <div v-for="(item, componentName) in $props.items"
      :class="['relative h-max w-max flex items-center rounded-lg', item.isShow ? 'bg-blue-500' : 'bg-white']">
      <a @click.stop.prevent="click(componentName)" href="#"
        :class="['material-symbols-outlined bg-transparent  p-2 rounded-md has-tooltip-arrow', item.isShow ? 'text-white' : 'text-blue-500']"
        :data-tooltip="item.tooltipName">{{ item.iconName }}</a>
    </div>
</div></template>