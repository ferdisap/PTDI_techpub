<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
import ObjectUpdate from './ObjectUpdate.vue';

export default {
  data(){
    return {
      techpubStore: useTechpubStore(),
      showIndex: true,
      showCreateObject: false,
      showCreateProject: false,
      showCreateRepo: false,
    }
  },
  async mounted(){
    await this.techpubStore.setProject();
  },
  components: {ObjectUpdate},
  methods: {
    exportrepo(evt){
      this.techpubStore.showLoadingBar = true;
      this.techpubStore.Errors = [];
      const formData = new FormData(evt.target);
      axios({
        url: evt.target.action,
        method: evt.target.method,
        data: formData,
      })
        .then(response => this.$root.success(response))
        .catch(error => this.$root.error(error));
    },
  },
}
</script>
<template>
  <h1 class="text-center">PROJECT</h1>
  <div class="w-full text-center mb-3">
    <button :class="[showIndex ? 'border-b-black border-b-4' : '' ,'button-nav']" @click="showIndex = !showIndex">Index</button>
    <button :class="[showCreateObject ? 'border-b-black border-b-4' : '' ,'button-nav']" @click="showCreateObject = !showCreateObject">Create Object</button>
    <button :class="[showCreateProject ? 'border-b-black border-b-4' : '' ,'button-nav']" @click="showCreateProject = !showCreateProject">Create Project</button>
    <button :class="[showCreateRepo ? 'border-b-black border-b-4' : '' ,'button-nav']" @click="showCreateRepo = !showCreateRepo">Export to Repo</button>
  </div>

  <!-- Create Object -->
  <div v-if="showCreateObject" class="mb-6 mt-10 w-full max-w-2/3 bg-white border rounded-xl p-3">
    <ObjectUpdate :utility="'create'"/>
  </div>
  <hr/>

  <div class="w-full flex">
    <!-- Index -->
    <div class="w-full mr-3" v-show="showIndex">
      <h2 class="text-center">All Project</h2>
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Create Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="pr in techpubStore.Project" class="hover:bg-blue-400">
            <td> <router-link class="hover:underline" :to="{name:'ProjectDetail', params:{projectName: pr.name},}">{{ pr.name }}</router-link> </td>
            <td> {{ pr.description }} </td>
            <td> {{ this.techpubStore.date(pr.created_at) }} </td>
            <td> <button class="button-danger" @click="tes()">Delete</button> </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create -->
    <div class="block ml-3">
      <!-- Create Project -->
      <div v-if="showCreateProject" class="mb-6 mt-3 w-full max-w-2/3">
        <form :action="this.techpubStore.getWebRoute('password.reset',{token: 'foo'}).path" method="get" @submit.prevent="getRepo">
          <div class="mb-5">
            <h2 class="text-center">Create New Project</h2>
            <label for="name" class="block mb-2 text-gray-900 dark:text-white text-xl font-bold">Project Name</label>
            <span>Project name must be unique. There is not allowed two or more project with same name.</span>
            <input type="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required placeholder="name your project..">
            <br/>
            <label for="description" class="block mb-2 text-gray-900 dark:text-white text-xl font-bold">Project Description</label>
            <span>The description commonly is around less than 200 words.</span>
            <textarea type="description" id="description" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required placeholder="describe your project.."></textarea>
          </div>          
          <button type="submit" @click="getRepo" class="shadow-lg text-white font-semibold rounded-full px-5 py-1 bg-violet-500 hover:bg-violet-600 active:bg-violet-700 focus:outline-none focus:ring focus:ring-violet-300 ">Create</button>
        </form>
        <br/>
      </div>
      <hr/>

      <!-- Export to  Repo -->
      <div v-if="showCreateRepo" class="mt-3 w-full max-w-2/3">
        <form :action="this.techpubStore.getWebRoute('api.post_create_repo').path" method="POST" @submit.prevent="exportrepo($event)">
          <div class="mb-5 mt-5">
            <h2 class="text-center">Create New Repository</h2>
            <label for="project_name" class="block mb-2 text-gray-900 dark:text-white text-xl font-bold">Project Name</label>
            <span>This will be repository name. At the end of repository name will be appended a random string to identify between others.</span>
            <input type="text" id="project_name" name="project_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required placeholder="name your repo..">
            <div class="text-red-600" v-html="techpubStore.error('project_name')"></div>
            <br/>
            <label for="token" class="block mb-2 text-gray-900 dark:text-white text-xl font-bold">Token</label>
            <span>This token is intended for the user who wants to access the repository. Token can be same with other repository.</span>
            <input type="text" id="token" name="token" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required placeholder="name your repo..">
            <div class="text-red-600" v-html="techpubStore.error('token')"></div>
          </div>  
          <button type="submit" class="shadow-lg text-white font-semibold rounded-full px-5 py-1 bg-violet-500 hover:bg-violet-600 active:bg-violet-700 focus:outline-none focus:ring focus:ring-violet-300 ">Send</button>        
        </form>
      </div>
    </div>
  </div>
</template>