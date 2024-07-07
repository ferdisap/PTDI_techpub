<script>
import Topbar from '../techpub/components/Topbar.vue';
import Info from '../techpub/components/Info.vue';
import Aside from './components/Aside.vue';
import Main from './components/Main.vue';
import { useTechpubStore } from '../techpub/techpubStore';
import { markdown } from 'markdown';
import createAlert from '../alert';



export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      messages: undefined,
      isSuccess: true,

      showMessages: false,
      errors: undefined, // {}
      message: undefined, // ""

      infoData: {}, // requiredAttribute: 'show:Boolean', 'message:String', 'name:String'
      alertData: {}
    }
  },
  components: { Topbar, Info, Aside, Main },
  methods: {
    // ini sesuai ret2(). Bedanya, yang fungsi ini ada pesan axiosError.message dan errors untuk input name
    // fungsi ini nanti bisa dihapus karena sekarang <info/> pakai emitt buz
    // async error(axiosError) {
    //   let errors, message;
    //   console.log(axiosError);
    //   axiosError.response.data.errors ? (errors = axiosError.response.data.errors) : (errors = undefined);
    //   axiosError.response.data.message ? (message = axiosError.message + ': ' + axiosError.response.data.message) : (message = axiosError.message);
    //   this.emitter.emit('flash', {
    //     isSuccess: false,
    //     errors: errors,
    //     message: message,
    //   });
    //   // this.techpubStore.showLoadingBar = false;
    //   // this.showMessages = true;
    // },
    // ini sesuai ret2(). 
    // success(response, isSuccess = true) {
    //   this.emitter.emit('flash', {
    //     isSuccess: true,
    //     message: response.data.message ? response.data.message : '',
    //   });
    //   // this.techpubStore.showLoadingBar = false;
    //   // this.showMessages = true;
    // },
    /**
     * required data.filename 
     */
    async info(data = {}) {
      let config = {
        route: {
          name: 'api.info',
          data: data,
        },
        responseType: 'text'
      };
      let md = await axios(config);
      // membuat <div> dulu agar didalam MD bisa ada tag HTML. Ini dicontohkan dalam README.md punya laravel (basic);
      let text = md.data;
      let div = $('<div/>').html(text);
      div.contents().each((i, e) => {
        if (e.nodeType === 3) {
          $(e).replaceWith(markdown.toHTML(e.textContent));
        }
      })
      this.infoData.message = div[0].innerHTML;
      this.infoData.show = true;
      this.infoData.name = name;
    },
    /**
     * required data.filename, data.objectame
     * diutamakan request filename.md. Jika request error, maka akan pakai data.message
     */
    async alert(data = {}) {
      // get MD file
      let response = await axios({
        route: {
          name: 'api.alert',
          data: {name: data.name}
        }
      });
      if(response.statusText !== 'OK'){
        return;
      }
      let text = response.data;
      if(!text){
        text = data.message;
      }
      delete data.name; // dihapus data.name agar tidak tertukar antara 'filename' dan 'name'. 'name' adalah nama alert
      delete data.message;
      // replace all variable in MD file with params 'data'
      this.$root.findText(/`\${([\S]+)}`/gm, text).forEach(v => {
        let replaced = v[0].replace(/(?<=`\${)([\S]+)(?=}`)/gm, 'data.$1')
        text = text.replace(v[0], eval(replaced));
      })

      // escaping text jikalau ada html didalam MD file
      let div = $('<div/>').html(text);
      div.contents().each((i, e) => {
        if (e.nodeType === 3) {
          $(e).replaceWith(markdown.toHTML(e.textContent));
        }
      })
      data.message = div[0].innerHTML;
      // finish by creating Alert;
      this.alertData = createAlert(data);
      return this.alertData.result;
    },
    async download() {
      if (!this.srcblob) {
        let response = await axios({
          route: {
            name: 'api.get_object',
            data: { filename: this.$route.params.filename },
          },
          responseType: 'blob'
        });
        if (response.statusText === 'OK') {
          this.typeblob = response.headers.getContentType();
          if (this.typeblob.includes('xml')) {
            this.raw = await response.data.text();
          }
          this.srcblob = URL.createObjectURL(await response.data);
        }
      }
      let a = $('<a/>')
      a.attr('download', this.$route.params.filename);
      a.attr('href', this.srcblob);
      a[0].click();
    },
    async download_all() {
      let response = await axios({
        route: {
          name: 'api.get_export_file',
          data: { filename: this.$route.params.filename },
        },
        responseType: 'blob'
      });
      if (response.statusText === 'OK') {
        let srcblob = URL.createObjectURL(await response.data);
        let filename = this.$route.params.filename;
        if(response.headers['content-type'].includes('zip')){
          filename = this.$route.params.filename.replace(/\.\w+$/,'.zip');
        }
        let a = $('<a/>')
        a.attr('download', filename);
        a.attr('href', srcblob);
        a[0].click();
      }
    }
  },
  beforeCreate() {
    this.References.defaultStore = useTechpubStore();
  },
  mounted(){
    window.techpubStore = this.techpubStore;
  }
}
</script>

