<script>
import EditorGeneral from './EditorGeneral.vue'
import EditorDML from './EditorDML.vue';
import RCMenu from '../../rightClickMenuComponents/RCMenu.vue';
export default {
  data(){
    return {
      type: 'DMC',
    }
  },
  components: {EditorGeneral, EditorDML, RCMenu},
  methods:{
  }
}
</script>
<template>
  <div>
    <EditorDML v-if="type === 'DML'"/>
    <EditorGeneral v-else/>
  
    <RCMenu id="EditorVue">
      <div @click="type = 'DML'" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Create DML</div>
      </div>
      <div @click="type = 'DMC'" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Create DMC</div>
      </div>
      <div @click="type = 'PMC'" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Create PMC</div>
      </div>
      <div class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Create Comment</div>
      </div>
    </RCMenu>
  </div>
</template>