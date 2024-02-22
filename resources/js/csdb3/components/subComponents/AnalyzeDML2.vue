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
    sortEntry() {
      let root = $(event.target).parents('.analyze-dml').eq(0);
      let entrycontainers = root.find('.analysis-dml-entry').toArray().sort((a, b) => {
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
  <div v-if="dmlEntryList" class="analyze-dml rounded-lg mb-3">
    <h1 class="underline">
      <span class="text-2xl">Analysis</span>
      <Sort :function="sortEntry.bind(this)" />
    </h1>

    <div v-for="entry in dmlEntryList" class="analysis-dml-entry px-2 overflow-auto">
      <details open>
        <summary>{{ entry.code }}</summary>
        <div>
          <span class="text-pink-400">{{ entry.dmlEntryType || 'entryType' }}, </span>
          <span class="text-green-400">{{ entry.issueType || 'issueType' }}, </span>
          <span class="">{{ entry.responsiblePartnerCompany.enterpriseName }}</span>
        </div>
        Available file:
        <Sort :function="sortUl.bind(this)" />
        <ul v-if="entry.objects.length > 0">
          <li v-for="obj in entry.objects">
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
</template>