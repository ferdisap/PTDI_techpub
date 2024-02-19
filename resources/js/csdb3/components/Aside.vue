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
    <!-- <div class="bg-blue-500 py-3 px-2 text-white mb-3 border rounded-t-xl text-center">
      <span class="text-2xl">Detail</span>
      <button class="float-right mr-3 hover:scale-125" @click="delete this">X</button>
    </div> -->
    <div class="p-2 flex items-center space-x-3 mb-3 bg-blue-500 rounded-t-xl text-white">
      <span class="material-symbols-outlined text-4xl bg-transparent text-white p-2 rounded-md">database</span>
      <div>
        <span class="font-bold text-2xl">CSDB</span>
        <br />
        <span class="text-black italic font-light text-sm">powered by PTDI</span>
      </div>
    </div>
    <hr/>

    <!-- BR and DML -->
    <div class="mt-3 mb-3">
      <details>
        <summary @click="$router.push({name: 'IndexBR'})">
          <span class="material-icons text-4xl p-2">rule</span>
          <span>BR</span>
        </summary>
        <div class="pl-5">
          <a class="p-3" @click.prevent="$router.push({name:'CreateBR'})" :href="techpubStore.getWebRoute('',{},Object.assign({},$router.getRoutes().find(r => r.name =='CreateBR')))['path']">
            Create
          </a>
        </div>
      </details>
      <details>
        <summary @click="$router.push({name: 'IndexDML'})">
          <span class="material-icons text-4xl p-2">donut_small</span>
          <span>DML</span>
        </summary>
        <div class="pl-5">
          <a class="p-3" @click.prevent="$router.push({name:'CreateDML'})" :href="techpubStore.getWebRoute('',{},Object.assign({},$router.getRoutes().find(r => r.name =='CreateDML')))['path']">
            Create
          </a>
        </div>
      </details>
    </div>
    <hr/>

    <!-- Staged Staging Editting -->
    <div class="mt-3 mb-3">
      <details>
        <!-- <summary @click="$router.push({name: 'IndexObject'})"> -->
        <summary @click="$router.push({name: 'Stagged'})">
          <span class="material-icons text-4xl p-2">commit</span>
          <span>Staged</span>
        </summary>
        <div class="pl-5">
          <a class="p-3" @click.prevent="$router.push({name:'Editing-PushToStage'})" :href="techpubStore.getWebRoute('',{},Object.assign({},$router.getRoutes().find(r => r.name =='Editing-PushToStage')))['path']">
            Push To Stage
          </a>
        </div>
      </details>
      <details>
        <summary @click="$router.push({name: 'Staging'})">
          <span class="material-icons text-4xl p-2">approval</span>
          <span>Staging</span>
        </summary>
      </details>
      <details>
        <summary @click="$router.push({name:'Editting'})">
          <span class="material-icons text-4xl p-2">edit_square</span>
          <span>Editting</span>
        </summary>
        <div class="pl-5">
          <a class="p-3" @click.prevent="$router.push({name:'Editing-Upload'})" :href="techpubStore.getWebRoute('',{},Object.assign({},$router.getRoutes().find(r => r.name =='Editing-Upload')))['path']">
            Upload
          </a>
          <a class="p-3" @click.prevent="$router.push({name:'Editing-Editor'})" :href="techpubStore.getWebRoute('',{},Object.assign({},$router.getRoutes().find(r => r.name =='Editing-Editor')))['path']">
            XML Editor
          </a>
        </div>
      </details>
    </div>
    <hr/>

    <!-- Bin -->
    <div class="mt-3 mb-3">
      <details>
        <summary @click="$router.push({name:'Deletion'})">
          <span class="material-icons text-4xl p-2">folder_delete</span>
          <span>Deletion</span>
        </summary>
      </details>
    </div>
    <hr/>
  </aside>
</template>