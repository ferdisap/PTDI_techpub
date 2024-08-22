<script>
import RCMenu from '../../rightClickMenuComponents/RCMenu.vue';
import ContinuousLoadingCircle from '../../loadingProgress/continuousLoadingCircle.vue';
import {CheckboxSelector} from '../../CheckboxSelector';
import DropdownInputSearch from '../../DropdownInputSearch';
import { useTechpubStore } from '../../../techpub/techpubStore';
import Remarks from '../subComponents/Remarks.vue';
import {isObject} from '../../helper'
import RoutesWeb from '../../RoutesWeb';
import axios from 'axios';
export default {
  data() {
    return {
      // model: {},
      showLoadingProgress: false,
      // selectionMode: false,
      // isShowRcMenu: false,
      // checkboxId: '',
      // isSelectAll: false,
      techpubStore: useTechpubStore(),
      CbSelector: new CheckboxSelector,
      objects: [], // berisi filenames
      DropdownUserSearch: new DropdownInputSearch('email'),
      DropdownBrexSearch: new DropdownInputSearch('filename')
    }
  },
  components:{ContinuousLoadingCircle, RCMenu, Remarks},
  props:{
    objectsToDispatch:{
      type: Array,
      default: [],
      // type: Array,
      // default: [],
    }
  },
  methods: {
    async submit(event){
      let fd = new FormData(event.target);

      fd.set('dispatchFromPersonEmail', this.techpubStore.Auth.email);

      for (var i = 0; i < this.objects.length; i++) {
        fd.append('deliveryListItemsFilename[]', this.objects[i]);
      }
      
      let response = await axios({
        route: {
          name: 'api.create_ddn',
          data: fd,
        },
      });

      if (response.statusText === 'OK') {
        // do something here
        // this.model = response.data.model;
        this.emitter.emit('createDDNFromDispatchTo', response.data.csdb);

      }
      // this.showLoadingProgress = false;
    },
    clickFilename(event){
      if(this.CbSelector.selectionMode){
        this.CbSelector.select();
      }
    },
    remove(){
      if(this.CbSelector.selectionMode){
        let values = this.CbSelector.getAllSelectionValue()
        values.forEach((value)=>{
          let i = this.objects.indexOf(value);
          if(i > -1) this.object.splice(i,1);
        });
      }
      else {
        let cbid = this.CbSelector.cbHovered;
        this.CbSelector.selectionMode = true;
        setTimeout(()=>{
          let value = document.getElementById(cbid).value;
          let i = this.objects.indexOf(value);
          if(i > -1) this.objects.splice(i,1);
          this.CbSelector.selectionMode = false;
        },0);
      }
      this.CbSelector.isShowTriggerPanel = false;
    },
    // bisa langsung pakai DropdownBrexSearch@keypress di tag htmlnya, tanpa pakai fungsi searchUser ini
    async searchUser(event){
      if(event.target.id === this.DropdownUserSearch.idInputText){
        // let route = this.techpubStore.getWebRoute('api.user_search_model', {sc: event.target.value, limit:5});
        const route = RoutesWeb.get('api.user_search_model', {sc: event.target.value, limit:5});
        this.DropdownUserSearch.keypress(event, route);
      }
    },
    // bisa langsung pakai DropdownBrexSearch@keypress di tag htmlnya, tanpa pakai fungsi searchBrexIni
    async searchBrex(event){
      if(event.target.id === this.DropdownBrexSearch.idInputText){
        // let route = this.techpubStore.getWebRoute('api.dmc_search_model', {sc: "filename::" + event.target.value, limit:5});
        const route = RoutesWeb.get('api.dmc_search_model', {sc: "filename::" + event.target.value, limit:5});
        this.DropdownBrexSearch.keypress(event, route);
      }
    },
    setObject(data){
      if(Array.isArray(data)){
        this.objects = [];
        data.forEach((v) => {
          if(isObject(v)) this.objects.push(v.filename);
          else this.objects.push(v);
        });
      }
    },
    addObject(data){
      if(Array.isArray(data)){
        data.forEach((v) => {
          if(isObject(v)) this.objects.push(v.filename);
          else this.objects.push(v);
        });
      }
    },
    removeObject(data){
      if(Array.isArray(data)){
        data.forEach((v) => {
          let filename;
          if(isObject(v)) filename = v.filename;
          else filename = v;
          let index = this.objects.indexOf(filename);
          if(index >= 0) this.objects.splice(index,1);
        });
      }
    },
  },
  mounted() {
    let emitters =  this.emitter.all.get('dispatchTo'); // 'emitter.length < 2' artinya emitter max. hanya dua kali di instance atau baru sekali di emit, check ManagementData.vue
    let indexEmitter;
    if(emitters){
       indexEmitter = emitters.indexOf(emitters.find((v) => v.name === 'bound setObject')) // 'bound addObjects' adalah fungsi, lihat scrit dibawah ini. Jika fungsi anonymous, maka output = ''
      if(emitters.length < 2 && indexEmitter < 0) this.emitter.on('dispatchTo', this.setObject); 
    }

    emitters =  this.emitter.all.get('AddDispatchTo'); // 'emitter.length < 1' artinya emitter baru sekali di instance/emit, yakni cuma di Dispatch.vue saja
    if(emitters){
      indexEmitter = emitters.indexOf(emitters.find((v) => v.name === 'bound addObject')) // 'bound addObjects' adalah fungsi, lihat scrit dibawah ini. Jika fungsi anonymous, maka output = ''
      if(emitters.length < 2 && indexEmitter < 0) this.emitter.on('AddDispatchTo', this.addObject); 
    }

    emitters =  this.emitter.all.get('RemoveDispatchTo'); // 'emitter.length < 1' artinya emitter baru sekali di instance/emit, yakni cuma di Dispatch.vue saja
    if(emitters){
      indexEmitter = emitters.indexOf(emitters.find((v) => v.name === 'bound removeObject')) // 'bound addObjects' adalah fungsi, lihat scrit dibawah ini. Jika fungsi anonymous, maka output = ''
      if(emitters.length < 2 && indexEmitter < 0) this.emitter.on('RemoveDispatchTo', this.removeObject); 
    }

    if((this.$props.objectsToDispatch.length > 0)){
      this.setObject(this.$props.objectsToDispatch);
    }
  }
}
</script>
<template>
  <div class="dispatchto overflow-auto h-[93%] w-full relative px-3">

    <form @submit.prevent="submit($event)">
      <!-- List of filename to dispatch -->
      <h1 class="text-blue-500 w-full text-center my-2">Dispatched Object</h1>
      <div class="flex items-center mt-1">
        <label for="securityClassification" class="text-sm mr-3 font-bold">Security Classification:</label>
        <input name="securityClassification" id="securityClassification" placeholder="eg:. 05" value="01" class="w-[50px]"/>
      </div>
      <div class="mb-3">
        <table class="text-left" :id="CbSelector.id">
          <thead>
            <tr>
              <th v-if="CbSelector.selectionMode" class="w-10"></th>
              <th class="w-10">No</th>
              <th>Filename</th>
            </tr>
          </thead>
          <tbody>
            <tr @click.prevent="clickFilename($event)" @contextmenu.prevent="CbSelector.isShowTriggerPanel = true" @mouseover="()=>{if(!CbSelector.isShowTriggerPanel) CbSelector.cbHovered = 'dt'+filename}" v-for="(filename, i) in objects" class="hover:bg-blue-300">
              <td v-if="CbSelector.selectionMode">
                <input type="checkbox" :value="filename" :id="'dt'+filename">
              </td>
              <td class="p-0 text-sm text-center">{{ i+1 }}</td>
              <td class="p-0 text-sm">{{ filename }}</td>
              <!-- <input name="ddn_deliverylistitem" :value="filename" class="mt-0 mb-0 bg-white border-none min-w-80 hover:bg-blue-300"/> -->
              <!-- <div class="mt-0 mb-0 bg-white border-none min-w-80 text-sm">{{ filename }}</div> -->
            </tr>
          </tbody>
        </table>
      </div>
      <hr/>
      <!-- Dropdown User Search -->
      <div class="mb-2 mt-2 flex">
        <div class="mr-2">
          <label class="font-bold text-sm" :for="DropdownUserSearch.idInputText">Send To:&#160;</label>
        </div>
        <div class="w-80 relative">
          <div class="h-[30px]">
            <div v-show="!DropdownUserSearch.isDone" class="mini_loading_buffer_dark right-[5px] top-[5px]"></div>
            <input  @keyup.prevent="searchUser($event)" :id="DropdownUserSearch.idInputText" name="dispatchToPersonEmail" class="block mb-0 w-full p-1" autocomplete="off" aria-autocomplete="none"/>
          </div>
          <div class="w-full" :id="DropdownUserSearch.idDropdownListContainer">
            <div class="text-sm border-b px-2" v-show="DropdownUserSearch.showList" v-for="(user) in DropdownUserSearch.result" :email="user.email" @click.prevent="DropdownUserSearch.keypress($event)" @keyup.prevent="DropdownUserSearch.keypress($event)">
              {{ user.first_name + " " + user.middle_name + " " + user.last_name}}
              <span class="text-xs">{{ user.email }}</span>
            </div>
          </div>
        </div>
      </div>
      <!-- Dropdown BREX Search -->
      <div class="mb-2 mt-2 flex">
        <div class="mr-2">
          <label class="font-bold text-sm" :for="DropdownBrexSearch.idInputText">Brex:&#160;</label>
        </div>
        <div class="w-80 relative">
          <div class="h-[30px]">
            <div v-show="!DropdownBrexSearch.isDone" class="mini_loading_buffer_dark right-[5px] top-[5px]"></div>
            <input @keyup.prevent="searchBrex($event)" :id="DropdownBrexSearch.idInputText" name="brexDmRef" class="block mb-0 w-full p-1" autocomplete="off" aria-autocomplete="none"/>
          </div>
          <div class="w-full" :id="DropdownBrexSearch.idDropdownListContainer">
            <div class="text-sm border-b px-2" v-show="DropdownBrexSearch.showList" v-for="(dmc) in DropdownBrexSearch.result" :filename="dmc.filename" 
              @click.prevent="DropdownBrexSearch.keypress($event)" 
              @keyup.prevent="DropdownBrexSearch.keypress($event)">
              {{ dmc.filename}}
            </div>
          </div>
        </div>
      </div>
      <div class="blok items-center mt-1 mb-2">
        <Remarks class_label="text-sm font-bold" placeholder="eg.: this comment answer the COM-...xml"/>
      </div>
      <div class="w-full text-center">
        <button type="submit" class="button bg-violet-400 text-white hover:bg-violet-600">Submit</button>
      </div>
    </form>
    <ContinuousLoadingCircle :show="showLoadingProgress"/>
    <RCMenu v-if="CbSelector.isShowTriggerPanel" id="DispatchTo">
      <div @click="CbSelector.select()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Select</div>
      </div>
      <div @click="remove()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Remove</div>
      </div>
    </RCMenu>
  </div>
</template>