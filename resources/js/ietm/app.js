import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import Index from "./index.vue";
import Routes from "./routers.js";
import Cookies from 'js-cookie';
import { useIetmStore } from './ietmStore';

import axios from 'axios';
import { createPinia } from 'pinia';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

const ietm = createApp(Index);
const router = createRouter({
  routes: Routes,
  history: createWebHistory(),
});
const pinia = createPinia();

ietm.use(pinia);
ietm.use(router);
ietm.mount('#body');
window.ietm = ietm;

window.Cookies = Cookies;

ietm.getObjects = async (repoName, handler, params) => {
  console.log('getObjects');
  const url = new URL(window.location.origin + '/api/ietm/repo/' + repoName);
  let response = await axios.get(url);
  return response;
  axios.get(url)
    .then(response => {
      useIetmStore().setResponse(response);
      if (handler) {
        handler.call(this, params);
      }
    });
};

ietm.getRepos = async (token, handler, params) => {
  console.log('getRepos');
  const url = new URL(window.location.origin + "/api" + "/ietm/repo");
  url.search = new URLSearchParams({ tokenRepo: token });
  let response = await axios.get(url);
  return response;
  // if(response.status == 200){
  //   return response.data;
  // }
  axios.get(url)
    .then(response => {
      Cookies.set('tokenRepo', token);
      useIetmStore().setResponse(response);
      useIetmStore().setTokenRepo(token);
      if(handler){
        handler.call(this, params);
      }
    });
};

ietm.getDetailObject = async (repoName, filename, handler, params) => {
  console.log('getDetailObject');
  const url = new URL(window.location.origin + "/api" + `/ietm/${repoName}/${filename}`);
  let response = await axios.get(url);
  return response;
  // return response;
  axios.get(url)
   .then(response => {
      useIetmStore().setResponse(response);
      if(handler){
        handler.call(this, params);
      }
   })
};
