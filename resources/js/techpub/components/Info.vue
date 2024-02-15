<script>
import { useTechpubStore } from '../techpubStore';

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      bag: [], // 'isSuccess:String', 'errors:{}', 'message:String',
      // messages: undefined,
      // showMessages: true
    }
  },
  props: [
    'messages', 'showMessages', // 2 props ini bisa di hapus
    'isSuccess', 'errors', 'message'], // tidak diperlukan lagi karena sekarang <info/> pakai emitbuz
  methods: {
    replaceFilenameWithURL(text) {
      let forObject = this.techpubStore.getWebRoute('', { filename: '$1' }, Object.assign({}, this.$router.getRoutes().find(r => r.name == 'DetailObject')))['path'];
      text = text.replace(/([\S]+.xml)(\s|$|\.)/g, `<a class="font-bold" href="${forObject}">$1$2</a>`);
      return text;
    },
    addBag(data = {}) {
      if (data.message) {
        if (!(this.bag.find((b) => b.message == data.message))) {
          this.bag.push(data);
          this.techpubStore.Errors = data.errors ? [data.errors] : [];
          setTimeout(() => this.popBag(data), 10000);
        }
      }
      this.techpubStore.showLoadingBar = false;
    },
    popBag(value) {
      let index = this.bag.indexOf(value);
      this.bag.splice(index,1);
    },
  },
  mounted() {
    this.emitter.on('flash', (data) => this.addBag(data));
  },
}
</script>

<template>
  <div class="fixed w-1/2 top-1/5 right-0 z-50 bg-transparent" v-if="bag.length !== 0">
    <div v-for="info in bag" :class="[info.isSuccess ? 'bg-cyan-300' : 'bg-red-600', 'mb-3 pb-3 px-5 shadow-lg rounded-lg block text-left w-full']">
      <div class="text-center text-xl p-3 font-bold">Message
        <span class="float-right has-tooltip-arrow" data-tooltip="Close"><button class="hover:scale-150"
            @click="popBag(info)" info-close-btn>X</button></span>
      </div>
      <div v-html="replaceFilenameWithURL(info.message)" class="mb-1"></div>
      <div class="mb-2" v-for="(ms, i) in info.errors">
        <h5> {{ i }} </h5>
        <p style="line-break: anywhere" v-for="text in ms"
          v-html="text.replace(/([\S]+.xml)(\s|$|\.)/g, `<a href='/csdb/$1'>$1$2<a/>`)" />
      </div>
    </div>
  </div>
</template>