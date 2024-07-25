<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
import ContinuousLoadingCircle from '../../loadingProgress/continuousLoadingCircle.vue';
import { get_list, goto, clickFolder, clickFilename, createListTreeHTML, deleteList, pushList, refresh, remove} from './ListTreeVue';

export default {
  data() {
    return {
      data: {},
      html: '',
      techpubStore: useTechpubStore(),
      showLoadingProgress: false,
    }
  },
  components: {ContinuousLoadingCircle},
  props: {
    type: String,
    routeName: String,
  },
  methods: {
    get_list: get_list,    
    goto: goto,
    clickFolder: clickFolder,
    clickFilename: clickFilename,
    createListTreeHTML: createListTreeHTML,
    deleteList: deleteList,
    pushList: pushList,
    
    refresh: refresh,
    remove: remove,
  },
  async mounted() {
    this.data.open = JSON.parse(top.localStorage.getItem('expandCollapseListTree'))

    let emitters =  this.emitter.all.get('ListTree-refresh'); // output array or undefined. Jika array, berarti sudah pernah di instance
    if(!(emitters)){
      this.emitter.on('ListTree-refresh', refresh.bind(this));
    }

    emitters =  this.emitter.all.get('ListTree-remove'); // output array or undefined. Jika array, berarti sudah pernah di instance
    if(!(emitters)){
      this.emitter.on('ListTree-remove', remove.bind(this));
    }

    let list = await this.get_list(this.$props.type);
    if(list) this.createListTreeHTML();
  },
  computed: {
    listobject() {
      return this.data[`${this.$props.type}_list`] ?? {};
    },
    pageless() {
      return this.paginationInfo['current_page'] > 1 ? this.paginationInfo['current_page'] - 1 : 1;
    },
    pagemore() {
      return (this.paginationInfo['current_page'] < this.paginationInfo['last_page']) ? this.paginationInfo['current_page'] + 1 : this.paginationInfo['last_page']
    },
    level() {
      return this.data[`${this.$props.type}_list_level`] ?? {}
    },
    tree() {
      return {
        template: this.html,
        computed: {
          parent() {
            return this.$parent;
          }
        },
        methods: {
          expandCollapse(path){
            let details = $(`details[path='${path}']`)[0];
            details.open = !details.open;
            let icon = details.firstElementChild.firstElementChild;
            icon.innerHTML = details.open ? 'keyboard_arrow_down' : 'chevron_right' 
            if(!this.$parent.data.open){
              let expandCollapseListTreeFromLocalStorage = top.localStorage.getItem('expandCollapseListTree');
              if(expandCollapseListTreeFromLocalStorage){
                this.$parent.data.open = JSON.parse(expandCollapseListTreeFromLocalStorage)
              } else {
                this.$parent.data.open = {};
              }
            }
            this.$parent.data.open[path] = details.open;
            top.localStorage.setItem('expandCollapseListTree', JSON.stringify(this.$parent.data.open))
          }
        },
      }
    }
  },
}
</script>
<template>
  <div class="listtree h-full relative">
    <!-- list -->
    <div :class="['listtree-list', $props.isRoot ? 'h-[90%] overflow-auto' : '']">
      <!-- <component v-if="(this.data[`${this.$props.type}_list_level`] && this.data[`${this.$props.type}_list`])" :is="tree" /> -->
      <component v-if="html" :is="tree" />
    </div>
    <ContinuousLoadingCircle :show="showLoadingProgress"/>
  </div>
</template>