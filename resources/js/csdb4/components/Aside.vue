<script>
import { useTechpubStore } from '../../techpub/techpubStore';
// import ButtonMinimizeContainer from './subComponents/ButtonMinimizeContainer.vue';
import { RouterLink } from 'vue-router'
import {isString} from '../helper.js';

export default {
  name: 'Aside',
  data() {
    return {
      techpubStore: useTechpubStore(),
      hrefs:{
        Explorer: '',
        Deletion: '',
        Welcome: '',
      },
    }
  },
  components:{RouterLink},
  methods:{
    /*
     * DEPRECATED, diganti @to()
     * data is a vueroutename or object contains property of name: ''
    */
    go(data = ''){
      // set last href
      this.hrefs[this.$route.name] = window.location.href;
      
      if(data instanceof Object){
        return this.$router.push(data)
      } 
      else if(isString(data)){
        return this.$router.push({name: data})
      }
    },
    
    /*
     * fill the href attribute with a url comes from vueroutename 
    */
    href(vueroutename, data = {}){
      return this.techpubStore.getWebRoute('',data,Object.assign({},this.$router.getRoutes().find(r => r.name === vueroutename)))['path'];
    },

    to(name= ''){
      // set last href sesuai route name sebelum pindah
      this.hrefs[this.$route.name] = window.location.href.substring(window.location.origin.length); // supaya dapet path sampai ke ujung url termasuk query dan hash
      this.$router.push(this.hrefs[name]);
    },
  },
  mounted() {
    this.hrefs.Welcome = this.href('Welcome');
    this.hrefs.Deletion = this.href('Deletion')
    this.hrefs.Explorer = this.href('Explorer',{filename:'',viewType:''})
  },
}
</script>


<template>
  <div class="absolute mx-2 bg-white rounded-xl top-[15%] w-max border-4 border-white">

    <div class="relative p-2 flex items-center space-x-3 mb-3 bg-blue-500 rounded-t-xl">
      <a @click.prevent="to('Welcome')" :href="hrefs.Welcome"  class="material-symbols-outlined bg-transparent text-white p-2 rounded-md has-tooltip-arrow"
        data-tooltip="Welcome">database</a>
    </div>
    <hr />

    <div class="relative p-2 flex items-center space-x-3 mb-3">
      <a @click.prevent="to('Explorer')" :href="hrefs.Explorer" class="material-symbols-outlined bg-transparent text-blue-500 p-2 rounded-md has-tooltip-arrow"
        data-tooltip="Explore">explore</a>
    </div>

    <div class="relative p-2 flex items-center space-x-3 mb-3">
      <a @click.prevent="to('Deletion')" :href="hrefs.Deletion" class="material-symbols-outlined bg-transparent text-blue-500 p-2 rounded-md has-tooltip-arrow"
        data-tooltip="Deletion">folder_delete</a>
    </div>

    <div class="relative p-2 flex items-center space-x-3 mb-3">
      <a href="#" class="material-symbols-outlined bg-transparent text-blue-500 p-2 rounded-md has-tooltip-arrow"
        data-tooltip="Repository">room_service</a>
    </div>

  </div>
</template>