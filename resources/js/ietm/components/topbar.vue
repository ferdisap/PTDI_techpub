<script>
import { useIetmStore } from '../ietmStore';

export default {
  name: 'Topbar',
  data(){
    ietmStore: useIetmStore()
  }
}
</script>
<template>
  <div class="flex bg-neutral-100 h-16 columns-3 w-full items-center">
    <div class="w-full flex justify-start">
      <h1 class="text-blue-600 font-bold align-middle text-2xl flex items-center">PT DIRGANTARA INDONESIA</h1>
    </div>
    <div class="w-full flex justify-center h-10">
      <input type="text" id="search" class="block w-full p-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search" required>
      <button class="p-2.5 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500">
        <svg xmlns="http://www.w3.org/2000/svg" style="height: -webkit-fill-available" height="24" viewBox="0 -960 960 960" width="24"><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
      </button>
    </div>
    <div class="w-full flex justify-end">
      <div class="mx-8">
        Repository Name:
        <br/>
        <span>{{ $route.params.repoName }}</span>
        <!-- <br/> -->
        <!-- <span>Date Create: {{ ietmStore.repo.date }}</span> -->
        <!-- <span>welcome, Ferdi</span>
        <br/>
        <a href="" class="underline text-blue-600">Account Settings</a> | <a href="" class="underline text-blue-600">Logout</a> -->
      </div>
    </div>
  </div>
</template>