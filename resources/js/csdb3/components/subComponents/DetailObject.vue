<script>
import axios from 'axios';
import { useTechpubStore } from '../../../techpub/techpubStore';
import Sort from '../../../techpub/components/Sort.vue';
import Editor from './Editor.vue';

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      show: 'stage',
      model: {},

      showEditor: false,

      transformed: undefined,
      raw: undefined,
      srcblob: '', // blob URL
      typeblob: '',

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
        components: { Sort },
        methods: {
          sort() {
            const getCellValue = function (row, index) {
              return $(row).children('td').eq(index).text();
            };
            const comparer = function (index) {
              return function (a, b) {
                let valA = getCellValue(a, index), valB = getCellValue(b, index);
                return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB);
              }
            };
            let table = $(event.target).parents('table').eq(0);
            let th = $(event.target).parents('th').eq(0);
            let rows = table.find('tr:gt(0)').toArray().sort(comparer(th.index()));
            this.asc = !this.asc;
            if (!this.asc) {
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
    },
  },
  components: { Editor },
  methods: {
    async submit(command) {
      let routename;
      if (command == 'commit') {
        routename = 'api.commit_object';
      } else if (command == 'issue') {
        routename = 'api.issue_object';
      } else if (command == 'edit') {
        routename = 'api.edit_object';
      } else if (command == 'delete') {
        if (!(await this.$root.alert({ name: 'beforeDeleteCsdbObject', filename: this.$route.params.filename }))) {
          return;
        }
        routename = 'api.delete_object';
      }
      axios({
        route: {
          name: routename,
          data: { filename: this.$route.params.filename },
        }
      })
    },
    async getObjectModel() {
      let response = await axios({
        route: {
          name: 'api.get_object',
          data: { filename: this.$route.params.filename, output: 'model' },
        }
      });
      if (response.statusText === 'OK') {
        this.model = response.data;
      }
    },
    async getObjectTransformed() {
      if (['DML', 'CSL'].includes(this.$route.params.filename.substr(0, 3))) {
        this.$router.push({ name: 'DetailDML', filename: this.$route.params.filename });
        return;
      }
      else {
        if (this.$route.params.filename.substr(0, 3) != 'ICN') {
          let response = await axios({
            route: {
              name: 'api.transform_csdb',
              data: { filename: this.$route.params.filename, ignoreError: 1 },
            },
          });
          if (response.statusText === 'OK') {
            let text = response.data.file;
            text = text.replace("\\r", "\r");
            text = text.replace("\\n", "\n");
            this.transformed = text;
          }
        } else {
          let response = await axios({
            route: {
              name: 'api.get_object',
              data: { filename: this.$route.params.filename },
            },
          });
          if (response.statusText === 'OK') {
            this.typeblob = response.headers.getContentType();
            if (this.typeblob.includes('xml')) {
              this.raw = await response.data.text();
            }
            this.srcblob = URL.createObjectURL(await response.data);
          }
        }

      }
    },
    async showEdit() {
      if (!this.showEditor) {
        if (!this.raw) {
          let result = await axios({
            route: {
              name: 'api.get_object',
              data: { filename: this.model.filename }
            }
          })
          if (result.statusText === 'OK') {
            this.raw = result.data;
          }
        }
        this.showEditor = true;
      } else {
        this.showEditor = false;
      }

    },
    // async download() {
    //   if (!this.srcblob) {
    //     let response = await axios({
    //       route: {
    //         name: 'api.get_object',
    //         data: { filename: this.$route.params.filename },
    //       },
    //       responseType: 'blob'
    //     });
    //     if (response.statusText === 'OK') {
    //       this.typeblob = response.headers.getContentType();
    //       if (this.typeblob.includes('xml')) {
    //         this.raw = await response.data.text();
    //       }
    //       this.srcblob = URL.createObjectURL(await response.data);
    //     }
    //   }
    //   let a = $('<a/>')
    //   a.attr('download', this.$route.params.filename);
    //   a.attr('href', this.srcblob);
    //   a[0].click();
    // },
    // async download_all() {
    //   axios({
    //     route: {
    //       name: 'api.get_export_file',
    //       data: { filename: this.$route.params.filename },
    //     },
    //   });
    // }
  },
  created() {
    this.getObjectModel();
  },
  mounted() {
    window.det = this;
    this.emitter.on('DetailObject', this.getObjectTransformed);
    this.emitter.emit('DetailObject');
  },
}
</script>
<template>
  <h1 class="text-center">Detail Object</h1>
  <div class="text-center">Editable: {{ model.editable ? 'yes' : 'no' }}</div>
  <!-- jika inWork object tidak '00' maka bisa commit, edit, issue -->
  <div class="w-full text-center mb-3 mt-3" v-if="$route.params.filename && !$route.params.filename.match(/_\d{3}-00/g)">
    <button class="button-nav" @click="submit('commit')">Commit</button>
    <button class="button-nav" @click="showEdit" v-if="model.editable">Edit</button>
    <button class="button-nav" @click="submit('issue')">Issue</button>
    <button class="button-nav" @click="submit('delete')">Delete</button>
  </div>
  <div class="w-full text-center mb-3 mt-3" v-else>
    <button class="button-nav" @click="submit('edit')">Open Edit</button>
    <button class="button-nav" @click="submit('delete')">Delete</button>
  </div>
  <br />

  <!-- EDITOR -->
  <!-- <Editor v-if="show == 'editor'" :text="raw" :is-create="false"/> -->
  <Editor v-if="showEditor" :text="raw" />

  <!-- <a class="underline text-blue-600" :href="srcblob" :download="$route.params.filename">download here..</a> -->
  <a class="underline text-blue-600" @click="$root.download" href="#">download here..</a>
  <br/>
  <a class="underline text-blue-600" @click="$root.download_all" href="#">download all..</a>
  <!-- <embed :src="srcblob" v-if="srcblob"/> -->
  <component :is="dynamic" v-if="transformed" />
  <embed v-else :type="typeblob" :src="srcblob" class="w-full" />
</template>