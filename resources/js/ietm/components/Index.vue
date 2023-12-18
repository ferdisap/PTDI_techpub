<script>
import Topbar from './Topbar.vue';
export default {
  components: {Topbar}
}
</script>
<template>
  <div class="mt-20 w-full grid grid-rows-2 grid-cols-2 place-content-center gap-4 h-[70vh]">
    <div class="flex justify-end">
      <div class="shadow-lg p-4 w-40 text-center rounded-xl border-sky-100 border bg-gradient-to-tr from-red-200 via-neutral-100 to-blue-700">
        <router-link to="/ietm/insert-token" class="no-underline text-black">
        <i class="material-icons" style="font-size:50px">vpn_key</i>
        <br>
        <span class="text-xl font-bold shadow-sm">Insert Token Repository</span>
        </router-link>
      </div>
    </div>
    <div class="flex justify-start">
      <div class="shadow-lg p-4 w-40 text-center rounded-xl border-sky-100 border bg-gradient-to-tr from-green-500 via-neutral-100 to-red-700">
        <router-link to="/ietm/list-repo" class="no-underline text-black">
          <i class="material-icons" style="font-size:50px">library_books</i>
          <br>
          <span class="text-xl font-bold shadow-sm">Change Selected Repository</span>
        </router-link>
      </div>
    </div>
    <div class="flex justify-end">
      <div class="shadow-lg p-4 w-40 text-center rounded-xl border-sky-100 border bg-gradient-to-tr from-yellow-500 via-neutral-100 to-green-500">
        <i class="material-icons" style="font-size:50px">menu_book</i>
        <br>
        <span class="text-xl font-bold shadow-sm">User Guides</span>
      </div>
    </div>
    <div class="flex justify-start">
      <div class="shadow-lg p-4 w-40 text-center rounded-xl border-sky-100 border bg-gradient-to-tr from-sky-400  via-neutral-100 to-slate-400 ">
        <i class="material-icons" style="font-size:50px">help</i>
        <br>
        <span class="text-xl font-bold shadow-sm">Help</span>
      </div>
    </div>
  </div>
</template>