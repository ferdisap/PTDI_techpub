<script>
import axios from 'axios';
import { useTechpubStore } from '../../../techpub/techpubStore';
import Sort from '../subComponents/Sort.vue';

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      showListObject: true,
      sortOrder: {},
    }
  },
  components: {Sort},
  props: ['projectName'],
  methods:{
    sort(status){
      this.techpubStore.sortObjects(this.$props.projectName,status,this.sortOrder[status]);
      this.sortOrder[status] = !this.sortOrder[status];
    },
    crudObject(filename, order){
      let route;
      if(order == 'delete'){
        route = this.techpubStore.getWebRoute('api.get_delete_csdb_object',{project_name: this.$props.projectName, filename:filename});
      }
      else if(order == 'restore'){
        route = this.techpubStore.getWebRoute('api.get_restore_csdb_object',{project_name: this.$props.projectName, filename:filename});
      }
      axios({
        url: route.url,
        method: route.method[0],
        data: route.params,
      })
      .then((response) => {
        this.$root.success(response);
        window.techpubStore = this.techpubStore;
        window.object = techpubStore.object(this.$props.projectName, filename);
        let newObject = response.data.object;
        let object = techpubStore.object(this.$props.projectName, filename);
        for(const col in object){
          if(newObject[col]){
            object[col] = newObject[col];
          }
        };

      })
      .catch((error) => this.$root.error(error));
    },
  },
  async mounted() {
    // window.store = this.techpubStore;
    await this.techpubStore.setProject();
    let projectName = this.$props.projectName;
    let route = this.techpubStore.getWebRoute('api.get_csdb_object_data', { project_name: projectName });
    axios.get(route.url.toString())
    .then(response => this.techpubStore.setObjects(projectName, response.data))
    .catch(error => this.$root.error(error));
  },
}
</script>

<style>
th {
  white-space: nowrap;
}
</style>

<template>
  <div class="w-full mt-3">
    <h1 class="text-center">Detail of {{ $props.projectName }}</h1>
    <div class="w-full text-center mb-3">
      <button :class="[showListObject ? 'border-b-black border-b-4' : '', 'button-nav']"
        @click="showListObject = !showListObject">List Object</button>
    </div>

    <!-- List Object -->
    <div v-show="showListObject">
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>Filename <Sort :function="sort.bind(this,'filename')"/></th>
            <th>Title <Sort :function="sort.bind(this,'title')"/></th>
            <th>Description <Sort :function="sort.bind(this,'description')"/></th>
            <th>Status <Sort :function="sort.bind(this,'status')"/></th>
            <th>Last Modified <Sort :function="sort.bind(this,'updated_at')"/></th>
            <th>Initiator <Sort :function="sort.bind(this,'name')"/></th>
            <th class="text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr class="hover:bg-blue-400" 
            v-if="techpubStore.project($props.projectName)"
            v-for="(obj, k) in techpubStore.project($props.projectName).objects">
            <td> {{ k+1 }} </td>
            <td> <router-link
                :to="{ name: 'ObjectDetail', params: { projectName: $props.projectName, filename: obj.filename }, }">{{
                  obj.filename }}</router-link> </td>
            <td> {{ obj.remarks.title }} </td>
            <td> {{ obj.description }} </td>
            <td> {{ obj.status }} </td>
            <td> {{ this.techpubStore.date(obj.updated_at) }} </td>
            <td> {{ obj.initiator.name }} </td>
            <td class="text-center">
              <!-- <router-link class="button" :to="{ name: 'ObjectUpdate', params: { projectName: $props.projectName, filename: obj.filename }, }">update</router-link> -->
              <a class="button" :href="techpubStore.getWebRoute('',{ projectName: $props.projectName, filename: obj.filename }, $router.getRoutes().find(v => v.name == 'ObjectUpdate'))['url']">Update</a>
              <button class="button-danger" @click="crudObject(obj.filename, 'delete')">delete</button>
              <button class="button-safe" @click="crudObject(obj.filename, 'restore')">restore</button>
            </td>
          </tr>
      </tbody>
    </table>
  </div>
</div></template>