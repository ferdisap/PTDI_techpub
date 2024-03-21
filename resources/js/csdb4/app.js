import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from "./App.vue";
import Routes from "./routers.js";

import axios from 'axios';
import { createPinia } from 'pinia';
import References from '../techpub/References';
import { useTechpubStore } from '../techpub/techpubStore';

import mitt from 'mitt';
import routes from '../../others/routes.json';


// ####### start here

/**
 * @param {string} pattern 
 * @param {string} subject 
 * @returns [Array(match1, match2)] 
 */
function find(pattern, subject) {
  let match = [];
  let m;
  while ((m = pattern.exec(subject)) !== null) {
    if (m.index === pattern.lastIndex) {
      pattern.lastIndex++;
    }
    match.push(m);
  }
  return match;
}

function createWorker()
{
  if(window.Worker){
    return new Worker("/js/csdb/worker.js",{type:'module'}); 
  } else {
    return false;
  }
}



axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['X-CSRF-Token'] = document.querySelector("meta[name='csrf-token']").content;
axios.defaults.withCredentials = true;
window.axios = axios;

const csdb = createApp(App);
const router = createRouter({
  routes: Routes,
  history: createWebHistory(),
});
const pinia = createPinia();

csdb.use(pinia);
csdb.use(router);
csdb.config.globalProperties.References = References;
csdb.config.globalProperties.emitter = mitt();
csdb.config.globalProperties.findText = find;
csdb.config.globalProperties.createWorker = createWorker;

// ga bisa npm build jika pakai await 
axios.get('/auth/check')
  .then(response => useTechpubStore().Auth = response.data)
  .catch(response => window.location.href = "/login");
// sebelum mounting app, akan request all Routes dulu
useTechpubStore().WebRoutes = routes;

/**
 * cara menggunakan axios
 * masukan 'data' (plain object) pada fungsi axios();
 * the data contains 'route' (plain object). The route contains 'name' (string) and 'data' (plain object);
 * the data also contains 'event' (plain Object or event object). 
 * 
 * If the response code is success:
 * if the'event' contains 'name' (string) then it will be emitted an event named the 'event.name', else named the 'route.name'
 * The emitted event will pass the parameter which is event combined with 'route.data'
 */
axios.interceptors.request.use(
  async (config) => {
    let headers = {};
    useTechpubStore().showLoadingBar = true;
    if (config.route) {
      try {
        let data = config.route.data;
        if(data.updated_at || (data instanceof FormData && data.get('updated_at'))){
          headers['If-Modified-Since'] = data.updated_at;
          data.delete ? data.delete('updated_at') : delete data.updated_at;
        }
        const route = useTechpubStore().getWebRoute(config.route.name, data);
        config.url = route.url;
        config.method = route.method[0];
        config.data = route.params;      

      } catch (error) {
        throw new Error(error); 
      }
    }
    for(const i in headers){
      config.headers.set(i,headers[i]);
    }
    return config;
  },
);
axios.interceptors.response.use(
  (response) => {
    // console.log(window.response = response);
    useTechpubStore().showLoadingBar = false;
    useTechpubStore().Errors = [];
    // if(response.config.event && response.config.event.name) {
    //   csdb.config.globalProperties.emitter.emit(response.config.event.name, Object.assign(response.config.event, response.config.route.data));
    // } else {
    // }
    if(response.config.route){
      csdb.config.globalProperties.emitter.emit(response.config.route.name, response.config.route.data);
    }
    csdb.config.globalProperties.emitter.emit('flash', {
      isSuccess: true,
      message: response.data.message
    });
    return response;
  },
  (axiosError) => {
    window.axiosError = axiosError; // jangan dihapus. Untuk dumping jika error pada user
    useTechpubStore().showLoadingBar = false;
    // if (axiosError.code === 'ERR_BAD_REQUEST')
    if (axiosError.code){
      csdb.config.globalProperties.emitter.emit('flash', {
        isSuccess: false,
        errors: axiosError.response.data.errors,
        message: `<i>${axiosError.message}</i>` + '<br/>' + axiosError.response.data.message
      });
    } else {
      console.log(axiosError.stack);
    }
    return axiosError.response;
  }
);

window.csdb = csdb;
csdb.mount('#body');

// ####### end here



// delayer
// async function a(){
//   return new Promise(resolve => {
//     setTimeout(() => {
//       console.log('aaa');
//       resolve('respolved');
//     },2000);
//   });
// }
// await a();

// function createRandomString() {
//   return (Math.random() + 1).toString(36).substring(7);
// }
// axios.id = {};