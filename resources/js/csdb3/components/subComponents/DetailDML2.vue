<!-- ini nanti tidak dipakai lagi. akan diganti oleh DetailDML2.vue -->

<script>
import axios from 'axios';
import { useTechpubStore } from '../../../techpub/techpubStore';
import Sort from '../../../techpub/components/Sort.vue';
import AddEntryDML from '../subComponents/AddEntryDML.vue';
import PushToStage from './PushToStage.vue';
import DmlEntryForm from './DmlEntryForm.vue';

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      showDML: undefined,

    }
  },
  components: { AddEntryDML, Sort, PushToStage },
  props: {
    transformed: {
      type: String,
      required: true,
    },
    model: Object
  },
  computed: {
    dynamic() {
      return {
        template: this.$props.transformed,
        data() {
          return {
            store: useTechpubStore(),
          }
        },
        components: { Sort, DmlEntryForm },
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
    }
  },
  methods: {
    async submit(command) {
      let routename;
      if (command == 'commit') {
        routename = 'api.commit_dml';
      } else if (command == 'issue') {
        routename = 'api.issue_dml';
      } else if (command == 'edit') {
        routename = 'api.edit_dml';
      } else if (command == 'delete') {
        if (!(await this.$root.alert({ name: 'beforeDeleteDML', filename: this.$route.params.filename }))) {
          return;
        }
        routename = 'api.delete_object';
      } else if (command == 'restore') {
        routename = 'api.restore_object';
      }

      axios({
        route: {
          name: routename,
          data: { filename: this.$route.params.filename },
        }
      })
    },
    async update() {
      let form = event.target;
      if (!(await this.$root.alert({ name: 'beforeUpdateDML', filename: this.$route.params.filename }))) {
        return false;
      }
      const formData = new FormData(form);
      axios({
        route: {
          name: 'api.dmlupdate',
          data: formData,
        }
      })
    },
  },
}
</script>
<style>
#dml th {
  text-wrap: nowrap;
}
</style>
<template>
  <div v-if="model" class="detail-dml rounded-lg shadow-lg mb-3">
    <h1 class="text-center">Detail DML</h1>
    <div class="text-center">Editable: {{ model.editable ? 'yes' : 'no' }}</div>

    <!-- jika DMRL -->
    <div class="w-full text-center mb-3 mt-3"
      v-if="model.filename.substr(0, 3) === 'DML' && ($route.params.filename && !$route.params.filename.match(/_\d{3}-00/g))">
      <button class="button-nav" v-if="model.editable" @click="submit('issue')">Issue</button>
      <button class="button-nav" @click="submit('commit')">Commit</button>
      <button class="button-nav" @click="submit('delete')">Delete</button>
    </div>
    <div class="w-full text-center mb-3 mt-3" v-else-if="model.filename.substr(0, 3) === 'DML'">
      <button class="button-nav" v-if="!model.editable" @click="submit('edit')">Open Edit</button>
      <button class="button-nav" v-if="!model.editable"
        @click="showDML = (showDML == 'pushToStage' ? '' : 'pushToStage')">Push to Stage</button>
      <button class="button-nav" @click="submit('delete')">Delete</button>
    </div>

    <a class="underline text-blue-600" @click="$root.download" href="#">download here..</a>
    <br />
    <a class="underline text-blue-600" @click="$root.download_all" href="#">download all..</a>

    <PushToStage v-if="showDML == 'pushToStage'" />
    <br />

    <form id="dml" @submit.prevent="update">
      <input type="hidden" name="filename" :value="$route.params.filename" />

      <component :is="dynamic" v-if="transformed" />

      <div class="w-full text-center mb-3 mt-3" v-if="model.editable">
        <button class="button-violet" type="submit">Update</button>
      </div>
    </form>

  </div>
</template>