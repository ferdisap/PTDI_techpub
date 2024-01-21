<script>
export default {
  data() {
    return {}
  },
}
</script>


<template>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  <aside
    class=" m-3 rounded-xl shadow-xl border-slate-300 bg-slate-50 text-black h-5/6 text-left py-3 md:w-1/4 2xl:max-w-[50%] ">
    <div class="p-2 flex items-center space-x-3 mb-3">
      <span class="material-symbols-outlined text-4xl bg-violet-800 text-white p-2 shadow-lg rounded-md">database</span>
      <div>
        <span class="font-bold text-2xl">CSDB</span>
        <br />
        <span>powered by PTDI</span>
      </div>
    </div>
    <hr />
    <ul class="my-4 mx-2 font-mono">
      <li class="flex items-center hover:bg-blue-500 hover:text-white rounded-lg">
        <span class="material-icons text-4xl p-2">commit</span>
        <a href="javascript:void(0)" @click="$router.push({name:'Commit'})">Commit</a>
      </li>
      <li class="flex items-center hover:bg-blue-500 hover:text-white rounded-lg">
        <span class="material-icons text-4xl p-2">approval</span>
        <a href="javascript:void(0)" @click="$router.push({name:'Stage'})">Staging</a>
      </li>
      <li class="flex items-center hover:bg-blue-500 hover:text-white rounded-lg">
        <span class="material-icons text-4xl p-2">edit_square</span>
        <a href="javascript:void(0)" @click="$router.push({name:'Edit'})">Editor</a>
      </li>
    </ul>
  </aside>
</template>