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
      DMLList: [], // tidak  digunakan lagi

      /**
       * untuk csdb3
       */
      BREXList: [], // tidak  digunakan lagi
      BRDPList: [], // tidak  digunakan lagi
      // BRList:[],

      /**
       * untuk csdb3
       */
      OBJECTList: {},

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
      if (response.statusText === 'OK') {
        // this[`${type}_list`] = response.data;
        // this[`${type}_page`] = response.data.current_page;

        // ini jika ingin pakai nested path. Tapi servernya jangan di paginate. Jika di paginate, ganti 'response.data' menjadi 'response.data.data'
        window.allobj = response.data;
        response.data = Object.assign({}, response.data); // entah Array atau object, akan menjadi Object;
        const sorter = function (container, asc = 1) {
          let arr = Object.keys(container).sort(); // ascending;
          if (!asc) {
            arr = arr.sort(() => -1); // descending
          }
          arr = arr.sort((a, b) => {
            if (b.substring(0, 2) === '__') {
              return asc ? (b > a ? -1 : 1) : (b > a ? 1 : -1);
            } else {
              return asc ? (b > a ? 1 : -1) : (b > a ? -1 : 1)
            }
          });
          arr.forEach((e, i) => {
            if (e.substring(0, 2) === '__') {
              arr = array_move(arr, i, 0);
            }
          });
          return arr.reduce((obj, key) => {
            obj[key] = container[key];
            return obj;
          }, {});
        };

        /**
         * Untuk memindahkan index elemen array. Ini bisa dipakai oleh fungsi lain.
         */
        const array_move = function (arr, old_index, new_index) {
          if (new_index >= arr.length) {
            var k = new_index - arr.length + 1;
            while (k--) {
              arr.push(undefined);
            }
          }
          arr.splice(new_index, 0, arr.splice(old_index, 1)[0]);
          return arr;
        };

        const append = function (container, path, csdbObject, callback) {
          let exploded_path = path.split("/");
          let loop = 0;
          let maxloop = exploded_path.length;
          let folder = "__" + exploded_path[loop];
          while (loop < maxloop) {
            container[folder] = container[folder] || {};
            if (exploded_path[loop + 1]) {
              exploded_path = exploded_path.slice(1);
              let newpath = exploded_path.join("/");
              container[folder] = callback(container[folder], newpath, csdbObject, callback);
              return container;
            }
            loop++;
          }
          let containerLength = Object.keys(container[folder]).filter(e => parseInt(e.slice(0, -1)) || parseInt(e.slice(0, -1)) === 0 ? true : false).length;
          container[folder][containerLength + "_"] = csdbObject;
          container = sorter(container,0);
          return container;
        }
        let newobj = {};
        for (const i in response.data) {
          let obj = response.data[i];
          newobj = append(newobj, obj['path'], obj, append);
          delete response.data[i];
        }
        this[`${type}_list`] = newobj;
        // window.sorter = sorter;
        // window.append = append;
        // console.log(window.newobj = newobj);

        // console.log(window.rsp = response.data);
        // const append = function (arr, path, csdbObject, callback) {
        //   arr = Object.assign({},arr);
        //   let exploded_path = path.split("/");
        //   let loop = 0;
        //   let maxloop = exploded_path.length;
        //   let folder = "__" + exploded_path[loop];
        //   while (loop < maxloop) {
        //     arr[folder] = arr[folder] || [];
        //     if (exploded_path[loop + 1]) {
        //       exploded_path = exploded_path.slice(1);
        //       let newpath = exploded_path.join("/");
        //       arr[folder] = callback(arr[folder], newpath, csdbObject, callback);
        //       return arr;
        //       break;
        //     }
        //     loop++;
        //   }
        //   arr[folder] = Object.assign([], arr[folder]);
        //   arr[folder].push(csdbObject);
        //   arr[folder] = Object.assign({},arr[folder]);
        //   return arr;
        // }
        // response.data.forEach((obj, k) => {
        //   response.data = append(response.data, obj['path'], obj, append);
        //   delete response.data[k];
        // })
        // response.data = [Object.assign({}, response.data)];
        // this[`${type}_list`] = response.data;
        // console.log(window.data = response.data);


        // untuk dump di console.log
        // obj1 = {filename: 'foo1', path: 'csdb'};
        // obj11 = {filename: 'foo11', path: 'csdb/n219'};
        // obj12 = {filename: 'foo12', path: 'csdb/n219'};
        // obj111 = {filename: 'foo11', path: 'csdb/n219/amm'};
        // allobj = [obj1, obj11, obj12, obj111];

        // append = function(arr, path, csdbObject, callback){
        //     let exploded_path = path.split("/");
        //     let loop = 0;
        //     let maxloop = exploded_path.length;
        //     let folder = "__" + exploded_path[loop];
        //     while (loop < maxloop) {
        //         arr[folder] = arr[folder] || [];
        //         if(exploded_path[loop+1]){
        //             exploded_path = exploded_path.slice(1);
        //             let newpath = exploded_path.join("/");
        //             arr[folder] = callback(arr[folder], newpath, csdbObject, callback);
        //             return arr;
        //             break;
        //         }
        //         loop++;
        //     }
        //     arr[folder].push(csdbObject);
        //     return arr;
        // }
        // allobj.forEach((obj, k) => {
        //     allobj = append(allobj, obj['path'], obj, append);
        //     delete allobj[k];
        // });
        // allobj = Object.assign({},allobj)



        // window.objs = response.data;
        // let objs = response.data;
        // let newobjs = {};
        // for(const i in objs.data){
        //   let obj = objs.data[i];
        //   if(!newobjs[obj.path]){
        //    newobjs[obj.path] = [];
        //   }
        //   newobjs[obj.path].push(obj);
        // }
        // this[`${type}_list`] = newobjs;
        // return this[`${type}_list`];
      }
    },
    async goto(type, page = undefined) {
      this.get_list(type, { page: page });
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
      for (const [k, name] of Object.entries(arguments)) {
        let e = this.Errors.find(o => o[name]);
        if (e) {
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
        if (blob.type.includes('text') || blob.type.includes('xml')) {
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
    dml(filename) {
      if (filename) {
        return this.DMLList.find(v => v.filename == filename);
      }
    }
  }


})
