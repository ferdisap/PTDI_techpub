<script>
import axios from 'axios';
import { useTechpubStore } from '../../techpub/techpubStore'
import { mount } from '@vue/test-utils';

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      openDropdown: false
    }
  },
  methods:{
    // logout(params = {}){
    //   let route = this.techpubStore.getWebRoute('logout', params);
    //   window.router = this.$router;
    //   axios({
    //     url: route.url,
    //     method: route.method[0],
    //     data: route.params,
    //   })
    //   .then( response => window.location.href ="/")
    //   .catch( e => this.$root.error(e));
    // }
  },
}
</script>

<template>
  <nav class="bg-blue-500 text-white h-20 relative flex justify-between items-center">
    <div class="pl-3">
      <a href="/" class="text-3xl">Technical Publication</a>

      <div class="ml-3 dropdown" v-if="techpubStore.Auth.name">
        <button @click="openDropdown = !openDropdown">
          {{ techpubStore.Auth.name }} <span class="material-symbols-outlined align-middle">keyboard_arrow_down</span>
        </button>
        <div v-show="openDropdown" class="dropdown-content text-black">
          <div class="dropdown-menu"><a class="block w-full" href="/profile">Profile</a></div>
          <div class="dropdown-menu"><a class="block w-full" href="/dashboard">Dashboard</a></div>
          <div class="dropdown-menu"><a class="block w-full" href="/csdb3">CSDB</a></div>
          <hr/>
          <div class="dropdown-menu"><a class="block w-full" href="/logout">Logout</a></div>
        </div>
      </div>
      <a class="ml-3" v-else :href="techpubStore.getWebRoute('login')['path']"> login here..</a>
    </div>

    <div class="flex text-xl">
      <a href="#" class="mx-2">Home</a>
      <a href="#" class="mx-2">About</a>
      <a href="#" class="mx-2">Contact</a>
    </div>

    <form action="" method="GET">
      <div class="flex">
        <input type="text" id="search" name="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="search..">
        <button class="shadow-lg text-white font-semibold rounded-lg ml-2 px-5 py-1 bg-slate-500 hover:bg-slate-600 active:bg-violet-700 focus:outline-none focus:ring focus:ring-violet-300 ">Search</button>
        <!-- <input type="token" id="token" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required placeholder="search..">
        <button type="submit" @click="getRepo" class="shadow-lg text-white font-semibold rounded-lg ml-2 px-5 py-1 bg-slate-500 hover:bg-slate-600 active:bg-violet-700 focus:outline-none focus:ring focus:ring-violet-300 ">Search</button> -->
      </div>
    </form>
  </nav>


</template>