<script>
import axios from 'axios';
import { useTechpubStore } from '../../../techpub/techpubStore';

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      object: {},
      filename_transformed: '',
      transformedObject: '',
      viewer: 'ietm',
    }
  },
  computed: {
    dynamic() {
      return {
        template: this.transformedObject,
        data() {
          return {
            store: useTechpubStore(),
          }
        },
      }
    }
  },
  props: ['projectName', 'filename'],
  methods: {
    submit(evt) {
      this.techpubStore.showLoadingBar = true;
      const formData = new FormData(evt.target);
      formData.append('filename', this.object.filename);
      formData.append(evt.submitter.name, evt.submitter.value);
      let api =  axios.create({
        url: evt.target.action,
        method: evt.target.method,
        data: formData,
      })
      api
        .then(response => this.$root.success(response))
        .catch(error => this.$root.error(error));
    },
    exportpdf(evt){
      this.techpubStore.showLoadingBar = true;
      const formData = new FormData(evt.target);
      const url = new URL(evt.target.action);
      url.search = new URLSearchParams(formData);
      
      let embed = $('#pdf-viewer #embed-pdf');
      embed.attr('src', url.toString())
      this.techpubStore.showLoadingBar = false;
    },
    transform() {
      if (this.filename_transformed != this.$props.filename) {
        this.filename_transformed = this.$props.filename;
        this.transformedObject = '';
        this.techpubStore.showLoadingBar = true;
        const route = this.techpubStore.getWebRoute('api.get_transform_csdb', { projectName: this.$props.projectName, filename: this.$props.filename });
        axios({
          url: route.url,
          method: 'GET',
          data: route.params,
          responseType: 'blob',
        })
          .then(async (response) => {
            let mime = response.headers.getContentType();
            const blob = new Blob([response.data], { type: mime });
            if (mime.includes('text/html')) {
              this.transformedObject = await blob.text();
            }
            else if (mime.includes('image')) {
              const url = URL.createObjectURL(blob);
              this.transformedObject = `<img src="${url.toString()}"/>`;
            }
            else {
              const url = URL.createObjectURL(blob);
              this.transformedObject = `<embed src="${url.toString()}" style="width:100%; height:50vh"/>`;
            }
            this.techpubStore.showLoadingBar = false;
          })
          .catch(error => {
            this.$root.error(error);
          });
      }

    },
  },
  async mounted() {
    // window.techpubStore = this.techpubStore;
    await this.techpubStore.setProject();
    let object = this.techpubStore.object(this.$props.projectName, this.$props.filename);
    if (!object) {
      this.techpubStore.showLoadingBar = true;
      const route = this.techpubStore.getWebRoute('api.get_csdb_object_data', { projectName: this.$props.projectName, filename: this.$props.filename })
      axios({
        url: route.url,
        method: 'GET',
        data: route.params,
      })
        .then(response => {
          this.object = response.data[0];
          this.techpubStore.showLoadingBar = false;
        })
        .catch(error => {
          this.$root.error(error);
        })
    } else {
      this.object = object;
    }
    if (!this.transformedObject) {
      this.transform();
    }
  },
  beforeUpdate() {
    this.transform();
  },
}
</script>
<template>
  <h1 class="text-center">Detail</h1>
  <h4 class="text-center"> {{ $props.filename }} </h4>
  <div class="text-center flex items-start justify-center">
    <div class="text-left border-r-2 px-2 border-slate-950">
      Status: {{ object.status }}
      <br />
      Initiator: {{ object.initiator ? object.initiator.name : '' }}
      <br />
      Last Modified: {{ techpubStore.date(object.updated_at) }}
    </div>
    <router-link class="button"
      :to="{ name: 'ObjectUpdate', params: { projectName: $props.projectName, filename: object.filename }, }">update</router-link>
  </div>
  <div class="mb-3">
    <h5>Description</h5>
    {{ techpubStore.isEmpty(object.description) ? 'none' : object.description }}
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
        <button class="button-violet" type="submit" name="verification" value="firstVerification">First Verification</button>
      </div>
      <div class="text-red-600 text-center" v-html="techpubStore.error('verification')"></div>
    </form>
  </div>

  <hr />
  <br/>
  <br/>

  <!-- viewer -->
  <div class="bg-white p-3 rounded-lg mt-3">
    <h1 class="text-center py-3 bg-gray-900 text-white">
      <button :class="[ viewer === 'ietm' ? 'underline' : 'font-light no-underline text-sm hover:text-blue-600',]" @click="viewer = 'ietm'"> {{ viewer === 'ietm' ? 'IETM view' : 'switch to ietm view' }} </button>&nbsp;
      <button :class="[ viewer === 'pdf' ? 'underline' : 'font-light no-underline text-sm hover:text-blue-600']" @click="viewer = 'pdf'"> {{ viewer === 'pdf' ? 'PDF view' : 'switch to pdf view' }} </button>
    </h1>
    <!-- ietm viewer -->
    <div id="ietm-viewer" v-show="viewer == 'ietm'">
      <div class="my-3">
        <component :is="dynamic" v-if="transformedObject"/>
      </div>
    </div>
    <!-- pdf viewer -->
    <div id="pdf-viewer" v-show="viewer == 'pdf'">
      <form :action="techpubStore.getWebRoute('api.pdf_csdb',{ projectName: $props.projectName, filename: $props.filename }).path" method="GET" @submit.prevent="exportpdf($event)">
        <input type="hidden" value="pdf" name="type"/>
        <div class="flex w-full space-x-4 my-3">
          <div class="w-1/3">
            <label for="filename" class="block mb-2 text-gray-900 dark:text-white text-xl font-bold">Filename</label>
            <span>This filename of current detail of object.</span>
            <input type="text" id="filename" name="filename" class="input w-full" :value="$props.filename" />
          </div>
          <div class="w-1/3">
            <label for="pmType" class="block mb-2 text-gray-900 dark:text-white text-xl font-bold">pmType</label>
            <span>Type the desired pmType or none. Ref <a href="" class="underline text-blue-500">brex-no-xxxx</a></span>
            <input type="text" id="pmType" name="pmType" class="input w-full" placeholder="type desired pmType.."/>
          </div>
          <div class="w-1/3">
            <label for="pmEntryType" class="block mb-2 text-gray-900 dark:text-white text-xl font-bold">pmEntryType</label>
            <span>Type the desired pmEntryType or none. Ref <a href="" class="underline text-blue-500">brex-no-xxxx</a></span>
            <input type="text" id="pmEntryType" name="pmEntryType" class="input w-full" placeholder="type desired pmEntryType.."/>
          </div>
        </div>
        <div class="text-center"><button class="button-violet" type="submit">Send</button></div>
      </form>

      <!-- <embed type="application/pdf" src="/csdb/object/export?type=pdf&filename=DMC-MALE-A-00-00-00-00A-001A-A_000-01_EN-EN.xml&pmType=&pmEntryType="/> -->
      <embed id="embed-pdf" type="application/pdf" class="w-full h-screen"/>

    </div>
  </div>
</template>