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
      raw: undefined
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
  methods:{},
  mounted(){
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
  <div class="w-full text-center mb-3 mt-3">
    <button class="button-nav" @click="show == 'editor' ? show = '' : show = 'editor'">Edit</button>
  </div>
  <br/>
  
  <!-- EDITOR -->
  <Editor v-if="show == 'editor'" :text="raw" :is-create="false"/>


  <component :is="dynamic" v-if="transformed" />
</template>