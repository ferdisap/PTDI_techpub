<script>
import { useSearchStore } from '../searchStore';


export default {
  data(){
    return {
      searchStore: useSearchStore()
    };
  },
  props: ['id','name','projectName', 'filename']
}
</script>
<template>
  <div class="w-full">
    <form @submit.prevent="searchStore.search()">
      <input type="text" :id="$props.id ?? 'search'" :name="$props.name ?? 'search'" class="block w-full p-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search">
      <slot/>
    </form>
  </div>
</template>