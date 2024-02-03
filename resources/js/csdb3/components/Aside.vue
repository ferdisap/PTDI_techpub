<script>
import { useTechpubStore } from '../../techpub/techpubStore';

export default {
  data() {
    return {
      view: undefined,
      techpubStore: useTechpubStore(),
    }
  },
  methods: {
    tes(){
      console.log('aa');
    }
  },
  mounted(){
    window.router = this.$router;
  },
}
</script>


<template>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  <aside class=" m-3 rounded-xl shadow-xl border-slate-300 bg-slate-50 text-black h-5/6 text-left py-3 md:w-1/4 2xl:max-w-[50%] ">
    <div class="p-2 flex items-center space-x-3 mb-3">
      <span class="material-symbols-outlined text-4xl bg-violet-800 text-white p-2 shadow-lg rounded-md">database</span>
      <div>
        <span class="font-bold text-2xl">CSDB</span>
        <br />
        <span>powered by PTDI</span>
      </div>
    </div>
    <hr/>

    <details>
      <summary @click="$router.push({name: 'IndexBR'})">
        <span class="material-icons text-4xl p-2">rule</span>
        <span>BR</span>
      </summary>
      <div>
        <a @click.prevent="$router.push({name:'CreateBR'})" :href="techpubStore.getWebRoute('',{},Object.assign({},$router.getRoutes().find(r => r.name =='CreateBR')))['path']">
          Create
        </a>
      </div>
    </details>
    <details>
      <summary @click="$router.push({name: 'IndexDML'})">
        <span class="material-icons text-4xl p-2">donut_small</span>
        <span>DML</span>
      </summary>
      <div>
        <a @click.prevent="$router.push({name:'CreateDML'})" :href="techpubStore.getWebRoute('',{},Object.assign({},$router.getRoutes().find(r => r.name =='CreateDML')))['path']">
          Create
        </a>
      </div>
    </details>
    <hr/>

    <details>
      <summary @click="$router.push({name: 'IndexObject'})">
        <span class="material-icons text-4xl p-2">commit</span>
        <span>Commit</span>
      </summary>
      <div>
        <a @click.prevent="$router.push({name:'Editing-PushToStage'})" :href="techpubStore.getWebRoute('',{},Object.assign({},$router.getRoutes().find(r => r.name =='Editing-PushToStage')))['path']">
          Push To Stage
        </a>
      </div>
    </details>
    <details>
      <summary @click="$router.push({name: 'Stage'})">
        <span class="material-icons text-4xl p-2">approval</span>
        <span>Staging</span>
      </summary>
    </details>
    <details>
      <summary @click="$router.push({name:'InEditting-IndexObject'})">
        <span class="material-icons text-4xl p-2">edit_square</span>
        <span>Editor</span>
      </summary>
      <div>
        <a @click.prevent="$router.push({name:'Editing-Upload'})" :href="techpubStore.getWebRoute('',{},Object.assign({},$router.getRoutes().find(r => r.name =='Editing-Upload')))['path']">
          Upload
        </a>
        <a @click.prevent="$router.push({name:'Editing-Editor'})" :href="techpubStore.getWebRoute('',{},Object.assign({},$router.getRoutes().find(r => r.name =='Editing-Editor')))['path']">
          XML Editor
        </a>
      </div>
    </details>
  </aside>
</template>