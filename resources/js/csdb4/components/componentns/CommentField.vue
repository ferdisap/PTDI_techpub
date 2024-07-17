<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
import Remarks from '../subComponents/Remarks.vue';
import ContinuousLoadingCircle from '../../loadingProgress/continuousLoadingCircle.vue'
export default {
  components: {Remarks,ContinuousLoadingCircle},
  data(){
    return {
      techpubStore: useTechpubStore(),
      isNew: true,
      showSetting: false,
      showLoadingProgress: false,
      model: {},

      brexDmRef: '',
      enterpriseName: useTechpubStore().Auth.work_enterprise.name,
      // address
      department: useTechpubStore().Auth.work_enterprise.address.department,
      street: useTechpubStore().Auth.work_enterprise.address.street,
      postOfficeBox: useTechpubStore().Auth.work_enterprise.address.postOfficeBox,
      postalZipCode: useTechpubStore().Auth.work_enterprise.address.postalZipCode,
      city: useTechpubStore().Auth.work_enterprise.address.city,
      country: useTechpubStore().Auth.work_enterprise.address.country,
      state: useTechpubStore().Auth.work_enterprise.address.state,
      province: useTechpubStore().Auth.work_enterprise.address.province,
      building: useTechpubStore().Auth.work_enterprise.address.building,
      room: useTechpubStore().Auth.work_enterprise.address.room,
      phoneNumber: useTechpubStore().Auth.work_enterprise.address.phoneNumber,
      faxNumber: useTechpubStore().Auth.work_enterprise.address.faxNumber,
      email: useTechpubStore().Auth.work_enterprise.address.email,
      internet: useTechpubStore().Auth.work_enterprise.address.internet,
      SITA: useTechpubStore().Auth.work_enterprise.address.SITA,
    }
  },
  methods: {
    async submit(event){
      // event.stopPropagation(); // jaga jaga jika parent commentFiled ini ada form juga 
      this.showLoadingProgress = true;
      let identFirstChild = Object.keys(this.techpubStore.currentObjectModel.remarks.ident)[0] // output= 'dmlCode'
      let modelIdentCode = this.techpubStore.currentObjectModel.remarks.ident[identFirstChild]['modelIdentCode'] // output = 'CN235'
      let senderIdent = this.techpubStore.Auth.work_enterprise.code; // senderIdent tidak boleh diambil dari techubStore.currentObjectModel (DML current) karena comment ini bersifat "SIAPA" yang menulis komentar
      let fd = new FormData(event.target);
      fd.set('com_modelIdentCode', modelIdentCode);
      fd.set('com_senderIdent', senderIdent);
      if(!fd.get('com_brexDmRef')){
        fd.set('com_brexDmRef', this.techpubStore.currentObjectModel.remarks.status['brexDmRef']);
      }

      let response = await axios({
        route: {
          name: 'api.create_comment',
          data: fd,
        },
        useMainLoadingBar: false,
      });

      if(response.statusText === 'OK')
      {
        // do something here
        this.model = response.data.model;
      } else {
        if(response.data.errors) this.showSetting = true;
      }
      this.showLoadingProgress = false;
    }
  },
  mounted() {
  },
}

