import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from "./App.vue";
import Routes from "./routers.js";

import axios from 'axios';
import { createPinia } from 'pinia';
import References from '../techpub/References';
import { useTechpubStore } from '../techpub/techpubStore';

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

await axios.get('/auth/check')
  .then(response => useTechpubStore().Auth = response.data)
  // .catch(response => window.location.href = "/login");
  .catch(response => null);
// sebelum mounting app, akan request all Routes dulu
await axios.get('/getAllRoutes')
  .then(response => {
    useTechpubStore().WebRoutes = response.data;
  });


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
