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
 */

import { defineStore } from 'pinia';

export const useTechpubStore = defineStore('useTechpubStore', {
  state: () => {
    return {
      Auth: {},
      WebRoutes: {},
      Project: [],
      showLoadingBar: false,
    }
  },
  actions: {
    isEmpty(value) {
      return (value == null || (typeof value === "string" && value.trim().length === 0));
    },
    date(str) {
      return (new Date(str)).toLocaleDateString('en-EN', {
        year: 'numeric', month: 'short', day: 'numeric'
      });
    },
    getWebRoute(name, params = {}) {
      let route = this.WebRoutes[name];
      if (route.method.includes('GET')) {
        for (const p in route.params) {
          if (!params[p]) {
            throw new Error(`Parameter '${p}' shall be exist.`);
          }
          else {
            route.path = route.path.replace(`:${p}`, params[p]);
            delete params[p];
          }
        }
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
    setObjects(projectName, data){
      console.log(this.Project[0]);
      this.Project.find(v => v.name = projectName).objects = data;
    },
    project(projectName){
      // console.log(this.Project, projectName);
      return this.Project.find(v => v.name = projectName);
    }
  }
})
