<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
import Sort from '../../../techpub/components/Sort.vue';
export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      dmlEntryList: undefined,
    }
  },
  props: ['filename'],
  components: { Sort },
  computed: {
    async getDmlEntries() {
      let response = await axios({
        route: {
          name: 'api.get_dmlentry',
          data: { filename: this.$props.filename }
        }
      });
      if (response.statusText === 'OK') {
        this.dmlEntryList = response.data;
      }
    }
  },
  methods: {
    sortUl() {
      let ul = $(event.target).parents('details').eq(0).children('ul');
      let lis = ul.find('li').toArray().sort((a, b) => {
        let valA, valB;
        valA = $(a).children('*:first-child').text();
        valB = $(b).children('*:first-child').text();
        return valA.toString().localeCompare(valB);
      })
      this.asc = !this.asc;
      if (!this.asc) {
        lis = lis.reverse();
      }
      for (let i = 0; i < lis.length; i++) {
        ul.append(lis[i]);
      }
    },
    sortEntry(){
      let root = $(event.target).parents('.analyze-dml').eq(0);
      let entrycontainers = root.find('.analysis-dml-entry').toArray().sort((a,b) => {
        let valA, valB;
        valA = $(a).find('summary').text();
        valB = $(b).find('summary').text();
        return valA.toString().localeCompare(valB);
      });
      this.asc = !this.asc;
      if (!this.asc) {
        entrycontainers = entrycontainers.reverse();
      }
      for (let i = 0; i < entrycontainers.length; i++) {
        root.append(entrycontainers[i]);
      }
    }

  },
}
</script>
<style>
.analyze-dml ul {
  list-style: revert;
  margin: revert;
  padding: revert
}
</style>
<template>
  <div v-show="false">{{ getDmlEntries }}</div>
  <div class="ml-3 w-1/3">
    <div v-if="dmlEntryList" class="analyze-dml rounded-lg shadow-lg">
      <div class="bg-blue-500 flex py-3 px-2 text-white mb-3 border rounded-t-xl text-center">
        <div style="width:90%">
          <span class="text-2xl">Detail</span>
          &nbsp;
          <Sort :function="sortEntry.bind(this)" />
          <br />
          <span class="text-md">{{ $props.filename }}</span>
        </div>
        <div style="width:10%">
          <button class="float-right mr-3 hover:scale-125" @click="$parent.filenameAnalysis = undefined">X</button>
        </div>
      </div>

      <div v-for="entry in dmlEntryList" class="analysis-dml-entry px-2 overflow-auto">
        <details open>
          <summary>{{ entry.code }}</summary>
          <div>
            <span class="text-pink-400">{{ entry.dmlEntryType || 'entryType' }}, </span>
            <span class="text-green-400">{{ entry.issueType || 'issueType' }}, </span>
            <span class="">{{ entry.responsiblePartnerCompany.enterpriseName }}</span>
          </div>
          <!-- Available file: <Sort :function="sort.bind(this,'fooasa')"/> -->
          Available file:
          <Sort :function="sortUl.bind(this)" />
          <ul v-if="entry.objects.length > 0">
            <li v-for="obj in entry.objects">
              <!-- <a @click.prevent="detailObject(obj.filename)" class="material-icons text-blue-600 has-tooltip-arrow" data-tooltip="Detail" :href="techpubStore.getWebRoute('', { filename: obj.filename }, Object.assign({}, $router.getRoutes().find((route) => route.name == 'DetailObject'))).path">details</a> -->
              <a class="font-bold" target="_blank"
                :href="techpubStore.getWebRoute('', { filename: obj.filename }, Object.assign({}, $router.getRoutes().find((route) => route.name == 'DetailObject'))).path">
                {{ obj.filename }}
              </a>
              <br />
              <span>Stage: </span><span class="italic">{{ obj.remarks.stage }}</span>
              <br />
              <span class="has-tooltip-arrow" :data-tooltip="obj.initiator.email">Initiator: {{ obj.initiator.name }}
              </span>
              <br />
              <span>Created: {{ techpubStore.date(obj.created_at) }}</span>
            </li>
          </ul>
          <span v-else>none</span>
        </details>
      </div>
    </div>
    <!-- <table>
      <thead>
        <tr>
          <th>Entry</th>
          <th>Type</th>
          <th>Issue Type</th>
          <th>Responsible Company</th>
        </tr>
      </thead>
      <tbody>
        <tr :id="entry.code" v-for="entry in dmlEntryList">
          <td><a href="#" @click="availableFile = entry.code">{{ entry.code }}</a></td>
          <td>{{ entry.dmlEntryType || '-' }}</td>
          <td>{{ entry.issueType || '-' }}</td>
          <td>{{ entry.responsiblePartnerCompany.enterpriseName || entry.responsiblePartnerCompany.enterpriseCode }} </td>
        </tr>
      </tbody>
    </table>
  
    Available File
    <div v-if="availableFile" class="mt-5">
      <div class="mb-3" 
      v-if="Object.values(dmlEntryList).find(o => o.code == availableFile)['objects'].length"
      v-for="obj in Object.values(dmlEntryList).find(o => o.code == availableFile)['objects']">
        <div v-if="!obj.length">
          Filename: <a :href="techpubStore.getWebRoute('',{filename: obj.filename}, $router.getRoutes().find((route) => route.name == 'DetailObject')).path">{{ obj.filename }}</a> <br/>
          Stage: {{ obj.remarks.stage }} <br/>
          Initiator: {{ obj.initiator.name }}
        </div>
      </div>
      <div class="mb-3 bg-red-500" v-else>
        The {{ availableFile }} has no object model available in database.
      </div>
    </div> -->
  </div>


  <!-- <div v-show="false">{{ getDmlEntries }}</div>
  <div v-if="dmlEntryList" class="rounded-lg shadow-lg">
    <div class="bg-blue-500 py-3 px-2 text-white mb-3 border rounded-t-xl text-center">
      <span class="text-2xl">Detail</span>
      <button class="float-right mr-3 hover:scale-125" @click="delete this">X</button>
    </div>
    <table>
      <thead>
        <tr>
          <th>Entry</th>
          <th>Type</th>
          <th>Issue Type</th>
          <th>Responsible Company</th>
        </tr>
      </thead>
      <tbody>
        <tr :id="entry.code" v-for="entry in dmlEntryList">
          <td><a href="#" @click="availableFile = entry.code">{{ entry.code }}</a></td>
          <td>{{ entry.dmlEntryType || '-' }}</td>
          <td>{{ entry.issueType || '-' }}</td>
          <td>{{ entry.responsiblePartnerCompany.enterpriseName || entry.responsiblePartnerCompany.enterpriseCode }} </td>
        </tr>
      </tbody>
    </table>
  
    Available File
    <div v-if="availableFile" class="mt-5">
      <div class="mb-3" 
      v-if="Object.values(dmlEntryList).find(o => o.code == availableFile)['objects'].length"
      v-for="obj in Object.values(dmlEntryList).find(o => o.code == availableFile)['objects']">
        <div v-if="!obj.length">
          Filename: <a :href="techpubStore.getWebRoute('',{filename: obj.filename}, $router.getRoutes().find((route) => route.name == 'DetailObject')).path">{{ obj.filename }}</a> <br/>
          Stage: {{ obj.remarks.stage }} <br/>
          Initiator: {{ obj.initiator.name }}
        </div>
      </div>
      <div class="mb-3 bg-red-500" v-else>
        The {{ availableFile }} has no object model available in database.
      </div>
    </div>
  </div> -->
</template>