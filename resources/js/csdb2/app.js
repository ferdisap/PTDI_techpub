import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from "./App.vue";
import Routes from "./routers.js";
// import $ from 'jquery';
// import $ from 'maphilight';

import axios from 'axios';
import { createPinia } from 'pinia';
import References from '../References';

// window.$ = $;
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-Token'] = document.querySelector("meta[name='csrf-token']").content;
axios.defaults.withCredentials = true;

const csdb = createApp(App);
const router = createRouter({
  routes: Routes,
  history: createWebHistory(),
});
const pinia = createPinia();

csdb.use(pinia);
csdb.use(router);
csdb.use({
  install: (app) => {
    app.config.globalProperties.References = References;
  }
});
// window.csdb = csdb;



// sebelum mounting app, akan request all Routes dulu
await axios.get('/getAllRoutes')
  .then(response => {
    window.WebRoutes = response.data;
  })
csdb.mount('#body');





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
