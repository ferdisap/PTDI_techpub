<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
import { showDMLContent, addEntry } from './DMLVue.js';
import { CheckboxSelector } from '../../CheckboxSelector';
import jp from 'jsonpath';
import Randomstring from 'randomstring';
import DMLVueCb from './DMLVueCb.js';
import ContextMenu from './ContextMenu.vue';
import Remarks from './Remarks.vue';
import Modal from './Modal.vue';
import { findAncestor } from '../../helper';

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      jp: jp,
      DMLObject: {},
      CbSelector: new CheckboxSelector(),
      rand: Randomstring,

      dmlEntryData: {},
      dmlEntryVueTemplate: '',

      contextMenuId: 'cmDMLVue',
      cbId: 'cbDMLVue',
      CB: {},
      dmlEntryModalId: 'modal_dmlEntry_form',
    }
  },
  components: { Remarks, ContextMenu, Modal },
  props: ['filename'], // tidak mengambil langsung dari $route 
  methods: {
    showDMLContent: showDMLContent,
    addEntry: addEntry, // deprecated
    edit() {
      let etarget = this.ContextMenu.triggerTarget;
      if (etarget = findAncestor(etarget, "*[use-in-modal]")) {
        this.Modal.start(etarget, etarget.getAttribute("use-in-modal"));
      }
    },
    async add(next = true, duplicate = false) {
      // create cloning element
      let etarget = this.ContextMenu.triggerTarget;
      let clonned;
      if (etarget = findAncestor(etarget, "*[cb-room]")) {
        clonned = etarget.cloneNode(true);
        clonned.id = Randomstring.generate({ charset: 'alphabetic' });
        if (!duplicate) {
          clonned.querySelectorAll("*[modal-input-ref]").forEach(input => {
            input.innerHTML = '';
          });
        }
        const id = clonned.getAttribute("use-in-modal");
        const modal = this.Modal.start(clonned, id, { manualUnset: true });

        // wait the replace
        if (!(await this.Modal.replace(await modal.data, id))) {
          clonned = false;
        };
      }
      // append clonned into DOM
      if (clonned) {
        next ? etarget.after(clonned) : etarget.before(clonned);
        this.CB.setCbRoomId(null, clonned, null);
      }
      // unset the Modal collection;
      this.Modal.unsetCollection(2);
    },
    remove() {
      let etarget = this.ContextMenu.triggerTarget;
      if (etarget = findAncestor(etarget, "*[cb-room]")) {
        etarget.remove();
      }
    },
    getAllValues() {
      const values = {};
      const allNamed = this.$el.querySelectorAll('.dmlIdentAndStatusSection *[name]');
      const allEntries = this.$el.querySelectorAll('table tbody tr');
      values.ident = {};
      allNamed.forEach(el => {
        let key = el.name;
        let value = el.value;
        if(key.substring(key.length-2) === '[]'){
          key = key.substring(0,key.length-2)
          value = value.split(/<br\/>|<br>/g); // atau di replace dengan "\n" sesuai backend nya sekarang pakai array (split)
        };
        values.ident[key] = value;
      })
      values.entries = []
      allEntries.forEach((tr, i) => {
        const obj = {};
        tr.querySelectorAll("*[modal-input-ref]").forEach(input => {
          let key = input.getAttribute('modal-input-ref');
          let value = input.innerHTML;
          if(key.substring(key.length-2) === '[]') {
            key = key.substring(0,key.length-2)
            value = value.split(/<br\/>|<br>/g); // atau di replace dengan "\n" sesuai backend nya sekarang pakai array (split)
          };
          obj[key] = value;
        })
        values.entries.push(obj);
      })
      return values;
    },
    async submit() {
      const values = this.getAllValues();
      values.filename = this.$route.params.filename;
      let response = await axios({
        route: {
          name: 'api.dmlupdate',
          data: values,
        },
        useMainLoadingBar: false,
      });
      if (response.statusText === 'OK') {
        // this.emitter.emit('createDMLFromEditorDML', { model: response.data.data });
        // do something here
      }
    },
    newDML(){
      this.$parent.isUpdate = false;
    }
  },
  computed: {
    entries() {
      return {
        components: { Remarks },
        template: this.dmlEntryVueTemplate,
      }
    }
  },
  mounted() {
    window.dml = this;
    // window.th = this;
    // window.jp = jp;
    // window.json = this.$props.json;
    if (this.$props.filename && (this.$props.filename.substring(0, 3) === 'DML')) {
      this.showDMLContent(this.$props.filename);
    }

    // this.entriesString = this.entriesStringFromProps();

    if(this.ContextMenu.register(this.contextMenuId)) this.ContextMenu.toggle(false, this.contextMenuId);

    this.CB = new DMLVueCb(this.cbId)
    this.CB.cbRoomAdditionalAttribute = { "use-in-modal": this.dmlEntryModalId };
  }
}
</script>
<template>
  <form @submit.prevent class="DML relative">
    <!-- dmlIdentAndStatus -->
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
            class="w-20 bg-white border-none p-1" />
        </div>
        <div class="mr-2">
          <div class=" font-bold italic">BREX</div>
          <input name="ident-brexDmRef" :value="DMLObject.BREX" class=" w-96 bg-white border-none p-1" />
        </div>
      </div>
      <div>
        <Remarks :para="DMLObject.remarks" class_label="text-sm font-bold italic" nameAttr="ident-remarks[]" />
      </div>
    </div>
    <!-- dmlContent -->
    <div class="mt-3">
      <table :id="cbId">
        <thead>
          <tr>
            <th v-show="CB.selectionMode"></th>
            <th>No</th>
            <th>Ident</th>
            <th>Type</th>
            <th>Security</th>
            <th>Company</th>
            <th>Answer</th>
            <th>Remarks</th>
          </tr>
        </thead>
        <tbody>
          <component v-if="dmlEntryVueTemplate" :is="entries" />
        </tbody>
      </table>
    </div>

    <div class="text-center mt-3">
      <button @click.stop.prevent="submit()" type="button"
        class="button bg-violet-400 text-white hover:bg-violet-600">Submit</button>
    </div>
    <!-- modal to fill dml Entry -->
    <Modal :id="dmlEntryModalId">
      <template #title>
        <h1 class="text-center font-bold mb-2 text-lg">DML Entry</h1>
      </template>
      <div class="w-full text-center mb-2 relative">
        <!-- entry ident -->
        <div class="relative text-left mb-2">
          <label class="italic font-semibold ml-1">No.</label>
          <input modal-input-name="no" type="number" class="p-2 ml-1 text-sm rounded-lg w-14">
          <label class="italic font-semibold ml-1">Entry Ident:</label>
          <input modal-input-name="entryIdent" dd-input="filename,path" dd-type="csdbs" dd-route="api.get_object_csdbs"
            dd-target="self" placeholder="DMC-..." type="text" class="p-2 w-96 ml-1 inline text-sm rounded-lg border">
          <!-- <input dd-input="first_name,email" dd-type="users" dd-route="api.user_search_model" dd-target="self" placeholder="DMC-..." type="text" class="p-2 w-full inline bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"> -->
        </div>
        <div class="relative text-left mb-2">
          <label class="italic font-semibold ml-1">Entry Type:</label>
          <select modal-input-name="dmlEntryType" class="ml-2 p-2 rounded-lg">
            <option class="text-sm" value="">---</option>
            <option class="text-sm" value="new">new</option>
            <option class="text-sm" value="changed">changed</option>
            <option class="text-sm" value="deleted">deleted</option>
          </select>
          <label class="italic font-semibold ml-1">Issue Type:</label>
          <select modal-input-name="issueType" class="ml-2 p-2 rounded-lg">
            <option class="text-sm" value="">---</option>
            <option class="text-sm" value="new">new</option>
            <option class="text-sm" value="changed">changed</option>
            <option class="text-sm" value="deleted">deleted</option>
            <option class="text-sm" value="revised">revised</option>
            <option class="text-sm" value="status">status</option>
            <option class="text-sm" value="rinstate-status">rinstate-status</option>
            <option class="text-sm" value="rinstate-changed">rinstate-changed</option>
            <option class="text-sm" value="rinstate-revised">rinstate-revised</option>
          </select>
        </div>
        <div class="relative text-left mb-2">
          <label class="italic font-semibold ml-1">Security Classification:</label>
          <select modal-input-name="securityClassification" class="ml-2 p-2 rounded-lg">
            <option class="text-sm" value="">---</option>
            <option class="text-sm" value="01">Unclassified</option>
            <option class="text-sm" value="02">Classified</option>
            <option class="text-sm" value="03">Restricted</option>
            <option class="text-sm" value="04">Secret</option>
            <option class="text-sm" value="05">Top Secret</option>
          </select>
        </div>
        <div class="relative text-left mb-2">
          <label class="italic font-semibold ml-1">Enterprise:</label>
          <!-- <input
          dd-input="enterprise,path" dd-type="csdbs" dd-route="api.get_object_csdbs" dd-target="self,modal_enterpriseCode" -->
          <input modal-input-name="enterpriseName" placeholder="find name" type="text"
            class="ml-1 w-80 p-2 inline bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
          <input modal-input-name="enterpriseCode" id="modal_enterpriseCode" placeholder="find code" type="text"
            class="ml-1 w-32 p-2 inline bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
        <div class="relative text-left mb-2">
          <Remarks modalInputName="remarks[]" class_label="text-sm font-semibold italic" />
        </div>
      </div>
    </Modal>

    <ContextMenu :id="contextMenuId">
      <div @click="CB.push()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Select</div>
      </div>
      <div @click="CB.pushAll(true)" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Select All</div>
      </div>
      <div @click="CB.pushAll(false)" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Deselect All</div>
      </div>
      <hr class="border border-gray-300 block mt-1 my-1 border-solid" />
      <div @click="add()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Add</div>
      </div>
      <div @click="add(true, true)" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Duplicate</div>
      </div>
      <div @click="edit()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Edit</div>
      </div>
      <div @click="remove()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Remove</div>
      </div>
      <hr class="border border-gray-300 block mt-1 my-1 border-solid" />
      <div @click="newDML()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Create New</div>
      </div>
      <div @click.prevent="CB.cancel()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Cancel</div>
      </div>
    </ContextMenu>
  </form>
</template>