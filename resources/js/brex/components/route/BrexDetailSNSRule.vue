<script>
import axios from 'axios';
import { useTechpubStore } from '../../../techpub/techpubStore';

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      transformedObject: '',
    }
  },
  computed: {
    dynamic(){
      return{
        template: this.transformedObject,
        data(){
          return {
            store: useTechpubStore(),
          }
        },
      }
    },    
  },
  mounted(){
    let projectName = this.$route.params.projectName;
    let filename = this.$route.params.filename;

    let route = this.techpubStore.getWebRoute("api.get_brex_transform", {project_name: projectName, filename: filename, type: 'snsRules'});
    
    axios({
      url: route.url,
      method: route.method[0],
      responseType: 'blob',
    })
    .then(async (rsp) => {
      this.transformedObject = await rsp.data.text();
    })
    .catch((e) => {
      this.$root.error(e);
    })
  },
}
</script>

<template>
  <div>
    <component :is="dynamic" v-if="transformedObject" />
  </div>
</template>