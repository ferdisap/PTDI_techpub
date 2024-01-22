<script>
import axios from 'axios';
import { useTechpubStore } from '../../../techpub/techpubStore';
import Sort from '../../../techpub/components/Sort.vue';
import AddEntryDML from '../subComponents/AddEntryDML.vue';
import PushToStage from './PushToStage.vue';

// ini dibuat di window karena belum tau caranya ketika clonne node, vue component element di clone juga
function delete_dmlEntry_row() {
  let clickked_addButton = $(event.target).parents('.add_dmlEntry');
  clickked_addButton.prev().remove();
  clickked_addButton.remove()
}
window.delete_dmlEntry_row = delete_dmlEntry_row;
function add_dmlEntry_row() {
  let clonned_dmlEntry = $(event.target).parents('.add_dmlEntry').prev().clone();
  clonned_dmlEntry.find('*[name]').each((i, e) => $(e).val(''));
  let clonned_addButton = $(event.target).parents('.add_dmlEntry').clone();

  let clickked_addButton = $(event.target).parents('.add_dmlEntry');
  clonned_dmlEntry.insertAfter(clickked_addButton);
  clonned_addButton.insertAfter(clonned_dmlEntry);
}
window.add_dmlEntry_row = add_dmlEntry_row;
function add_dmlEntry_row_first(){
  let dmlEntry = `<tr class="dmlEntry"><td class="dmlEntry-ident"><textarea name="entryIdent" class="w-full"></textarea></td><td><input class="dmlEntry-dmlType w-2/5" name="dmlType"> | <input class="dmlEntry-issueType w-2/5" name="issueType"></td><td><input class="dmlEntry-securityClassification w-full" name="securityClassification"></td><td class="responsibleCompany"><input class="dmlEntry-enterpriseName w-2/5" name="enterpriseName"> | <input class="dmlEntry-enterpriseCode w-2/5" name="enterpriseCode"></td><td>-</td><td>-</td></tr>`
  let button = `<tr class="add_dmlEntry"><td><button class="material-icons" type="button" onclick="add_dmlEntry_row()">add</button></td><td><button class="material-icons" type="button" onclick="delete_dmlEntry_row()">delete</button></td></tr>`;
  let clickked_addButton = $(event.target).parents('.add_dmlEntry');
  dmlEntry = $(dmlEntry).insertAfter(clickked_addButton);
  $(button).insertAfter(dmlEntry);
}
window.add_dmlEntry_row_first = add_dmlEntry_row_first;

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      transformed: undefined,
      showDML: undefined,
    }
  },
  props: ['isInEditing', 'isCSL'],
  components: { AddEntryDML, Sort, PushToStage },
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
    }
  },
  methods: {
    submit(commitOrIssue) {
      let routename;
      if (commitOrIssue == 'commit') {
        routename = 'api.commit_dml';
      } else if (commitOrIssue == 'issue') {
        routename = 'api.issue_dml';
      } else if (commitOrIssue == 'edit') {
        routename = 'api.edit_dml';
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
    update(){
      const formData = new FormData(event.target);
      this.$root.showMessages = false;
      const route = this.techpubStore.getWebRoute('api.dmlcontentupdate', formData);
      axios({
        url: route.url,
        method: route.method[0],
        data: formData,
      })
      .then(response => this.$root.success(response))
      .catch(error => this.$root.error(error))
    }
  },
  mounted() {
    const route = this.techpubStore.getWebRoute('api.transform_csdb', { filename: this.$route.params.filename });
    axios({
      url: route.url,
      method: route.method[0],
      data: route.params,
    })
      .then(response => {
        window.res = response;
        this.transformed = response.data.file;
      })
      .catch(error => this.$root.error(error));
  },
}
</script>
<template>
  <h1 class="text-center">Detail DML isInEditing: {{ $props.isInEditing }}</h1>

  <!-- if is in COMMITING area (bukan CSL) -->
  <div class="w-full text-center mb-3 mt-3" v-if="!$props.isInEditing && !isCSL">
    <button :class="[showDML == 'addEntry' ? 'border-b-black border-b-4' : '', 'button-nav']"
      @click="showDML != 'addEntry' ? (showDML = 'addEntry') : (showDML = '')">Add Entry</button>
    <button class="button-nav" @click="submit('commit')">Commit</button>
    <button class="button-nav" @click="submit('issue')">Issue</button>
    <button class="button-nav" @click="submit('edit')">Open to Edit</button>
  </div>
  <!-- if is in EDITTING area (bukan CSL) -->
  <div class="w-full text-center mb-3 mt-3" v-if="$props.isInEditing && !isCSL">
    <button class="button-nav" @click="showDML = (showDML == 'pushToStage' ? '' : 'pushToStage')">Push to Stage</button>
  </div>

  <AddEntryDML v-if="(!$props.isInEditing) && (showDML == 'addEntry')" :dmlfilename="$route.params.filename" />
  <PushToStage v-if="showDML == 'pushToStage'" />

  <br />
  <form id="dml" @submit.prevent="update">
    <input type="hidden" name="filename" :value="$route.params.filename"/>
    <component :is="dynamic" v-if="transformed" />
    <div class="w-full text-center mb-3 mt-3" v-if="isCSL">
      <button class="button-violet" type="submit">Update</button>
    </div>
  </form>
</template>