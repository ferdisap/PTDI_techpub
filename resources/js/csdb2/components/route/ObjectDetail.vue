<script>
import axios from 'axios';
import { useTechpubStore } from '../../../techpub/techpubStore';
import Sort from '../../../techpub/components/Sort.vue';
import Search from '../../../techpub/components/Search.vue';

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      viewer: 'ietm',
      transformedTemplate: undefined,
    }
  },
  components:{Search},
  computed: {
    dynamic() {
      return {
        template: this.transformedTemplate,
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
  props: ['projectName', 'filename'],
  methods: {
    async submit(evt) {
      this.techpubStore.showLoadingBar = true;
      const formData = new FormData(evt.target);
      formData.append('filename', this.techpubStore.currentDetailObject.model.filename);
      formData.append('project_name', this.techpubStore.currentDetailObject.projectName);
      formData.append(evt.submitter.name, evt.submitter.value);
      let api = await axios({
        url: evt.target.action,
        method: evt.target.method,
        data: formData,
      })
        .then(response => this.$root.success(response))
        .catch(error => this.$root.error(error));
      this.techpubStore.showLoadingBar = false;
    },
    exportpdf(evt) {
      this.techpubStore.showLoadingBar = true;
      const formData = new FormData(evt.target);
      const url = new URL(evt.target.action);
      url.search = new URLSearchParams(formData);

      let embed = $('#pdf-viewer #embed-pdf');
      embed.attr('src', url.toString())
      this.techpubStore.showLoadingBar = false;
    },
    async transform() {
      const transformed = await this.techpubStore.getCurrentDetailObject();
      const mime = transformed[0];
      if (mime.includes('text')) {
        this.transformedTemplate = transformed[1];
      } else {
        let src = transformed[1];
        this.transformedTemplate = `<embed type="${mime}" src="${src}" style="width:inherit"/>`
      }
    },
    
  },
  async mounted() {
    await this.techpubStore.setProject(); // di page ProjectDetail sudah di set. Jika page ini di refresh, dia akan set project, 
    let object = this.techpubStore.object(this.$props.projectName, this.$props.filename);
    await this.techpubStore.setCurrentObject_model(object, this.$props.projectName, this.$props.filename);
    await this.techpubStore.setCurrentDetailObject_blob();
    this.transform();
    this.techpubStore.showLoadingBar = false;
  },
  async beforeUpdate() {
    if(this.techpubStore.currentDetailObject.model.filename != this.$props.filename){
      let object = this.techpubStore.object(this.$props.projectName, this.$props.filename);
      await this.techpubStore.setCurrentObject_model(object, this.$props.projectName, this.$props.filename);
      await this.techpubStore.setCurrentDetailObject_blob();
      this.transform();
      this.techpubStore.showLoadingBar = false;
    }
  },
}
</script>

<style>
.dmRef{
  white-space: nowrap;
}
.responsibleCompany{
  position: relative;
}
.responsibleCompany .enterpriseCode {
  position: absolute;
  top: 0px;
  display: none;
  /**
  */
}
.responsibleCompany:hover .enterpriseCode{
  display: block;
  position: absolute;
  width:100%;
  text-align: center;
  left: 50px;
  top: -1.25rem;
  background-color: black;
  color: white;
  border-radius: 0.5rem;
}
</style>

<template>
  <h1 class="text-center">Detail</h1>
  <h4 class="text-center"> {{ $props.filename }} </h4>
  <div class="text-center flex items-start justify-center">
    <div class="text-left border-r-2 px-2 border-slate-950">
      Status: {{ techpubStore.currentDetailObject.model ? techpubStore.currentDetailObject.model.status : '' }}
      <br />
      Initiator: {{ techpubStore.currentDetailObject.model ? techpubStore.currentDetailObject.model.initiator.name : '' }}
      <br />
      Last Modified: {{ techpubStore.date(techpubStore.currentDetailObject.model ? techpubStore.currentDetailObject.model.updated_at : '') }}
    </div>
    <a class="button" :href="techpubStore.getWebRoute('',{ projectName: $props.projectName, filename: $props.filename }, $router.getRoutes().find(v => v.name == 'ObjectUpdate'))['url']">Update</a>
    <span> {{ techpubStore.currentDetailObject.modelfilename }} </span>
  </div>
  <div class="mb-3">
    <h5>Description</h5>
    {{ techpubStore.isEmpty(techpubStore.currentDetailObject.modeldescription) ? 'none' : techpubStore.currentDetailObject.modeldescription }}
  </div>
  <hr />
  <br />

  <!-- Verification -->
  <div class="mb-3">
    <form method="POST" :action="techpubStore.getWebRoute('api.post_csdb_object_verify').path"
      @submit.prevent="submit($event)">
      <label for="verificationType"
        class="block mb-2 text-gray-900 dark:text-white text-xl font-bold">VerificationType</label>
      <span>required.</span>
      <br />
      <select name="verificationType" id="verificationType" class="input">
        <option>--Please choose type of verification--</option>
        <option value="tabtop">tabtop</option>
        <option value="onobject">on object</option>
        <option value="ttandoo">both</option>
      </select>
      <div class="text-red-600" v-html="techpubStore.error('verificationType')"></div>
      <br />
      <label for="applicRefId" class="block mb-2 text-gray-900 dark:text-white text-xl font-bold">Applicability Refference
        Id: </label>
      <span>The applicRefId is used to indicate the verification is intended for the certain applicability.</span>
      <input type="text" id="applicRefId" name="applicRefId" class="input w-full" placeholder="describe your project.." />
      <div class="text-red-600" v-html="techpubStore.error('applicRefId')"></div>
      <br />
      <div class="text-center mb-3">
        <button class="button-violet" type="submit" name="verification" value="unverified">Unverified</button>
        <button class="button-violet" type="submit" name="verification" value="firstVerification">First
          Verification</button>
      </div>
      <div class="text-red-600 text-center" v-html="techpubStore.error('verification')"></div>
    </form>
  </div>

  <hr />
  <br />
  <br />

  <!-- viewer -->
  <div class="bg-white p-3 rounded-lg mt-3">
    <h1 class="text-center py-3 bg-gray-900 text-white">
      <button :class="[viewer === 'ietm' ? 'underline' : 'font-light no-underline text-sm hover:text-blue-600',]"
        @click="viewer = 'ietm'"> {{ viewer === 'ietm' ? 'IETM view' : 'switch to ietm view' }} </button>&nbsp;
      <button :class="[viewer === 'pdf' ? 'underline' : 'font-light no-underline text-sm hover:text-blue-600']"
        @click="viewer = 'pdf'"> {{ viewer === 'pdf' ? 'PDF view' : 'switch to pdf view' }} </button>
    </h1>
    <!-- ietm viewer -->
    <div id="ietm-viewer" v-show="viewer == 'ietm'" class="flex">
      <div
        :class="['my-3 detail-container h-[1000px] overflow-scroll', techpubStore.isOpenICNDetailContainer ? 'w-1/2' : 'w-full']">
        <!-- <Search v-if="transformedTemplate" :projectName="$props.projectName" :filename="$props.filename" id="search-obj" name="search-obj">
          <input name="project_name" :value="$props.projectName" type="hidden"/>
          <input name="filename" :value="$props.filename" type="hidden"/>
        </Search> -->
        <component :is="dynamic" v-if="transformedTemplate" />
      </div>
      <div id="icn-detail-container"
        :class="[techpubStore.isOpenICNDetailContainer ? 'w-1/2' : '', 'my-3 py-3 flex justify-center bg-gray-500']">
      </div>
    </div>
    <!-- pdf viewer -->
    <div id="pdf-viewer" v-show="viewer == 'pdf'">
      <form
        :action="techpubStore.getWebRoute('api.pdf_csdb', { projectName: $props.projectName, filename: $props.filename }).path"
        method="GET" @submit.prevent="exportpdf($event)">
        <input type="hidden" value="pdf" name="type" />
        <div class="flex w-full space-x-4 my-3">
          <div class="w-1/3">
            <label for="filename" class="block mb-2 text-gray-900 dark:text-white text-xl font-bold">Filename</label>
            <span>This filename of current detail of techpubStore.currentDetailObject.model</span>
            <input type="text" id="filename" name="filename" class="input w-full" :value="$props.filename" />
          </div>
          <div class="w-1/3">
            <label for="pmType" class="block mb-2 text-gray-900 dark:text-white text-xl font-bold">pmType</label>
            <span>Type the desired pmType or none. Ref <a href="" class="underline text-blue-500">brex-no-xxxx</a></span>
            <input type="text" id="pmType" name="pmType" class="input w-full" placeholder="type desired pmType.." />
          </div>
          <div class="w-1/3">
            <label for="pmEntryType"
              class="block mb-2 text-gray-900 dark:text-white text-xl font-bold">pmEntryType</label>
            <span>Type the desired pmEntryType or none. Ref <a href=""
                class="underline text-blue-500">brex-no-xxxx</a></span>
            <input type="text" id="pmEntryType" name="pmEntryType" class="input w-full"
              placeholder="type desired pmEntryType.." />
          </div>
        </div>
        <div class="text-center"><button class="button-violet" type="submit">Send</button></div>
      </form>

      <embed id="embed-pdf" type="application/pdf" class="w-full h-screen" />

    </div>
  </div>
</template>