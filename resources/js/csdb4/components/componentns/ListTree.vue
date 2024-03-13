<script>
import { list } from 'postcss';
import { useTechpubStore } from '../../../techpub/techpubStore';
export default {
  data() {
    return {
      data: {},
      html: '',
    }
  },
  props: {
    type: String,
  },
  methods: {
    /**
     * require array_move and sorter from helper.js
     * @param {string} type 'dml','csl', 'brex', 'brdp'
     * @param {Object} params 
     */
    async get_list(type, params = {}) {
      params.listtree = 1;
      let response = await axios({
        route: {
          name: `api.get_${type}_list`,
          data: params
        }
      })
      if (response.statusText === 'OK') {
        // sortir berdasarkan path
        response.data.data = response.data.data.sort((a, b) => {
          return a.path > b.path ? 1 : (a.path < b.path ? -1 : 0);
        });
        // sortir object dan level path nya eg: "/csdb/n219/amm" berarti level 3
        let obj = {};
        let levels = {};
        for (const v of response.data.data) {
          let path = v.path
          let split = path.split("/");
          let l = split.length;

          let p = [];
          for (let i = 1; i <= l; i++) {
            p.push(split[i-1]);
            levels[i] = levels[i] ?? [];
            levels[i].push(p.join("/"));
          }
          levels[l].indexOf(path) < 0 ? levels[l].push(path) : '';

          // let l = path.split("/").length;
          // levels[l] = levels[l] ?? [];
          // levels[l].indexOf(path) < 0 ? levels[l].push(path) : '';

          obj[path] = obj[path] || [];
          obj[path].push(v);
        }
        this.data[`${type}_list`] = obj;
        this.data[`${type}_list_level`] = levels;
        // console.log(window.obj = obj, window.lvl = levels);
      }
    },
    async goto(type, page = undefined) {
      this.get_list(type, { page: page });
    },
    clickFolder(data) {
      this.emitter.emit('clickFolderFromListTree', data);
    },
    clickFilename(data) {
      // this.$parent.$emit('clickFilename', data)
      this.emitter.emit('clickFilenameFromListTree', data); // key path dan filename
    },
    createListTreeHTML() {
      const gen_objlist = function (models, style = '') {
        let listobj = '';
        if(models){ // ada kemungkinan models undefined karena path "csdb/n219/amm", csdb/n219 nya tidak ada csdbobject nya
          for (const model of models) {
            let logo = model.filename.substr(0,3) === 'ICN' ? `<span class="material-symbols-outlined text-sm">mms</span>&#160;` : `<span class="material-symbols-outlined text-sm">description</span>&#160;`;
            listobj = listobj + `
                  <div class="obj" style="${style}">
                    ${logo}<a href="#" @click.prevent="$parent.clickFilename({path:'${model.path}',filename: '${model.filename}'})">${model.filename}</a>
                  </div>`
          }
        }
        return listobj
      };
      const path_yang_sudah = [];
      const fn = (start_l = 1, leveldata = {}, dataobj = {}, callback, parentPath = '') => {
        let details = '';
        let defaultMarginLeft = 5;
        if (leveldata[start_l]) {

          for (const path of leveldata[start_l]) { // untuk setiap path 'csdb' dan 'xxx'

            let pathSplit = path.split("/");
            let currFolder = pathSplit[start_l - 1];
            pathSplit.splice(pathSplit.indexOf(currFolder), 1);
            let parentP = pathSplit.join("/");

            if (path_yang_sudah.indexOf(path) >= 0
            || path_yang_sudah.indexOf(parentP) >= 0
            || parentP !== parentPath // expresi ini membuat path tidak di render
            ) {
              continue;
            }
            let isOpen = this.data.open ? this.data.open[path] : false;
            isOpen = isOpen ? 'open' : '';

            // generating folder list
            details = details + `
            <details ${isOpen} style="margin-left:${start_l * 3 + defaultMarginLeft}px;" path="${path}" @click="clickDetails($el)">
              <summary>
                <a href="#" @click.prevent="$parent.clickFolder({path: '${path}'})">${currFolder}</a>
              </summary>`;

            if (leveldata[start_l + 1]) {
              details = details + (callback.bind(this, start_l + 1, leveldata, dataobj, callback, path))();
            }

            // generating obj list
            details = details + gen_objlist(dataobj[path], `margin-left:${start_l * 3 + defaultMarginLeft + 2}px;`);

            details = details + "</details>"

            path_yang_sudah.push(path);
          }
        }
        return details
      };

      this.html = fn(1, this.data[`${this.$props.type}_list_level`], this.data[`${this.$props.type}_list`], fn);
    },

    /*
     * akan mendelete jika newModel tidak ada
    */
    deleteList(filename) {
      return Object.entries(this.data[`${this.$props.type}_list`]).find(arr => {
        let find = arr[1].find(v => v.filename === filename);
        if (find) {
          let index = arr[1].indexOf(find);
          this.data[`${this.$props.type}_list`][arr[0]].splice(index, 1);
        }
        return find;
      });
    },
    pushList(model) {
      let path = model.path;
      this.data[`${this.$props.type}_list`][path] = this.data[`${this.$props.type}_list`][path] ?? [];
      this.data[`${this.$props.type}_list`][path].push(model);

      let split = model.path.split("/");
      let level = split.length;
      let p = [];
      for (let i = 1; i <= level; i++) {
        p.push(split[i-1]);
        this.data[`${this.$props.type}_list_level`][i] = this.data[`${this.$props.type}_list_level`][i] ?? [];
        this.data[`${this.$props.type}_list_level`][i].push(p.join("/"));
      }
      let foundLevel = Object.entries(this.data[`${this.$props.type}_list_level`]).find(arr =>  arr[0] == level); // output ['level', Array containing path];;
      if(foundLevel && !(this.data[`${this.$props.type}_list_level`][foundLevel[0]].find(v => v === model.path))){ // agar tidak terduplikasi path nya
        this.data[`${this.$props.type}_list_level`][foundLevel[0]].push(model.path)
      } 
    }
  },
  async mounted() {
    this.emitter.on('ListTree-refresh', (data) => {
      //data adalah model SQL Csdb Object
      if (data) { 
        if (this.deleteList(data.filename)) {
          this.pushList(data);
        }
      } else {
        this.get_list(this.$props.type);
      }
      this.createListTreeHTML();
    });

    this.emitter.on('ListTree-remove', (data) => {
      //data adalah model SQL Csdb Object
      this.deleteList(data.filename);
      this.createListTreeHTML();
    })

    this.emitter.on('ListTree-add', (data) => {
      //data adalah model SQL Csdb Object
      this.pushList(data);
      this.createListTreeHTML();
    })

    await this.get_list(this.$props.type);
    this.createListTreeHTML();
  },
  computed: {
    listobject() {
      return this.data[`${this.$props.type}_list`] ?? {};
    },
    pageless() {
      return this.paginationInfo['current_page'] > 1 ? this.paginationInfo['current_page'] - 1 : 1;
    },
    pagemore() {
      return (this.paginationInfo['current_page'] < this.paginationInfo['last_page']) ? this.paginationInfo['current_page'] + 1 : this.paginationInfo['last_page']
    },
    level() {
      return this.data[`${this.$props.type}_list_level`] ?? {}
    },
    tree() {
      return {
        template: this.html,
        computed: {
          parent() {
            return this.$parent;
          }
        },
        methods: {
          clickDetails(element) {
            setTimeout(() => {
              let path = element.getAttribute('path');
              this.$parent.data.open = this.$parent.data.open ?? {};
              this.$parent.data.open[path] = element.open;
            }, 0);
          }
        },
      }
    }
  },
}
</script>
<template>
  <div class="listtree h-full">
    <!-- list -->
    <div :class="['listtree-list', $props.isRoot ? 'h-[90%] overflow-auto' : '']">
      <!-- <component v-if="(this.data[`${this.$props.type}_list_level`] && this.data[`${this.$props.type}_list`])" :is="tree" /> -->
      <component v-if="html" :is="tree" />
    </div>
  </div>
</template>