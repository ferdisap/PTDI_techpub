<script>
import IndexDML from '../components/subComponents/IndexDML.vue';
import { useTechpubStore } from '../../techpub/techpubStore';
import Editor from '../components/subComponents/Editor.vue';
import IndexObject from '../components/subComponents/IndexObject.vue';
export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      showEditor: true,
    }
  },
  components: { IndexDML, Editor, IndexObject, IndexObject },
}
</script>
<template>


  <div class="mb-5">
    <IndexDML :only_uneditable="true" />
  </div>
  <hr/>

  <div class="mb-5">
    <IndexObject/>
  </div>

  

  <hr/>
  <!-- xml editor -->
  <div class="mb-5 mt-5">
    <Editor :is-create="true"/>
  </div>

  
  
</template>