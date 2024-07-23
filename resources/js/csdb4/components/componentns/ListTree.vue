<script>
import { list } from 'postcss';
import { useTechpubStore } from '../../../techpub/techpubStore';
import setListTreeData from '../../../../../public/js/csdb/setListTreeData';
import ContinuousLoadingCircle from '../../loadingProgress/continuousLoadingCircle.vue';
import { isProxy, toRaw } from 'vue';
export default {
  data() {
    return {
      data: {},
      html: '',
      techpubStore: useTechpubStore(),
      showLoadingProgress: false,
    }
  },
  components: {ContinuousLoadingCircle},
  props: {
    type: String,
    routeName: String,
  },
  methods: {
    /**
     * require array_move and sorter from helper.js
     * @param {string} type 'dml','csl', 'brex', 'brdp'
     * @param {Object} params 
     */
    async get_list(type, params = {}) {
      params.listtree = 1;
      
      const worker = this.createWorker("WorkerListTree.js");
      if(worker){
        let route = this.techpubStore.getWebRoute(`api.get_${type}_list`, params);
        let prom = new Promise((resolve, reject) =>{
          worker.onmessage = (e) => {
            this.data[`${type}_list`] = e.data[0];
            this.data[`${type}_list_level`] = e.data[1];
            worker.terminate();
            this.showLoadingProgress = false;
            return resolve(true);
          };
        });
        worker.postMessage({
          mode: 'fetchData',
          data: {
            route:route,
          },
        });
        this.showLoadingProgress = true;
        return prom;
      } else {
        let response = await axios({
          route: {
            name: `api.get_${type}_list`,
            data: params
          }
        })
        if (response.statusText === 'OK') {
          let data = setListTreeData(response.data.csdbs);
          this.data[`${type}_list`] = data[0];
          this.data[`${type}_list_level`] = data[1];
          return Promise.resolve(true);
        }
        return Promise.resolve(false);
      }
    },
    async goto(type, page = undefined) {
      this.get_list(type, { page: page });
    },
    /**
     * data itu isinya path doang, lihat di WorkerListTree.js
    */
    clickFolder(data) {
      this.emitter.emit('clickFolderFromListTree', data);
    },
    /**
     * data itu isinya path, filename, viewType
    */
    clickFilename(data) {
      this.$router.push({
          name: this.$props.routeName,
          params: {
              filename: data.filename,
              viewType: data.viewType
          },
          query: this.$route.query
      });
      this.emitter.emit('clickFilenameFromListTree', data); // key path dan filename

    },
    /**
     * di workernya belum mendukung untuk html karena memang saya belum membuat XSL/ IETM untuk object nya
    */
    createListTreeHTML() {
      let hrefForPdf = this.techpubStore.getWebRoute('',{filename:':filename',viewType:'pdf'},Object.assign({},this.$router.getRoutes().find(r => r.name === this.$props.routeName)))['path'];
      let hrefForHtml = hrefForPdf.replace('pdf','html');
      let hrefForOther = hrefForPdf.replace('pdf','other');
      const worker = this.createWorker("WorkerListTree.js");
      if(worker){
        worker.onmessage = (e) => {
          this.html = e.data
          worker.terminate();
        };
        worker.postMessage({
          mode: 'createHTMLString',
          data: {
            start_l:  1,
            list_level: isProxy(this.data[`${this.$props.type}_list_level`]) ? toRaw(this.data[`${this.$props.type}_list_level`]) : this.data[`${this.$props.type}_list_level`],
            list: isProxy(this.data[`${this.$props.type}_list`]) ? toRaw(this.data[`${this.$props.type}_list`]) : this.data[`${this.$props.type}_list`],
            open: isProxy(this.data.open) ? toRaw(this.data.open) : this.data.open,
            hrefForPdf : hrefForPdf,
            hrefForHtml : hrefForHtml,
            hrefForOther : hrefForOther,
          }
        });
      }
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
        p.push(split[i - 1]);
        this.data[`${this.$props.type}_list_level`][i] = this.data[`${this.$props.type}_list_level`][i] ?? [];
        this.data[`${this.$props.type}_list_level`][i].push(p.join("/"));
      }
      let foundLevel = Object.entries(this.data[`${this.$props.type}_list_level`]).find(arr => arr[0] == level); // output ['level', Array containing path];;
      if (foundLevel && !(this.data[`${this.$props.type}_list_level`][foundLevel[0]].find(v => v === model.path))) { // agar tidak terduplikasi path nya
        this.data[`${this.$props.type}_list_level`][foundLevel[0]].push(model.path)
      }
    }
  },
  async mounted() {
    this.data.open = JSON.parse(top.localStorage.getItem('expandCollapseListTree'))
    this.emitter.on('ListTree-refresh', (data) => {
      //data adalah model SQL Csdb Object atau array contain csdb object (bukan meta objek nya)
      if(Array.isArray(data)){
        data.forEach((obj) => {
          if (this.deleteList(obj.filename)) this.pushList(obj);
        });
      }
      else if (data) {
        if (this.deleteList(data.filename)) this.pushList(data);
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

    this.emitter.on('ListTree-removeMultiple', (data) => {
      // data is array models contained several SQL Csdb Object model 
      data.forEach(model => {
        this.deleteList(model.filename);
      });
      this.createListTreeHTML();
    })

    /*
     *  DEPRECIATED, diganti oleh ListTree-refresh  
     */ 
    this.emitter.on('ListTree-add', (data) => {
      //data adalah model SQL Csdb Object
      this.pushList(data);
      this.createListTreeHTML();
    })

    this.emitter.on('ListTree-addMultiple', (data) => {
      //data adalah array yang tedapat banyak model SQL Csdb Object
      data.forEach(model => {
        this.pushList(model);
      })
      this.createListTreeHTML();
    })

    let list = await this.get_list(this.$props.type);
    if(list) this.createListTreeHTML();

    this.emitter.on('tesListtree',() => {
      console.log('tes Listtree');
    })
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
          // clickDetails(element) {
          //   setTimeout(() => {
          //     let path = element.getAttribute('path');
          //     this.$parent.data.open = this.$parent.data.open ?? {};
          //     this.$parent.data.open[path] = element.open;
          //   }, 0);
          // },
          expandCollapse(path){
            let details = $(`details[path='${path}']`)[0];
            details.open = !details.open;
            let icon = details.firstElementChild.firstElementChild;
            icon.innerHTML = details.open ? 'keyboard_arrow_down' : 'chevron_right' 
            if(!this.$parent.data.open){
              let expandCollapseListTreeFromLocalStorage = top.localStorage.getItem('expandCollapseListTree');
              if(expandCollapseListTreeFromLocalStorage){
                this.$parent.data.open = JSON.parse(expandCollapseListTreeFromLocalStorage)
              } else {
                this.$parent.data.open = {};
              }
            }
            this.$parent.data.open[path] = details.open;
            top.localStorage.setItem('expandCollapseListTree', JSON.stringify(this.$parent.data.open))
          }
        },
      }
    }
  },
}
</script>
<template>
  <div class="listtree h-full relative">
    <!-- list -->
    <div :class="['listtree-list', $props.isRoot ? 'h-[90%] overflow-auto' : '']">
      <!-- <component v-if="(this.data[`${this.$props.type}_list_level`] && this.data[`${this.$props.type}_list`])" :is="tree" /> -->
      <component v-if="html" :is="tree" />
    </div>
    <ContinuousLoadingCircle :show="showLoadingProgress"/>
  </div>
</template>