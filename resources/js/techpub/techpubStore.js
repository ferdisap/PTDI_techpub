/**
 * WebRoutes: {
    "password.reset": {
        "name": "password.reset",
        "method": [
            "GET",
            "HEAD"
        ],
        "path": "/reset-password/:token",
        "params": {
            "token": ":token"
        }
    },
  }

  Auth: {
    name: '',
    email: ''
  },

  Errors: [ {name: ['text1']}, ... ]
 */

import { defineStore } from 'pinia';
import References from '../References';

export const useTechpubStore = defineStore('useTechpubStore', {
  state: () => {
    return {
      Auth: {},
      WebRoutes: {},
      Project: [],
      Errors: [],
      
      showLoadingBar: false,
      showIdentSection: true,
      
      isOpenICNDetailContainer : false,
    }
  },
  actions: {
    isEmpty(value) {
      return (value == null || value === '' || (typeof value === "string" && value.trim().length === 0));
    },
    date(str) {
      return (new Date(str)).toLocaleDateString('en-EN', {
        year: 'numeric', month: 'short', day: 'numeric'
      });
    },
    getWebRoute(name, params = {}) {
      if(params instanceof FormData){
        params.delete('http://www.w3.org/1999/xhtml');
        let a = {};
        for(const [key,value] of params){
          a[key] = value;
        }
        params = a;
      }
      let route = Object.assign({}, this.WebRoutes[name]);
      for (const p in route.params) {
        if (!params[p]) {
          throw new Error(`Parameter '${p}' shall be exist.`);
        }
        else {
          route.path = route.path.replace(`:${p}`, params[p]);
          delete params[p];
        }
      }
      if (route.method.includes('GET')) {
        let url = new URL(window.location.origin + route.path);
        url.search = new URLSearchParams(params);
        route.url = url;
      }
      else if (route.method.includes('POST')) {
        route.params = params
        route.url = new URL(window.location.origin + route.path);
      }
      return route;

    },
    async setProject() {
      if(this.Project.length == 0){
        this.showLoadingBar = true;
        let url = this.getWebRoute('api.get_index_project').path;
        let response = await axios.get(url)
        if (response.statusText === 'OK') {
          this.Project = response.data;
        }
        this.showLoadingBar = false;
      }
    },

    /**
     * 
     * @param {string} projectName 
     * @param {Object} data 
     */
    setObjects(projectName, data){
      this.Project.find(v => v.name = projectName).objects = data;
    },

    /**
     * get Project
     * @param {string} projectName 
     * @returns object
     */
    project(projectName){
      return this.Project.find(v => v.name == projectName);
    },

    /**
     * get objects from project
     */
    object(projectName, filename){
      let pr = this.project(projectName);
      if(!pr){
        return false;
      }
      else if(!pr.objects){
        return false;
      }
      else {
        return pr.objects.find( v => v.filename == filename);
      }
    },

    /**
     * sort object
     */
    sortObjects(projectName, key, ascending = true){
      let objects = this.project(projectName).objects;
      
      // karena objects[i]['initiator'] = {name: '...', email: '...'}
      if(key == 'initiator'){
        let sorted_array = Object.entries(objects).sort((a,b) => {
            const sortA = a[1][key]['name'].toUpperCase();
            const sortB = b[1][key]['name'].toUpperCase();
            if(ascending){
              return sortA < sortB ? 1 : (sortA > sortB ? -1 : 0);
            } else {
              return sortA < sortB ? -1 : (sortA > sortB ? 1 : 0);
            }
        });
        for (let i = 0; i < sorted_array.length; i++) {
            this.project(projectName).objects[i] = sorted_array[i][1];
        }
      }
      else{
        let sorted_array = Object.entries(objects).sort((a,b) => {
            if(!(a[1][key] && b[1][key])){
              return 0;
            }
            const sortA = a[1][key].toUpperCase();
            const sortB = b[1][key].toUpperCase();
            if(ascending){
              return sortA < sortB ? 1 : (sortA > sortB ? -1 : 0);
            } else {
              return sortA < sortB ? -1 : (sortA > sortB ? 1 : 0);
            }
        });
        for (let i = 0; i < sorted_array.length; i++) {
            this.project(projectName).objects[i] = sorted_array[i][1];
        }
      }
    },

    error(name){
      let err = this.Errors.find(o => o[name]); // return array karena disetiap hasil validasi error [{name: ['text1']},...]
      if(err){
        return err[name];  // return array ['text1', 'text2', ...] 
      }
    },
  }
})
