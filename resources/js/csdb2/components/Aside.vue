<script>
export default {
  data(){
    return {}
  },
}
</script>

<template>
  <aside class=" m-3 rounded-xl shadow-xl border-slate-300 bg-slate-50 min-w-[30%] text-black h-screen text-left py-3 w-100">
    <div class="p-2 flex items-center space-x-3 mb-3">
      <span class="material-icons text-4xl bg-violet-800 text-white p-2 shadow-lg rounded-md">library_books</span>
      <div>
        <span class="font-bold text-2xl">repoName</span>
        <br/>
        <span>Date created</span>
      </div>
    </div>
    <hr/>
    <ul class="my-4 mx-2 font-mono">
      <li class="flex items-center hover:bg-blue-500 hover:text-white rounded-lg">
        <span class="material-icons text-4xl p-2">dataset</span>
        <RouterLink :to="{name: 'Project'}" class="text-xl font-medium">Project</RouterLink>
      </li>
      <li class="flex items-center hover:bg-blue-500 hover:text-white rounded-lg">
        <span class="material-icons text-4xl p-2">local_library</span>
        <RouterLink :to="{name: 'Project'}" class="text-xl font-medium">Repository</RouterLink>
      </li>
    </ul>
  </aside>
</template>