<template>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  <Topbar />
  <Info />

  <div class="flex mx-auto">
    <Aside />
    <Main />
  </div>

  <div v-if="techpubStore.showLoadingBar" class="fixed top-0 left-0 h-[100vh] w-[100%] z-50">
    <div style="
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    background:rgba(0,0,0,0.5);
    ">
      <div class="loading_buffer"></div>
    </div>
  </div>


  <div v-if="alertData.show" class="fixed top-0 left-0 h-[100vh] w-[100%] z-50 bg-[rgba(255,0,0,00)] font-mono">
    <div style="
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    background:rgba(0,0,0,0.5);
    ">
      <div
        class="w-1/2 h-1/2 bg-white dark:bg-neutral-900 opacity-100 absolute top-1/4 left-1/4 rounded-xl border-[15px] border-red-500 border-dashed">
        <h1 class="text-center mt-3 mb-3">ALERT !</h1>
        <hr class="border-2" />
        <div class="max-h-[65%] overflow-auto px-10" message v-html="alertData.message"></div>
        <div class="w-full text-center bottom-3 absolute">
          <button autofocus class="button-danger shadow-md" alert-not-ok @click="alertData.button(0)">X</button>
          <button class="button-safe shadow-md" alert-ok @click="alertData.button(1)">O</button>
        </div>
      </div>
    </div>
  </div>

  <div v-if="infoData.show" id="info" class="fixed top-0 left-0 h-[100vh] w-[100%] z-50 bg-[rgba(255,0,0,00)] font-mono">
    <div style="
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    background:rgba(0,0,0,0.5);
    ">
      <div
        class="w-1/2 h-1/2 bg-white dark:bg-neutral-900 opacity-100 absolute top-1/4 left-1/4 rounded-xl border-[15px] border-cyan-200">
        <h1 class="text-center mt-3 mb-3">INFORMATION</h1>
        <hr class="border-2" />
        <div class="max-h-[65%] overflow-auto px-10" v-html="infoData.message"></div>
        <div class="w-full text-center bottom-3 absolute">
          <button autofocus class="button-danger shadow-md" @click="infoData.show = false">X</button>
        </div>
      </div>
    </div>
  </div>

  <!-- <div class="w-1/2 h-1/2 bg-white opacity-100 absolute top-1/4 left-1/4 rounded-xl border-[15px] border-red-500 border-dashed">
    <h1 class="text-center mt-3 mb-3">ALERT !</h1>
    <hr class="border-2"/>
    <div v-html="Alert.message" class="max-h-[65%] overflow-auto px-10"></div>
    <div class="w-full text-center bottom-3 absolute">
      <button class="button-danger" @click="reject()">X</button>
      <button class="button-safe" @click="resolve()">O</button>
      <button class="button-violet" @click="emitter.emit('alert',{message: 'messages tesmessages', fn: fun})">tes emit</button>
    </div>
  </div> -->


  <!-- <dialog open class="fixed top-0 left-0 h-[100vh] w-[100%] z-50">
    <div style="
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    background:rgba(0,0,0,0.5);
    ">
      <div id="#dialog" class="w-1/2 h-1/2 bg-white opacity-100 absolute top-1/4 left-1/4 rounded-xl border-[15px] border-red-500 border-dashed">
        <h1 class="text-center mt-3 mb-3">ALERT !</h1>
        <hr class="border-2"/>
        <div class="max-h-[65%] overflow-auto px-10" message>message</div>
        <div class="w-full text-center bottom-3 absolute">
          <button class="button-danger" onclick=run.getresult(0)>X</button>
          <button class="button-safe" onclick="run.getresult(1)">O</button>
        </div>
      </div>
    </div>
  </dialog> --></template>