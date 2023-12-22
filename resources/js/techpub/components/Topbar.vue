<script>
import { useTechpubStore } from '../../techpub/techpubStore'

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
    }
  }
}
</script>

<template>
  <nav class="bg-blue-500 text-white h-20 relative flex justify-between items-center">
    <div class="pl-3">
      <a href="/" class="text-3xl">Technical Publication</a>

      <span class="ml-5">
        {{ techpubStore.Auth.name }}
      </span>
    </div>

    <div class="flex text-xl">
      <a href="#" class="mx-2">Home</a>
      <a href="#" class="mx-2">About</a>
      <a href="#" class="mx-2">Contact</a>
    </div>

    <form action="" method="GET">
      <div class="flex">
        <input type="token" id="token" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required placeholder="search..">
        <button type="submit" @click="getRepo" class="shadow-lg text-white font-semibold rounded-lg ml-2 px-5 py-1 bg-slate-500 hover:bg-slate-600 active:bg-violet-700 focus:outline-none focus:ring focus:ring-violet-300 ">Search</button>
      </div>
    </form>
  </nav>


</template>