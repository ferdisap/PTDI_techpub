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
      transformed: undefined,
      showDML: undefined,

      model: undefined,
    }
  },
  props: ['isInEditing'],
  components: { AddEntryDML, Sort, PushToStage },
  computed: {
    dynamic() {
      return {
        template: this.transformed,
        data() {
          return {
            store: useTechpubStore(),
            dmlEntryOuterHTML: '',
          }
        },
        components: { Sort, DmlEntryForm},
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
          window.th = this;
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
      } else if (command == 'delete'){
        if(!(await this.$root.alert({name: 'beforeDeleteDML', filename:this.$route.params.filename}))){
          return;
        }
        routename = 'api.delete_object';
      } else if (command == 'restore'){
        routename = 'api.restore_object';
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
    async update() {
      let form = event.target;
      if(!(await this.alert({message: 'are you sure want to <b>UPDATE</b> this DML?'}).result)){
        return false;
      }
      const formData = new FormData(form);
      this.$root.showMessages = false;
      const route = this.techpubStore.getWebRoute('api.dmlupdate', formData);
      await axios({
        url: route.url,
        method: route.method[0],
        data: formData,
      })
        .then(response => this.$root.success(response))
        .catch(error => this.$root.error(error))
    }
  },
  async created() {
    const route = this.techpubStore.getWebRoute('api.get_object', { filename: this.$route.params.filename, output: 'model' });
    await axios({
      url: route.url,
      method: route.method[0],
      data: route.params,
    })
      .then(response => this.model = response.data)
      .catch(error => this.$root.error(error));
    
    if(this.model && this.model.remarks.crud == 'deleted'){
      setTimeout(() => $('#dml, #dml input, #dml textarea').each((i,e) => e.style.backgroundColor = 'red'),1000);
    }
  },
  mounted() {
    window.th = this;
    const route = this.techpubStore.getWebRoute('api.transform_csdb', { filename: this.$route.params.filename });
    axios({
      url: route.url,
      method: route.method[0],
      data: route.params,
    })
      .then(response => {
        let text = response.data.file;
        text = text.replace("\\n", "\n");
        this.transformed = text;
      })
      .catch(error => this.$root.error(error));
  },
}
</script>
<style>
#dml th {
  text-wrap: nowrap;
}
</style>
<template>
  <div v-if="model">
    <h1 class="text-center">Detail DML</h1>
    <div class="text-center">Editable: {{ model.editable ? 'yes' : 'no' }}</div>

    <!-- jika DML -->
    <div class="w-full text-center mb-3 mt-3" v-if="model.filename.substr(0, 3) == 'DML' && ($route.params.filename && !$route.params.filename.match(/_\d{3}-00/g))">
      <button class="button-nav" v-if="model.editable" @click="submit('issue')">Issue</button>
      <button class="button-nav" @click="submit('commit')">Commit</button>
      <button class="button-nav" @click="submit('delete')">Delete</button>
      <!-- <button class="button-nav" v-if="model.remarks.crud != 'deleted'" @click="submit('delete')">Delete</button> -->
      <!-- <button class="button-nav" v-else @click="submit('restore')">Restore</button> -->
    </div>
    <div class="w-full text-center mb-3 mt-3" v-else-if="model.filename.substr(0, 3) == 'DML'">
      <button class="button-nav" v-if="!model.editable" @click="submit('edit')">Open Edit</button>
      <button class="button-nav" v-if="!model.editable" @click="showDML = (showDML == 'pushToStage' ? '' : 'pushToStage')">Push to Stage</button>
      <button class="button-nav" @click="submit('delete')">Delete</button>
    </div>

    <!-- jika CSL -->
    <!-- <div class="w-full text-center mb-3 mt-3" v-if="model.filename.substr(0,3) == 'CSL'">
      <button class="button-nav" v-if="model.editable" @click="submit('issue')">Issue</button>
      <button class="button-nav" @click="showDML = (showDML == 'pushToStage' ? '' : 'pushToStage')">Push to Stage</button>
    </div> -->


    <!-- if is in COMMITING area (bukan CSL) -->
    <!-- <div class="w-full text-center mb-3 mt-3" v-if="!$props.isInEditing && !model.editable && model.filename.substr(0,3) != 'CSL'">
      <button :class="[showDML == 'addEntry' ? 'border-b-black border-b-4' : '', 'button-nav']"
        @click="showDML != 'addEntry' ? (showDML = 'addEntry') : (showDML = '')">Add Entry</button>
      <button class="button-nav" @click="submit('commit')">Commit</button>
      <button class="button-nav" @click="submit('edit')">Open to Edit</button>
    </div> -->

    <!-- if is in EDITTING area (bukan CSL) -->
    <!-- <div class="w-full text-center mb-3 mt-3" v-if="$props.isInEditing && !model.editable && model.filename.substr(0,3) != 'CSL'">
      <button class="button-nav" v-if="model.editable" @click="submit('issue')">Issue</button>
      <button class="button-nav" @click="showDML = (showDML == 'pushToStage' ? '' : 'pushToStage')">Push to Stage</button>
    </div> -->

    <!-- <AddEntryDML v-if="(!model.editable) && (showDML == 'addEntry')" :dmlfilename="$route.params.filename" /> -->
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