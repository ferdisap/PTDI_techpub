import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from "./App.vue";
import Routes from "./routers.js";

import axios from 'axios';
import { createPinia } from 'pinia';
import References from '../techpub/References';
import { useTechpubStore } from '../techpub/techpubStore';

import mitt from 'mitt';
import alert from '../alert';

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
// csdb.use({
//   install: (app) => {
//     app.config.globalProperties.References = References;
//   }
// });
csdb.config.globalProperties.References = References;
csdb.config.globalProperties.emitter = mitt();
csdb.config.globalProperties.alert = alert;

await axios.get('/auth/check')
  .then(response => useTechpubStore().Auth = response.data)
  .catch(response => window.location.href = "/login");
// sebelum mounting app, akan request all Routes dulu
await axios.get('/getAllRoutes')
  .then(response => {
    useTechpubStore().WebRoutes = response.data;
  });

  
csdb.mount('#body');

axios.interceptors.request.use(
  (request) => {
    useTechpubStore().showLoadingBar = true;
    return request;
  },
);
axios.interceptors.response.use(
  (response) => {
    useTechpubStore().showLoadingBar = false;
    return response;
  },
);





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
