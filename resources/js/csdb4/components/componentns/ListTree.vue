<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
export default {
  data() {
    return {
      data: {},
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
          let l = path.split("/").length - 1; // kurang 1 karena diujung ada "/"
          levels[l] = levels[l] ?? [];
          levels[l].indexOf(path) < 0 ? levels[l].push(path) : '';

          obj[path] = obj[path] || [];
          obj[path].push(v);
        }
        this.data[`${type}_list`] = obj;
        this.data[`${type}_list_level`] = levels;
        console.log(window.obj = obj, window.lvl = levels);
      }
    },
    async goto(type, page = undefined) {
      this.get_list(type, { page: page });
    },
    clickFolder(data) {
      // this.$parent.$emit('clickFolder',data);
      this.emitter.emit('clickFolderFromListTree', data);
    },
    clickFilename(data) {
      // this.$parent.$emit('clickFilename', data)
      this.emitter.emit('clickFilenameFromListTree', data);
    },
  },
  mounted() {
    window.lt = this;
    this.get_list(this.$props.type);
    // this.$props.objects agar object bisa di kirim ke Folder.vue.
    // this.$props.isRoot agar pagination tidak di render untuk anakn2nya
    // sebenernya cukup hanya $props.type saja untuk jalankan Listtree ini (tidak perlu di masukkan sebagai props di template html dibawah)
    // if (this.$props.type && !this.$props.objects && this.$props.isRoot) {
    //   this.get_list(this.$props.type);
    // }
  },
  computed: {
    listobject() {
      // return this.data[`${this.$props.type}_list`] ?? this.$props.objects;
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
      const gen_objlist = function (models, style = '') {
        let listobj = '';
        for (const model of models) {
          listobj = listobj + `
                <div class="obj" style="${style}">
                  <a href="#" @click.prevent="$parent.clickFilename({path:'${model.path}',filename: '${model.filename}'})">${model.filename}</a>
                </div>              `
        }
        return listobj
      };
      const path_yang_sudah = [];
      const createHTML = function (start_l = 1, leveldata = {}, dataobj = {}, callback, parentPath = '') {
        let details = '';
        let defaultMarginLeft = 5;
        // let listobj = '';
        if (leveldata[start_l]) {

          for (const path of leveldata[start_l]) { // untuk setiap path 'csdb' dan 'xxx'

            let pathSplit = path.split("/");
            let currFolder = pathSplit[start_l - 1];
            pathSplit.splice(pathSplit.indexOf(currFolder), 1);
            let parentP = pathSplit.join("/");
            // console.log(pathSplit, currFolder, parentP, parentPath);

            if (path_yang_sudah.indexOf(path) >= 0
              || path_yang_sudah.indexOf(parentP) >= 0
              || parentP !== parentPath
            ) {
              continue;
            }

            // generating folder list
            details = details + `
              <details style="margin-left:${start_l * 3 + defaultMarginLeft}px;">
                <summary>
                  <a href="#" @click.prevent="$parent.clickFolder({path: '${path}'})">${currFolder}</a>
                </summary>
            `;

            if (leveldata[start_l + 1]) {
              details = details + callback(start_l + 1, leveldata, dataobj, callback, path);
            }

            // generating obj list
            details = details + gen_objlist(dataobj[path], `margin-left:${start_l * 3 + defaultMarginLeft + 2}px;`);

            details = details + "</details>"
            // details = details + "</div></div>"

            path_yang_sudah.push(path);
          }
        }
        return details
      }
      let html = createHTML(1, this.data[`${this.$props.type}_list_level`], this.data[`${this.$props.type}_list`], createHTML);
      return {
        template: html,
      }
    }
  },
}
</script>
<template>
  <div class="listtree h-full">
    <!-- list -->
    <div :class="['listtree-list', $props.isRoot ? 'h-[90%] overflow-auto' : '']">
      <component 
      v-if="(this.data[`${this.$props.type}_list_level`] && this.data[`${this.$props.type}_list`])" 
      :is="tree" />
    </div>
  </div>
</template>