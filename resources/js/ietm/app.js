import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from "./App.vue";
import Routes from "./routers.js";
import Cookies from 'js-cookie';
import { useIetmStore } from './ietmStore';
import $ from 'jquery';

import axios from 'axios';
import { createPinia } from 'pinia';

window.$ = $;
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

const ietm = createApp(App);
const router = createRouter({
  routes: Routes,
  history: createWebHistory(),
});
const pinia = createPinia();

ietm.use(pinia);
ietm.use(router);
ietm.mount('#body');
window.ietm = ietm;
//  untuk delete all cookies
// document.cookie.split(";").forEach(function(c) { document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); });
window.Cookies = Cookies;

ietm.goto = (link) => {
  router.push(link);
}

ietm.clickImg = function(url) {
  useIetmStore().entity.filename = url;
  useIetmStore().show = true;
  useIetmStore().showEntity = true;
}