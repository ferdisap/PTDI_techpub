<script>
import jp from 'jsonpath';
import { DML, entriesStringFromProps, defaultTemplateEntry, addEntry } from './DMLVue.js';
import Remarks from './Remarks.vue';
import { CheckboxSelector } from '../../CheckboxSelector';
import RCMenu from '../../rightClickMenuComponents/RCMenu.vue';
import Randomstring from 'randomstring';

export default {
  data() {
    return {
      jp: jp,
      DMLObject: DML(this.$props.json),
      CbSelector: new CheckboxSelector(),
      rand: Randomstring,

      entriesData:{},
      entriesString: '',
    }
  },
  components: { Remarks, RCMenu },
  props: ['json'],
  methods: {
    entriesStringFromProps:entriesStringFromProps,
    defaultTemplateEntry:defaultTemplateEntry,
    addEntry:addEntry,
    tesAddEntry(){
      // this.entriesString = window.s;
      // this.entriesString = this.addEntry();
      window.str = this.addEntry();
    }
  },
  computed:{
    entries(){
      return {
        data(){
          return {
            CbSelector: this.$parent.CbSelector
          }
        },
        components: { Remarks },
        template: this.entriesString,
      }
    }
  },
  mounted() {
    window.th = this;
    window.jp = jp;
    window.json = this.$props.json;
    window.entriesStringFromProps = entriesStringFromProps;
    window.defaultTemplateEntry = defaultTemplateEntry;
    this.entriesString = this.entriesStringFromProps();
  }
}
</script>
<template>
  <!-- { '<h1>aaa</h1>' } -->
  <form @submit.prevent class="DML">
    <div class="dmlIdentAndStatusSection mb-3">
      <div class="mb-2 flex">
        <div class="mr-2">
          <div class=" font-bold italic">Code: </div>
          <div>{{ DMLObject.code }}</div>
        </div>
        <div class="mr-2">
          <div class=" font-bold italic">InWork: </div>
          <div>{{ DMLObject.inWork }}</div>
        </div>
        <div class="mr-2">
          <div class=" font-bold italic">Issue: </div>
          <div>{{ DMLObject.issueNumber }}</div>
        </div>
        <div class="mr-2">
          <div class=" font-bold italic">Date: </div>
          <div>{{ DMLObject.issueDate }}</div>
        </div>
      </div>
      <div class="mb-2 flex">
        <div class="mr-2">
          <div class=" font-bold italic">Security</div>
          <input type="number" name="ident-securityClassification" :value="DMLObject.securityClassification"
            class="w-20" />
        </div>
        <div class="mr-2">
          <div class=" font-bold italic">BREX</div>
          <input name="ident-brexDmRef" :value="DMLObject.BREX" class=" w-96" />
        </div>
      </div>
      <div>
        <!-- <textarea v-for="para in DMLObject.remarks">{{ para }}</textarea> -->
        <Remarks :para="DMLObject.remarks" class_label=" font-bold italic"
          placeholder="eg.: add more field if need more paragraph" />
      </div>
    </div>
    <div class="mt-3" @contextmenu.stop.prevent="CbSelector.isShowTriggerPanel = true">
      <table :id="CbSelector.id">
        <thead>
          <tr>
            <th v-show="CbSelector.selectionMode"></th>
            <th>Ident</th>
            <th>Type</th>
            <th>Security</th>
            <th>Company</th>
            <th>Answer</th>
            <th>Remarks</th>
          </tr>
        </thead>
        <tbody>
          <component :is="entries" />
        </tbody>
        <!-- <tbody>
          <tr class="dmlEntry" v-for="(v) in DMLObject.content"
            :id="'tr' + ((entry = DMLObject.getEntryData(v['dmlEntry'])).ident)"
            @click.stop.prevent="CbSelector.selectByEventTarget($event, 'entryIdent', 'cb')"
            @mousemove.stop.prevent="CbSelector.setCbHoveredByEventTarget($event, 'entryIdent', 'cb')"
            :entryIdent="entry.ident">
            <td v-show="CbSelector.selectionMode">
              <input file="true" :id="'cb' + (entry.ident)" type="checkbox" :value="entry.ident">
            </td>
            <td class="dmlEntry-ident ">{{ ((entry = DMLObject.getEntryData(v['dmlEntry'])).ident) }}</td>
            <td>{{ entry.issueType }}</td>
            <td>{{ entry.securityClassification }}</td>
            <td>{{ entry.enterpriseName }} {{ entry.enterpriseCode }}</td>
            <td>-</td>
            <td>
              <Remarks class_label="hidden" :para="entry.remarks.length ? entry.remarks : ['']" />
            </td>
          </tr>
        </tbody> -->
      </table>
      <RCMenu :show="CbSelector.isShowTriggerPanel">
        <div @click="CbSelector.select()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
          <div class="text-sm">Select</div>
        </div>
        <div @click="CbSelector.selectAll(!CbSelector.isSelectAll)"
          class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
          <div class="text-sm">{{ CbSelector.isSelectAll ? 'Deselect' : 'Select' }} All</div>
        </div>
        <div @click="tesAddEntry" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
          <div class="text-sm">Add Entry</div>
        </div>
        <hr class="border border-gray-300 block mt-1 my-1 border-solid" />
        <div @click.prevent="CbSelector.cancel()"
          class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
          <div class="text-sm">Cancel</div>
        </div>
      </RCMenu>
    </div>
</form></template>