<script>
export default {
  data(){
    return {
      // messages: undefined,
      // showMessages: true
    }
  },
  props: ['messages', 'showMessages'],
}
</script>

<template>
  <div class="flex justify-center absolute h-2/3 z-50 w-1/2 left-1/4 top-1/4 shadow-2xl" v-if="messages" v-show="showMessages">
    <div class="bg-cyan-300 px-5 shadow-sm rounded-lg block text-left w-full">
      <div class="text-center text-xl p-3 font-bold">Message:
        <button class="float-right" @click="showMessages = false">X</button>
      </div>
      <div v-for="message in messages">
        <div v-html="message" class="mb-1" @click="showMessages = false"></div>
      </div>
    </div>
  </div>
</template>