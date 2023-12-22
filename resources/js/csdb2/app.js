import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from "./App.vue";
import Routes from "./routers.js";
import Cookies from 'js-cookie';
// import { useIetmStore } from './ietmStore';
import $ from 'jquery';

import axios from 'axios';
import { createPinia } from 'pinia';

window.$ = $;
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
window.csdb = csdb;
//  untuk delete all cookies
// document.cookie.split(";").forEach(function(c) { document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); });
window.Cookies = Cookies;

// async function a(){
//   return new Promise(resolve => {
//     setTimeout(() => {
//       console.log('aaa');
//       resolve('respolved');
//     },2000);
//   });
// }
// await a();

// sebelum mounting app, akan request all Routes dulu
await axios.get('/getAllRoutes')
  .then(response => {
    window.WebRoutes = response.data;
  })
csdb.mount('#body');
// ietm.goto = (link) => {
//   router.push(link);
// }

// ietm.clickImg = function(url) {
//   useIetmStore().entity.filename = url;
//   useIetmStore().show = true;
//   useIetmStore().showEntity = true;
// }
