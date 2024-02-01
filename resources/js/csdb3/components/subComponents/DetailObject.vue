<script>
import axios from 'axios';
import { useTechpubStore } from '../../../techpub/techpubStore';
import Sort from '../../../techpub/components/Sort.vue';
import Editor from './Editor.vue';

export default {
  data(){
    return{
      techpubStore: useTechpubStore(),
      transformed: undefined,
      show: 'stage',
      raw: undefined,
      model: {},
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
        mounted() {
          $('.map').maphilight();
          $('area').each((k, area) => {
            $(area).click((e) => {
              e.preventDefault();
              let data = $(e.target).mouseout().data('maphilight') || {};
              data.alwaysOn = !(data.alwaysOn);
              $(e.target).data('maphilight', data).trigger('alwaysOn.maphilight');
            })
          });
        },
      }
    }
  },
  components:{Editor},
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
        .then(response => this.$root.success(response))
        .catch(error => this.$root.error(error));
    },
  },
  created() {
    const route = this.techpubStore.getWebRoute('api.get_object', { filename: this.$route.params.filename, output:'model'});
    axios({
      url: route.url,
      method: route.method[0],
      data: route.params,
    })
      .then(response => this.model = response.data)
      .catch(error => this.$root.error(error));
  },
  mounted(){
    if(['DML', 'CSL'].includes(this.$route.params.filename.substr(0,3))){
      return;
    }
    let route = this.techpubStore.getWebRoute('api.transform_csdb',{filename: this.$route.params.filename, ignoreError:1});
    axios({
      url: route.url,
      method: route.method[0],
      data: route.params,
    })
    .then(response => {
      this.transformed = response.data.file;
      if(response.data.messages){
        this.$root.success(response,false);
      }
    })
    .catch(error => this.$root.error(error));

    route = this.techpubStore.getWebRoute('api.get_object',{filename: this.$route.params.filename});
    axios({
      url: route.url,
      method: route.method[0],
      data: route.params,
    })
    .then(response => {
      this.raw = response.data;
    })
    .catch(error => this.$root.error(error));
  },
}
</script>
<template>
  <h1 class="text-center">Detail Object</h1>
  <div class="text-center">Editable: {{ model.editable ? 'yes' : 'no' }}</div>
  <!-- jika inWork object tidak '00' maka bisa commit, edit, issue -->
  <div class="w-full text-center mb-3 mt-3" v-if="$route.params.filename && !$route.params.filename.match(/_\d{3}-00/g)">
    <button class="button-nav" @click="submit('commit')">Commit</button>
    <button class="button-nav" @click="show == 'editor' ? show = '' : show = 'editor'">Edit</button>
    <button class="button-nav" @click="submit('issue')">Issue</button>
  </div>
  <div class="w-full text-center mb-3 mt-3" v-else>
    <button class="button-nav" @click="submit('edit')">Open Edit</button>
  </div>
  <br/>
  
  <!-- EDITOR -->
  <Editor v-if="show == 'editor'" :text="raw" :is-create="false"/>

  <component :is="dynamic" v-if="transformed" />
</template>