</script>
<template>
  <form class="w-full" @submit.prevent="submit($event)">
    <div v-if="model.filename" class="italic text-center" x-data="{open: false}" x-on:mouseover="open = true" x-on:mouseleave="open = false">
      <span>{{ model.filename }}</span>
      <a x-show="open" href="#" class="icon" @click="this.copyText(model.filename)"><i class="fas fa-copy"></i></a>
    </div>
    <div class="w-full text-center flex justify-between items-end">
      <textarea name="com_commentContentSimplePara" class="w-[90%] mr-4 shadow-md">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Voluptas, laudantium.</textarea>
      <div>
        <button @click.prevent="showSetting = !showSetting" type="button" class="material-symbols-outlined text-sm mx-2 has-tooltip-arrow" data-tooltip="setting comment">settings</button>
        <button class="material-icons bottom-0 rounded-full border-4 p-1 bg-blue-300 h-[50%] has-tooltip-arrow" data-tooltip="submit" type="submit">send</button>
      </div>
    </div>

    <div v-show="showSetting" class="px-2 bg-white border border-gray-300 rounded-lg flex flex-col text-sm py-4 text-gray-500 shadow-md">
      <div class="mb-2">
        <h5>Comment Ident</h5>
        <div class="flex items-center mt-1">
          <label for="commentTitle" class="text-sm mr-3">Title:</label>
          <input name="com_commentTitle" id="commentTitle" placeholder="eg:. The use of ..."/>
        </div>
        <div class="error text-sm text-red-600" v-html="techpubStore.error('commentTitle')"></div>
        <div class="flex items-center mt-1">
          <label for="commentType" class="text-sm mr-3">Type comment:</label>
          <select id="commentType" name="com_commentType" class="p-0">
            <option value="Q">Q</option>
            <option value="I">I</option>
            <option value="R">R</option>
          </select>
        </div>
        <div class="error text-sm text-red-600" v-html="techpubStore.error('commentType')"></div>
        <div class="flex items-center mt-1">
          <label for="languageIsoCode" class="text-sm mr-2">Lang:</label>
          <input name="com_languageIsoCode" id="languageIsoCode" placeholder="eg:. EN" class="mr-2 w-20" value="en"/>
          <label for="countryIsoCode" class="text-sm mr-2">Country:</label>
          <input name="com_countryIsoCode" id="countryIsoCode" placeholder="eg:. US" class="w-20" value="US"/>
        </div>
        <div class="error text-sm text-red-600" v-html="techpubStore.error('languageIsoCode')"></div>
        <div class="error text-sm text-red-600" v-html="techpubStore.error('countryIsoCode')"></div>
        <div class="flex items-center mt-1">
          <label for="seqNumberRef" class="text-sm mr-3">SeqNumber Reference:</label>
          <input name="com_seqNumberRef" id="seqNumberRef" placeholder="eg:. 00100"/>
        </div>
        <div class="error text-sm text-red-600" v-html="techpubStore.error('seqNumberRef')"></div>
        <div class="flex items-center mt-1">
          <label for="brexDmRef" class="text-sm mr-3">BREX DM Reference:</label>
          <input name="com_brexDmRef" id="brexDmRef" placeholder="eg:. DMC-....xml" :value="brexDmRef ?? null"/>
        </div>
        <div class="error text-sm text-red-600" v-html="techpubStore.error('com_brexDmRef')"></div>
        <div class="flex items-center mt-1">
          <Remarks class_label="text-sm" placeholder="eg.: this comment answer the COM-...xml"/>
        </div>
      </div>
      <hr/>
      <div class="mb-2">
        <h5>Comment Status</h5>
        <div class="flex items-center mt-1">
          <label for="securityClassification" class="text-sm mr-3">Security Classification:</label>
          <input name="com_securityClassification" id="securityClassification" placeholder="eg:. 05" value="01" class="w-[50px]"/>
        </div>
        <div class="error text-sm text-red-600" v-html="techpubStore.error('com_securityClassification')"></div>
        <div class="flex items-center mt-1">
          <label for="commentPriorityCode" class="text-sm mr-3">Comment Priority:</label>
          <select id="commentPriorityCode" name="com_commentPriorityCode" class="p-0">
            <option value="cp01">Routine</option>
            <option value="cp02">Emergency</option>
            <option value="cp03">Safety critical</option>
          </select>
        </div>
        <div class="error text-sm text-red-600" v-html="techpubStore.error('commentPriorityCode')"></div>
        <div class="flex items-center mt-1">
          <label for="responseType" class="text-sm mr-3">Comment Response:</label>
          <select id="responseType" name="com_responseType" class="p-0">
            <option value="rt01">Accepted</option>
            <option value="rt02">Pending</option>
            <option value="rt03">Partly accepted</option>
            <option value="rt04">Rejected</option>
          </select>
        </div>
        <div class="error text-sm text-red-600" v-html="techpubStore.error('responseType')"></div>
        <div class="flex items-center mt-1">
          <span class="text-sm mr-3">Comment Reference:</span>
          <div class="commentRefs">
            <input name="com_commentRefs[]" placeholder="eg:. DML|DMC|PMC|DDN-....xml"/>
            <button onclick="(() => {commentRefs = document.querySelector('div.commentRefs');commentRefs.parentElement.innerHTML += commentRefs.outerHTML;})()" class="add-commentRefs w-4 border text-white bg-green-500 rounded-md">+</button>
            <button onclick="(()=>{if(Object.values(this.parentElement.previousElementSibling.classList).includes('commentRefs')) this.parentElement.remove();})()" class="min-commentRefs w-4 border text-white bg-red-500 rounded-md">-</button>
          </div>
        </div>
        <div class="error text-sm text-red-600" v-html="techpubStore.error('com_commentRefs')"></div>
      </div>
      <hr/>
      <div class="mt-2 mb-2">
        <h5>Comment Address</h5>
        <div class="flex items-center mt-1">
          <label for="enterpriseName" class="text-sm mr-3">Enterprise:</label>
          <input name="com_enterpriseName" id="enterpriseName" placeholder="eg:. PT. Dirgantara Indonesia" class="mr-2" :value="enterpriseName"/>
          <label for="enterpriseUnit" class="text-sm mr-3">Unit:</label>
          <input name="com_enterpriseUnit" id="enterpriseUnit" placeholder="eg:. KP4" class="mr-2 w-20"/>
        </div>
        <div class="error text-sm text-red-600" v-html="techpubStore.error('enterpriseName')"></div>
        <div class="flex items-center mt-1">
          <label for="division" class="text-sm mr-3">Division:</label>
          <input name="com_division" id="division" placeholder="eg:. SE4100"/>
        </div>
        <div class="error text-sm text-red-600" v-html="techpubStore.error('division')"></div>
        <div class="mt-1 relative">
          <div class="flex items-center">
            <span class="text-sm">Address <button type="button" onclick="this.parentElement.parentElement.nextElementSibling.style.display = 'block'" class="text-blue-500 text-sm"><span class="material-symbols-outlined text-black">left_click</span>click</button></span>
          </div>
          <div style="display:none" class="relative border-2 border-gray-500 rounded-md w-[100%] bg-slate-100 text-sm px-3 py-2">
            <button class="absolute top-0 right-0 mx-3 has-tooltip-arrow" data-tooltip="close" style="position:absolute !important" onclick="this.parentElement.style.display = 'none'">x</button>
            <input name="com_department" placeholder="department" :value="department"/>
            <input name="com_street" placeholder="street" :value="street"/>
            <input name="com_postOfficeBox" placeholder="postOfficeBox" :value="postOfficeBox"/>
            <input name="com_postalZipCode" placeholder="postalZipCode" :value="postalZipCode"/>
            <input name="com_city" placeholder="city" :value="city"/>
            <input name="com_country" placeholder="country" :value="country"/>
            <input name="com_state" placeholder="state" :value="state"/>
            <input name="com_province" placeholder="province" :value="province"/>
            <input name="com_building" placeholder="building" :value="building"/>
            <input name="com_room" placeholder="room" :value="room"/>
            <input name="com_phoneNumber[]" placeholder="phoneNumber" :value="phoneNumber[0]"/>
            <input name="com_phoneNumber[]" placeholder="phoneNumber" :value="phoneNumber[1]"/>
            <input name="com_faxNumber[]" placeholder="faxNumber" :value="faxNumber[0]"/>
            <input name="com_faxNumber[]" placeholder="faxNumber" :value="faxNumber[1]"/>
            <input name="com_email[]" placeholder="email" :value="email[0]"/>
            <input name="com_email[]" placeholder="email" :value="email[1]"/>
            <input name="com_internet[]" placeholder="internet" :value="internet[0]"/>
            <input name="com_internet[]" placeholder="internet" :value="internet[1]"/>
            <input name="com_SITA" placeholder="SITA" :value="SITA"/>
          </div>
          <div class="error text-sm text-red-600" v-html="techpubStore.error('com_department')"></div>
          <div class="error text-sm text-red-600" v-html="techpubStore.error('com_department')"></div>
          <div class="error text-sm text-red-600" v-html="techpubStore.error('com_street')"></div>
          <div class="error text-sm text-red-600" v-html="techpubStore.error('com_postOfficeBox')"></div>
          <div class="error text-sm text-red-600" v-html="techpubStore.error('com_postalZipCode')"></div>
          <div class="error text-sm text-red-600" v-html="techpubStore.error('com_city')"></div>
          <div class="error text-sm text-red-600" v-html="techpubStore.error('com_country')"></div>
          <div class="error text-sm text-red-600" v-html="techpubStore.error('com_state')"></div>
          <div class="error text-sm text-red-600" v-html="techpubStore.error('com_province')"></div>
          <div class="error text-sm text-red-600" v-html="techpubStore.error('com_building')"></div>
          <div class="error text-sm text-red-600" v-html="techpubStore.error('com_room')"></div>
          <div class="error text-sm text-red-600" v-html="techpubStore.error('com_phoneNumber')"></div>
          <div class="error text-sm text-red-600" v-html="techpubStore.error('com_phoneNumber')"></div>
          <div class="error text-sm text-red-600" v-html="techpubStore.error('com_faxNumber')"></div>
          <div class="error text-sm text-red-600" v-html="techpubStore.error('com_faxNumber')"></div>
          <div class="error text-sm text-red-600" v-html="techpubStore.error('com_email')"></div>
          <div class="error text-sm text-red-600" v-html="techpubStore.error('com_email')"></div>
          <div class="error text-sm text-red-600" v-html="techpubStore.error('com_internet')"></div>
          <div class="error text-sm text-red-600" v-html="techpubStore.error('com_internet')"></div>
          <div class="error text-sm text-red-600" v-html="techpubStore.error('com_SITA')"></div>
        </div>
      </div>
      <hr/>
      <div class="mt-2 mb-2">
        <h5>Person</h5>
        <div class="flex items-center mt-1">
          <label for="firstName" class="text-sm mr-3">First name:</label>
          <input readonly class="text-gray-300" name="com_firstName" id="firstName" placeholder="eg:. Ferdi" :value="techpubStore.Auth.first_name"/>
        </div>
        <div class="flex items-center mt-1">
          <label for="middleName" class="text-sm mr-3">Middle name:</label>
          <input readonly class="text-gray-300" name="com_middleName" id="middleName" placeholder="eg:. Rahman" :value="techpubStore.Auth.middle_name"/>
        </div>
        <div class="flex items-center mt-1">
          <label for="lastName" class="text-sm mr-3">Last name:</label>
          <input readonly class="text-gray-300" name="com_lastName" id="lastName" placeholder="eg:. Saptoko" :value="techpubStore.Auth.last_name"/>
        </div>
        <div class="flex items-center mt-1">
          <label for="jobTitle" class="text-sm mr-3">Job Title:</label>
          <input name="com_jobTitle" id="jobTitle" placeholder="eg:. Structural Engineer" :value="techpubStore.Auth.job_title"/>
        </div>
      </div>
    </div>
  </form>
  <ContinuousLoadingCircle :show="showLoadingProgress"/>
</template>