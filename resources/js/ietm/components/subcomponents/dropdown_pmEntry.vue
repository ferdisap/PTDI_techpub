<script>
import { useIetmStore } from '../../ietmStore';
import Sidenav from '../sidenav.vue';
import Dropdown_pmEntry from './dropdown_pmEntry.vue';
import { reactive } from 'vue';

export default {
  data() {
    return {
      ietmStore: useIetmStore(),
      pmType: '',
      data: reactive({}),

    }
  },
  // props: { pmEntry: Array, filename: String, pt: String, title: String },
  props: { pmEntry: Array, filename: String, pt: String, title: String },
  methods: {
    async pmEntryHandler(filename) {
      if (!this.data[filename]) {
        let pmEntry = await this.ietmStore.pmEntryHandler(filename);
        this.data[filename] = pmEntry;
      }
      this.data[filename + 'show'] = !this.data[filename + 'show'];
    },
    async dmc_detail(filename) {
      this.$router.push({ name: 'Detail', params: { repoName: this.$route.params.repoName, filename: filename } });
    },
    getPMCTitle(filename) {
      const objects = this.ietmStore.listPMC;
      for (const [key, object] of Object.entries(objects)) {
        if (object.filename == filename) {
          // console.log(object.title);
          return object.title
        }
      }
    },
    getDMCTitle(filename) {
      const objects = this.ietmStore.listDMC;
      for (const [key, object] of Object.entries(objects)) {
        if (object.filename == filename) {
          // console.log(object.title);
          return object.title
        }
      }
    }
  },
}
</script>

<template>
  <div v-if="filename" class="text-lg mt-2">
    <button class="text-left hover:bg-sky-300" @click="pmEntryHandler(filename)"> 
      <span class="mr-2 font-bold">{{ title }} </span>
      <br/>
      <span class="">{{ filename }}</span>
    </button>
    <div v-show="data[filename + 'show']">
      <Dropdown_pmEntry :pm-entry="data[filename]" v-if="data[filename]" />
    </div>
  </div>

  <div v-if="pmEntry" v-for="entry in pmEntry" class="mt-2">
    <div v-for="content in entry.content" class="mt-2"
      :style="`margin-left:${entry.level * 10}px; text-align:left`">
      <div v-if="(typeof content == 'string')">

        <!-- jika PMC -->
        <div v-if="content.split('-')[0] == 'PMC'">
          <button @click="pmEntryHandler(content)" class="text-left hover:bg-sky-300">
            <span class="mr-2 font-bold" v-html="getPMCTitle(content)"></span>
            <br/>
            <span class="">{{ content }}</span>
          </button>
          <div v-show="data[content + 'show']">
            <Dropdown_pmEntry :pm-entry="data[content]" />
          </div>
        </div>

        <div v-else>
          <button @click="dmc_detail(content)" class="text-left hover:bg-sky-300"> 
            <span class="mr-2 font-bold" v-html="getDMCTitle(content)"></span>
            <br/>
            <span class="">{{ content }}</span>
          </button>
        </div>
      </div>
      <div v-else>
        <Dropdown_pmEntry :pm-entry="[content]" />
      </div>
    </div>
  </div>
</template>