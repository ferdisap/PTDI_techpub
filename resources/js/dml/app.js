import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from "./App.vue";
import Routes from "./routers.js";
import axios from 'axios';
import { createPinia } from 'pinia';
import jQuery from 'jquery';
import { useTechpubStore } from '../techpub/techpubStore';

window.$ = jQuery;
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-Token'] = document.querySelector("meta[name='csrf-token']").content;
axios.defaults.withCredentials = true;

const dml = createApp(App);
const router = createRouter({
  routes: Routes,
  history: createWebHistory(),
});
const pinia = createPinia();

dml.use(pinia);
dml.use(router);



// sebelum mounting app, akan request all Routes dulu
await axios.get('/getAllRoutes')
  .then(response => {
    window.WebRoutes = response.data;
    useTechpubStore().WebRoutes = response.data;
  })
dml.mount('#body');
