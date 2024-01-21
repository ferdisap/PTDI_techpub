<script>
import { useTechpubStore } from '../../techpub/techpubStore';
import CreateDML from '../components/subComponents/CreateDML.vue';
import IndexDML from '../components/subComponents/IndexDML.vue';
import IndexBREX from '../components/subComponents/IndexBREX.vue';

export default {
  data(){
    return {
      techpubStore: useTechpubStore(),
      showDML: '',
    }
  },
  components: {CreateDML, IndexDML, IndexBREX},
}
</script>
<template>
  <div class="Committing">

    <div class="w-full text-center mb-3 mt-3">
      <button :class="[showDML == 'createDML' ? 'border-b-black border-b-4' : '' ,'button-nav']" @click="showDML = 'createDML'">Create DML</button>
      <button :class="[showDML == 'createBrex' ? 'border-b-black border-b-4' : '' ,'button-nav']" @click="showDML = 'createBrex'">Create BREX</button>
    </div>    
    <CreateDML v-if="showDML == 'createDML'"/>
    
    <div class="mb-3">
      <IndexDML :both="true"/>
    </div>
    <div class="mb-3">
      <IndexBREX/>
    </div>

  </div>
</template>