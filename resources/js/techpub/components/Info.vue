<script>
import { useTechpubStore } from '../techpubStore';

export default {
  data(){
    return {
      techpubStore: useTechpubStore(),
      // messages: undefined,
      // showMessages: true
    }
  },
  props: ['messages', 'showMessages', 'isSuccess'],
  updated(){
    window.messages = this.$props.messages;
  }
}
</script>

<template>
  <div class="justify-center contents h-2/3 z-50 w-1/2 left-1/4 top-1/4 shadow-2xl" v-if="messages" v-show="showMessages">

    <div :class="[ $props.isSuccess ? 'bg-cyan-300' : 'bg-red-600' ,'pb-3  px-5 shadow-sm rounded-lg block text-left w-full']">
      <div class="text-center text-xl p-3 font-bold">Message: {{ $props.isSuccess ? 'success' : 'fail'  }}
        <button class="float-right" @click="showMessages = false">X</button>
      </div>
      <div v-for="message in messages">
        <div v-if="(message.constructor === Object)">

          <!-- untuk generate error text disetiap input form -->
          {{ (() => {techpubStore.Errors = messages})() }}

          <div class="mb-2" v-for="(ms, i) in message">
            <h5> {{ i }} </h5>
            <p style="line-break: anywhere" v-for="text in ms" v-html="text.replace(/([\S]+.xml)/g, `<a href='/csdb/${$route.params.projectName}/$1'>$1<a/>`)"/>
          </div>
        </div>
        <div v-else-if="(message.constructor === Array)">
          <div v-for="ms in message" v-html="ms.replace(/([\S]+.xml)/g, `<a href='/csdb/${$route.params.projectName}/$1'>$1<a/>`)" class="mb-1"></div>

        </div>
        <div v-else v-html="message.replace(/([\S]+.xml)/g, `<a href='/csdb/${$route.params.projectName}/$1'>$1<a/>`)" class="mb-1"></div>
      </div>
    </div>
  </div>
</template>