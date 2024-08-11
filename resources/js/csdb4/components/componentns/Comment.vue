<script>
import Randomstring from 'randomstring';
import Modal from '../subComponents/Modal.vue';
import { useTechpubStore } from '../../../techpub/techpubStore';
import axios from 'axios';

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      editor: {
        isShow: true,
        commentModalId: "modal_comment_form"
      }
    }
  },
  components: { Modal },
  // computed:{
  //   brexDmRef(){
  //     return this.techpubStore.currentObjectModel.brexDmRef;
  //   }
  // },
  methods: {
    commentPreferences(event) {
      this.Modal.start(undefined, this.editor.commentModalId)
    },
    async commentSubmit(event){
      const fd = new FormData(event.target);
      if(!fd.get('brexDm')) fd.set('brexDmRef', this.techpubStore.currentObjectModel['brexDmRef']);

      const response = await axios({
        route:{
          name: 'api.create_comment',
          data: fd,
        }
      })
      if(response.statusText === 'OK'){
        
      } else {
        this.commentPreferences();
      }
    },
    tes(event){
      // alert('tes');
      console.log(event.target.value);
      event.target.value = 'foo';
    }
  },
  mounted() {
    window.techpubStore = this.techpubStore;
  }
}
</script>

<!-- template diletakkan didalam Preview.vue -->
<template>
  <div class="mt-2">
    <!-- Associated Comment List -->

    <!-- Comment Editor -->
    <div>
      <div class="flex content-center font-semibold text-sm italic">Insert comment:
        <span @click="editor.isShow = !editor.isShow" class="material-symbols-outlined cursor-pointer">{{ editor.isShow ?
          'arrow_drop_down' : 'arrow_right' }}</span>
      </div>
      <form @submit.prevent="commentSubmit" v-if="editor.isShow">
        <!-- parent comment -->
        <input class="text-sm hidden" name="parentCommentFilename" value=""/>
        <input class="text-sm hidden" name="parentCommentOwnerEmail" value=""/>
        <!-- comment text editor -->
        <div class="flex items-end">
          <text-editor v-pre name="commentContentSimplePara" id="commentContentSimplePara" class="w-full" />
          <div class="flex ml-2">
            <span @click="commentPreferences()" class="material-symbols-outlined cursor-pointer text-sm">tune</span>
            <button type="submit"
              class="material-icons text-sm ml-2 mb-2 hover:bg-blue-300 hover:border rounded-full px-1">send</button>
          </div>
        </div>
        <!-- modal preferences -->
        <Modal :id="editor.commentModalId">
          <template #title>
            <h1 class="text-center font-bold mb-2 text-lg">Submit Preferences</h1>
          </template>
          <div class="w-full text-center mb-2 relative">
            <!-- comment title -->
            <div class="relative text-left mb-2">
              <label class="italic font-semibold ml-1">Title:</label>
              <input placeholder="comment title" type="text" class="p-2 w-full ml-1 inline text-sm rounded-md border">
            </div>
            <div class="error text-sm text-red-600" v-html="techpubStore.error('commentTitle')"></div>
            <!-- commentType, nanti ini sesuai apakah ada parent comment (yang typenya Q) atau jika tidak value 'I' default.-->
            <!-- <input name="commentType" value="q" class="hidden" /> -->
            <!-- comment language/countryIsoCode -->
            <div class="flex items-center mt-1 text-left mb-2">
              <div class="w-1/2 mr-1">
                <label for="languageIsoCode" class="text-sm mr-2 font-semibold italic">Lang:</label>
                <input name="languageIsoCode" id="languageIsoCode" class="mr-2 p-2 rounded-md w-full" value="en" />
              </div>
              <div class="w-1/2 ml-1">
                <label for="countryIsoCode" class="text-sm mr-2 font-semibold italic">Country:</label>
                <input name="countryIsoCode" id="countryIsoCode" class="p-2 rounded-md w-full" value="US" />
              </div>
            </div>
            <div class="error text-sm text-red-600" v-html="techpubStore.error('languageIsoCode')"></div>
            <div class="error text-sm text-red-600" v-html="techpubStore.error('countryIsoCode')"></div>
            <!-- comment BREX -->
            <div class="relative text-left mb-2">
              <label class="italic font-semibold ml-1">BREX:</label>
              <input placeholder="DMC..." type="text" class="p-2 w-full ml-1 inline text-sm rounded-md border" 
                :value="techpubStore.currentObjectModel.brexDmRef"
                name="brexDmRef"
                dd-input="filename,path" 
                dd-type="csdbs" 
                dd-route="api.get_object_csdbs"
                dd-target="self"
                  >
            </div>
            <div class="error text-sm text-red-600" v-html="techpubStore.error('brexDmRef')"></div>
            <!-- comment security class  -->
            <div class="flex items-center mb-2">
              <label for="securityClassification" class="italic font-semibold ml-1 mr-2">Security Classification:</label>
              <input name="securityClassification" id="securityClassification" placeholder="eg:. 05" value="01" 
                class="w-[50px] p-2"
                type="number" min="1" max="5" step="1" onchange="if(parseInt(this.value,10)<10)this.value='0'+this.value;" 
               />
            </div>
            <div class="error text-sm text-red-600" v-html="techpubStore.error('securityClassification')"></div>
            <!-- comment type -->
            <div class="flex items-center mb-2">
              <label for="commentPriorityCode" class="italic text-sm font-semibold ml-1 mr-2">Priority:</label>
              <select id="commentPriorityCode" name="commentPriorityCode" class="p-2 rounded-md">
                <option class="text-sm" value="cp01">Routine</option>
                <option class="text-sm" value="cp02">Emergency</option>
                <option class="text-sm" value="cp03">Safety critical</option>
              </select>
            </div>
            <div class="error text-sm text-red-600" v-html="techpubStore.error('commentPriorityCode')"></div>
            <!-- response type -->
            <div class="flex items-center mb-2">
              <label for="responseType" class="italic text-sm font-semibold ml-1 mr-2">Response:</label>
              <select id="responseType" name="responseType" class="p-2 rounded-md">
                <option class="text-sm" value="rt01">Accepted</option>
                <option class="text-sm" value="rt02">Pending</option>
                <option class="text-sm" value="rt03">Partly rejected</option>
                <option class="text-sm" value="rt04">Rejected</option>
              </select>
            </div>
            <div class="error text-sm text-red-600" v-html="techpubStore.error('responseType')"></div>
            <!-- commentRefs, nanti ini pakai filename sesuai route atau sesuai preview -->
            <div class="relative text-left mb-2">
              <label for="commentRefs" class="italic text-sm font-semibold ml-1 mr-2">Comment Refs:</label>
              <input class="block p-2 w-full ml-1 rounded-md"
                :value="$route.params.filename"
                dd-input="filename,path" 
                dd-type="csdbs" 
                dd-route="api.get_object_csdbs"
                dd-target="self-append"
                name="commentRefs"/>
            </div>
            <div class="error text-sm text-red-600" v-html="techpubStore.error('commentRefs')"></div>
            <!-- commentRemarks -->
            <div class="relative text-left mb-2">
              <label class="italic text-sm font-semibold ml-1 mr-2">Remarks:</label>
              <text-editor v-pre name="commentRemarks[]" class="w-full" />
            </div>
            <div class="error text-sm text-red-600" v-html="techpubStore.error('commentRefs')"></div>
          </div>
        </Modal>
      </form>
    </div>


  </div>
</template>