/**, csdb3
 * WebRoutes: {
    "passw,

/**, csdb3
 * WebRoutes: {
    "passw
    ord.reset": {
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

export const useTechpubStore = defineStore('useTechpubStore', {
  state: () => {
    return {
      Auth: {},
      WebRoutes: {},
      Project: [],
      Errors: [],

      showLoadingBar: false,
      showIdentSection: true,

      isOpenICNDetailContainer: false,

      /**
       * awalnya digunakan untuk mengirim data dari ObjectDetail to ObjectUpdate.
       * Selanjutnya diharapkan bisa dipakai untuk menggantikan transformedObject
       */
      currentDetailObject: {
        model: undefined,
        blob: undefined, // transformed csdb object is blob
        // filename: undefined, // filename harusnya tidak perlu karena ada didalam
        projectName: undefined,
        // transformed: undefined, // harusnya tidak diperlukan karena sudah ada blob. Agar menghemat memory
      }, // blob object

      /**
       * untuk DML app, csdb3
       * kayaknya ini tidak dipakai karena nanti pakai DMLList di IndexDML.vue
       */
      DMLList:[], // tidak  digunakan lagi

      /**
       * untuk csdb3
       */
      BREXList:[], // tidak  digunakan lagi
      BRDPList:[], // tidak  digunakan lagi
      // BRList:[],

      /**
       * untuk csdb3
       */
      OBJECTList:{},

      /**
       * digunakan saat Upload.vue ke Editor.vue
       */
      readText: '',

      /** pengganti fitur di App.vue */
      isSuccess: true,
      errors: undefined,
      message: undefined,
      
    }
  },
  actions: {
    /**
     * 
     * @param {string} type 'dml','csl', 'brex', 'brdp'
     * @param {*} params 
     */
    async get_list(type, params = {}) {
      params.filenameSearch = this[`${type}_filenameSearch`] ?? '';
      let response = await axios({
        route: {
          name: `api.get_${type}_list`,
          data: params
        }
      })
      if(response.statusText === 'OK'){
        this[`${type}_list`] = response.data;
        this[`${type}_page`] = response.data.current_page;
      }
    },
    async goto(type,page = undefined) {
      this.get_list(type, {page: page});
    },

    isEmpty(value) {
      return (value == null || value === '' || (typeof value === "string" && value.trim().length === 0));
    },
    date(str) {
      return (new Date(str)).toLocaleDateString('en-EN', {
        year: 'numeric', month: 'short', day: 'numeric'
      });
    },

    /**
     * @param {*} name 
     * @param {*} params 
     * @param {*} Plain Object which get same from this.WebRoute[`${name}`]
     * @returns {Object} if route method is get then all params has attached to URL, otherwise is attached in route.params 
     * returned Object will have params (plain Object) if the method is POST;
     */
    getWebRoute(name, params = {}, route = undefined) {
      let formData;
      if (params instanceof FormData) {
        formData = params;
        params.delete('http://www.w3.org/1999/xhtml');
        let a = {};
        for (const [key, value] of params) {
          a[key] = value;
        }
        params = a;
      }
      if (route) {
        route.params = params;
      } else {
        route = Object.assign({}, this.WebRoutes[name]);
      }
      for (const p in route.params) {
        if (!params[p]) {
          throw new Error(`Parameter '${p}' shall be exist.`);
        }
        else {
          route.path = route.path.replace(`:${p}`, params[p]);
          // delete params[p];
          // if(formData){
          //   formData.delete(p);
          // }
        }
      }
      if (!route.method || route.method.includes('GET')) {
        let url = new URL(window.location.origin + route.path);
        url.search = new URLSearchParams(params);
        route.url = url;
      }
      else if (route.method && route.method.includes('POST')) {
        route.params = formData ?? params;
        route.url = new URL(window.location.origin + route.path);
      }
      return route;

    },
    // async setProject() {
    //   if (this.Project.length == 0) {
    //     this.showLoadingBar = true;
    //     let url = this.getWebRoute('api.get_index_project').path;
    //     let response = await axios.get(url)
    //     if (response.statusText === 'OK') {
    //       this.Project = response.data;
    //     }
    //     this.showLoadingBar = false;
    //   }
    // },

    /**
     * @param {string} projectName 
     * @param {Object} data 
     */
    // setObjects(projectName, data) {
    //   this.Project.find(v => v.name = projectName).objects = data;
    // },

    /**
     * get Project
     * @param {string} projectName 
     * @returns object
     */
    // project(projectName) {
    //   return this.Project.find(v => v.name == projectName);
    // },

    /**
     * get objects from project
     */
    // object(projectName, filename) {
    //   let pr = this.project(projectName);
    //   if (!pr) {
    //     return false;
    //   }
    //   else if (!pr.objects) {
    //     return false;
    //   }
    //   else {
    //     return pr.objects.find(v => v.filename == filename);
    //   }
    // },

    /**
     * sort object
     */
    // sortObjects(projectName, key, ascending = true) {
    //   let objects = this.project(projectName).objects;

    //   // karena objects[i]['initiator'] = {name: '...', email: '...'}
    //   if ((key == 'name')) {
    //     let sorted_array = Object.entries(objects).sort((a, b) => {
    //       const sortA = a[1][key]['name'].toUpperCase();
    //       const sortB = b[1][key]['name'].toUpperCase();
    //       if (ascending) {
    //         return sortA < sortB ? 1 : (sortA > sortB ? -1 : 0);
    //       } else {
    //         return sortA < sortB ? -1 : (sortA > sortB ? 1 : 0);
    //       }
    //     });
    //     for (let i = 0; i < sorted_array.length; i++) {
    //       this.project(projectName).objects[i] = sorted_array[i][1];
    //     }
    //   }
    //   else if ((key == 'title')) {
    //     let sorted_array = Object.entries(objects).sort((a, b) => {
    //       const sortA = a[1]['remarks'][key].toUpperCase();
    //       const sortB = b[1]['remarks'][key].toUpperCase();
    //       if (ascending) {
    //         return sortA < sortB ? 1 : (sortA > sortB ? -1 : 0);
    //       } else {
    //         return sortA < sortB ? -1 : (sortA > sortB ? 1 : 0);
    //       }
    //     });
    //     for (let i = 0; i < sorted_array.length; i++) {
    //       this.project(projectName).objects[i] = sorted_array[i][1];
    //     }
    //   }
    //   else {
    //     let sorted_array = Object.entries(objects).sort((a, b) => {
    //       if (!(a[1][key] && b[1][key])) {
    //         return 0;
    //       }
    //       const sortA = a[1][key].toUpperCase();
    //       const sortB = b[1][key].toUpperCase();
    //       if (ascending) {
    //         return sortA < sortB ? 1 : (sortA > sortB ? -1 : 0);
    //       } else {
    //         return sortA < sortB ? -1 : (sortA > sortB ? 1 : 0);
    //       }
    //     });
    //     for (let i = 0; i < sorted_array.length; i++) {
    //       this.project(projectName).objects[i] = sorted_array[i][1];
    //     }
    //   }
    // },

    /**
     * untuk form input name
     * @param {string} name 
     * @returns array contains error text
     */
    error(name) {
      let err = [];
      for(const [k,name] of Object.entries(arguments)){
        let e = this.Errors.find(o => o[name]);
        if(e){
          err = err.concat(e[name]);
        }
      }
      return err;
      // let err = this.Errors.find(o => o[name]); // return array karena disetiap hasil validasi error [{name: ['text1']},...]
      // if (err) {
      //   return err[name];  // return array ['text1', 'text2', ...] 
      // }
    },

    /** pengganti fitur pada App.vue */
    async set_error(axiosError) {
      axiosError.response.data.errors ? (this.errors = axiosError.response.data.errors) : (this.errors = undefined);
      axiosError.response.data.message ? (this.message = axiosError.message + ': ' + axiosError.response.data.message) : (this.message = axiosError.message);
      this.isSuccess = false;
      this.showLoadingBar = false;
    },
    set_success(response, isSuccess = true) {
      this.errors = undefined;
      this.isSuccess = true;
      this.message = response.data.message ? response.data.message : '';
      this.showLoadingBar = false;
    },

    // return src:blob untuk ICN atau blob.text() jika xml
    // return Promise
    async getCurrentDetailObject(output = '', object = {}) {
      const blob = object.blob ?? this.currentDetailObject.blob;
      if (!blob) {
        return ['', ''];
      }
      window.blob = blob;
      if (output == 'srcblob') {
        const url = URL.createObjectURL(blob);
        return [output, url.toString()];
      }
      else if (output == 'text') {
        return [output, await blob.text()];
      }
      else if (output == '') {
        if(blob.type.includes('text') || blob.type.includes('xml')) {
          return [blob.type, await blob.text()];
        } else {
          const url = URL.createObjectURL(blob);
          return [blob.type, url.toString()];
        }
      }
      // return (async () => {
      // })();
    },

    async setCurrentDetailObject_blob() {
      this.showLoadingBar = true;
      const route = this.getWebRoute('api.get_transform_csdb', { project_name: this.currentDetailObject['projectName'], filename: this.currentDetailObject.model['filename'] });
      await axios({
        url: route.url,
        method: route[0],
        data: route.params,
        responseType: 'blob',
      })
        .then(async (response) => {
          this.currentDetailObject.blob = response.data;
        });
    },

    /**
     * object = {model: ..., blob: ...}
     * */
    async setCurrentObject_model(object, projectName, filename) {
      if (!this.currentDetailObject.model) {
        this.showLoadingBar = true;
        const route = this.getWebRoute('api.get_csdb_object_data', { project_name: projectName, filename: filename });
        await axios({
          url: route.url,
          method: route[0],
          data: route.params,
        })
          .then(response => {
            this.currentDetailObject.model = response.data[0];
          })
          .catch(error => this.$root.error(error));
        this.currentDetailObject['projectName'] = projectName;
      } else {
        this.currentDetailObject.model = object ? object : this.currentDetailObject.model;
      }
      this.showLoadingBar = false;
    },

    /**
     * get one dml from DMLList
     * @param {string} filename 
     */
    dml(filename){
      if(filename){
        return this.DMLList.find(v => v.filename == filename);
      }
    }
  }


})
