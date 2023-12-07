import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import Index from "./index.vue";
import Routes from "./routers.js";

import axios from 'axios';
import {createPinia} from 'pinia';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const ietm = createApp(Index);
const router = createRouter({
  routes: Routes,
  history: createWebHistory(),
})
const pinia = createPinia();

ietm.use(pinia);
ietm.use(router);

ietm.mount('#body');
window.ietm = ietm;

