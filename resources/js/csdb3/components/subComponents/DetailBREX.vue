<script>
import axios from 'axios';
import { useTechpubStore } from '../../../techpub/techpubStore';
import Sort from '../../../techpub/components/Sort.vue';

export default {
  data(){
    return{
      techpubStore: useTechpubStore(),
      transformed: undefined,
      showEditor: false,
    }
  },
  computed: {
    dynamic() {
      return {
        template: this.transformed,
        data() {
          return {
            store: useTechpubStore(),
          }
        },
        components:{Sort},
        methods: {
          sort(){
            const getCellValue = function(row, index){
              return $(row).children('td').eq(index).text();
            };
            const comparer = function(index){
              return function(a,b){
                let valA = getCellValue(a, index), valB = getCellValue(b, index);
                return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB);
              }
            };
            let table = $(event.target).parents('table').eq(0);
            let th = $(event.target).parents('th').eq(0);
            let rows = table.find('tr:gt(0)').toArray().sort(comparer(th.index()));
            this.asc = !this.asc;
            if(!this.asc){
              rows = rows.reverse();
            }
            for (let i = 0; i < rows.length; i++) {
              table.append(rows[i]);
            }
          },
        },
      }
    }
  },
  methods:{
    submit(commitOrIssue) {
      let routename;
      if (commitOrIssue == 'commit') {
        routename = 'api.commit_object';
      } else if (commitOrIssue == 'issue') {
        routename = 'api.issue_object';
      } else if (commitOrIssue == 'edit') {
        routename = 'api.edit_object';
      }

      this.$root.showMessages = false;
      const route = this.techpubStore.getWebRoute(routename, { filename: this.$route.params.filename });
      axios({
        url: route.url,
        method: route.method[0],
        data: route.params,
      })
        .then(response => {
          this.$root.success(response);
        })
        .catch(error => this.$root.error(error));
    },
  },
  mounted(){
    const route = this.techpubStore.getWebRoute('api.transform_csdb',{filename: this.$route.params.filename});
    axios({
      url: route.url,
      method: route.method[0],
      data: route.params,
    })
    .then(response => {
      this.transformed = response.data.file;
    })
    .catch(error => this.$root.error(error));
  },
}
</script>
<template>
  <!-- ini nanti tidak dipakai karena diganti DetailObject -->
  <div class="detailBREX">
    <h1 class="text-center">Detail BREX</h1>
    <button class="button-nav" @click="submit('commit')">Commit</button>
    <button class="button-nav" @click="showEditor = !showEditor">Edit</button>
    <br/>
    <Editor/>
    <component :is="dynamic" v-if="transformed" />
  </div>
</template>