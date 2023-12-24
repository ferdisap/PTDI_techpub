<script>
import { useTechpubStore } from '../../../techpub/techpubStore';

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      showListObject: true
    }
  },
  props: ['projectName'],
  async mounted() {
    await this.techpubStore.setProject();
    let projectName = this.$props.projectName;
    let route = this.techpubStore.getWebRoute('api.get_csdb_object_data', { projectName: projectName });
    axios.get(route.url.toString())
    .then(response => this.techpubStore.setObjects(projectName, response.data))
    .catch(error => this.$root.error(error));
  },
}
</script>
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
            <th>Filename</th>
            <th>Title</th>
            <th>Description</th>
            <th>Status</th>
            <th>Last Modified</th>
            <th>Initiator</th>
            <th class="text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr class="hover:bg-blue-400" 
            v-if="techpubStore.project($props.projectName)"
            v-for="obj in techpubStore.project($props.projectName).objects">
            <td> <router-link
                :to="{ name: 'ObjectDetail', params: { projectName: $props.projectName, filename: obj.filename }, }">{{
                  obj.filename }}</router-link> </td>
            <td> not prepared yet </td>
            <td> {{ obj.description }} </td>
            <td> {{ obj.status }} </td>
            <td> {{ this.techpubStore.date(obj.updated_at) }} </td>
            <td> {{ obj.initiator.name }} </td>
            <td class="text-center">
              <router-link class="button"
                :to="{ name: 'ObjectUpdate', params: { projectName: $props.projectName, filename: obj.filename }, }">update</router-link>
              |
              <button class="button-danger">delete</button>
            </td>
          </tr>
      </tbody>
    </table>
  </div>
</div></template>