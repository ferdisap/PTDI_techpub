<script>
import RCMenu from '../../rightClickMenuComponents/RCMenu.vue';
import ContinuousLoadingCircle from '../../loadingProgress/continuousLoadingCircle.vue';
import CheckboxSelector from '../../CheckboxSelector';
import DropdownInputSearch from '../../DropdownInputSearch';
import { useTechpubStore } from '../../../techpub/techpubStore';
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
  components:{ContinuousLoadingCircle, RCMenu},
  props:{
    objectsToDispatch:{
      type: Object,
      default: {},
      // type: Array,
      // default: [],
    }
  },
  methods: {
    async getDDNList() {
      // this.showLoadingProgress = true;
      // let response = await axios({
      //   route: {
      //     name: 'api.get_ddn_list',
      //   },
      //   useMainLoadingBar: false,
      // });

      // if (response.statusText === 'OK') {
      //   // do something here
      //   // this.model = response.data.model;
      // }
      // this.showLoadingProgress = false;
    },
    async submit(event){
      console.log('submit');
      // this.showLoadingProgress = true;
      // let fd = new FormData(event.target);

      // let response = await axios({
      //   route: {
      //     name: 'api.create_ddn',
      //     data: fd,
      //   },
      //   useMainLoadingBar: false,
      // });

      // if (response.statusText === 'OK') {
      //   // do something here
      //   // this.model = response.data.model;
      // }
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
    async searchUser(event){
      if(event.target.id === this.DropdownUserSearch.idInputText){
        let route = this.techpubStore.getWebRoute('api.user_search_model', {sc: event.target.value, limit:5});
        this.DropdownUserSearch.keypress(event, route);
      }
    },
    async searchBrex(event){
      if(event.target.id === this.DropdownBrexSearch.idInputText){
        let route = this.techpubStore.getWebRoute('api.dmc_search_model', {sc: "filename::" + event.target.value, limit:5});
        this.DropdownBrexSearch.keypress(event, route);
      }
    },
    assignObject(data, by = ''){
      console.log(data);
      if(by === 'filenames'){
        this.objects = data.filenames;
      } else if(by === 'paths'){
        this.objects = data.paths;
      } else {
        this.objects = [];
        this.addObjectsByPaths(data);
        this.addObjectsByFilenames(data);
      }
    },
    addObjectsByFilenames(data){
      if(data.filenames && data.filenames.length > 0){
        this.objects = this.objects.concat(data.filenames);
        this.objects = this.objects.filter((item, i, ar) => ar.indexOf(item) === i); // unique array     
      }
    },
    addObjectsByPaths(data){
      if(data.paths && data.paths.length > 0){
        // do axios here
      }
    }
  },
  mounted() {
    let arr =  this.emitter.all.get('dispatchTo'); // 'arr.length < 2' artinya emitter max. hanya dua kali di instance atau baru sekali di emit, check ManagementData.vue
    let indexEmitter = arr.indexOf(arr.find((v) => v.name === 'bound assignObject')) // 'bound addObjects' adalah fungsi, lihat scrit dibawah ini. Jika fungsi anonymous, maka output = ''
    if(arr.length < 2 && indexEmitter < 0) this.emitter.on('dispatchTo', this.assignObject); 

    if((this.$props.objectsToDispatch.filenames && this.$props.objectsToDispatch.filenames.length > 0) || (this.$props.objectsToDispatch.paths && this.$props.objectsToDispatch.paths.length > 0)){
      this.assignObject(this.$props.objectsToDispatch);
    }

    window.DropdownUserSearch = this.DropdownUserSearch;
  }
}
</script>
<template>
  <div class="dispatchTo overflow-auto h-[93%] w-full relative px-3">

    <form @click.prevent="submit($event)">
      <!-- List of filename to dispatch -->
      <div class="mb-3">
        <h2 class="text-center text-3xl">Dispatched Object</h2>
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
      <div class="mb-2 mt-2 flex">
        <div class="mr-2">
          <label class="font-bold" :for="DropdownUserSearch.idInputText">To:&#160;</label>
        </div>
        <div class="w-80 relative">
          <div class="h-[30px]">
            <div v-show="!DropdownUserSearch.isDone" class="mini_loading_buffer_dark right-[5px] top-[5px]"></div>
            <input @keyup.prevent="searchUser($event)" :id="DropdownUserSearch.idInputText" name="personmainemail" class="block mb-0 w-full p-1"/>
          </div>
          <div class="w-full" :id="DropdownUserSearch.idDropdownListContainer">
            <div class="text-sm border-b px-2" v-show="DropdownUserSearch.showList" v-for="(user) in DropdownUserSearch.result" :email="user.email" @click.prevent="DropdownUserSearch.keypress($event)" @keyup.prevent="DropdownUserSearch.keypress($event)">
              {{ user.first_name + " " + user.middle_name + " " + user.last_name}}
              <span class="text-xs">{{ user.email }}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="mb-2 mt-2 flex">
        <div class="mr-2">
          <label class="font-bold" :for="DropdownBrexSearch.idInputText">Brex:&#160;</label>
        </div>
        <div class="w-80 relative">
          <div class="h-[30px]">
            <div v-show="!DropdownBrexSearch.isDone" class="mini_loading_buffer_dark right-[5px] top-[5px]"></div>
            <input @keyup.prevent="searchBrex($event)" :id="DropdownBrexSearch.idInputText" name="brexfilename" class="block mb-0 w-full p-1"/>
          </div>
          <div class="w-full" :id="DropdownBrexSearch.idDropdownListContainer">
            <div class="text-sm border-b px-2" v-show="DropdownBrexSearch.showList" v-for="(dmc) in DropdownBrexSearch.result" :filename="dmc.filename" @click.prevent="DropdownBrexSearch.keypress($event)" @keyup.prevent="DropdownBrexSearch.keypress($event)">
              {{ dmc.filename}}
            </div>
          </div>
        </div>
      </div>
      <div class="w-full text-center">
        <button type="button" class="button bg-violet-400 text-white hover:bg-violet-600">Submit</button>
      </div>
    </form>
    <ContinuousLoadingCircle :show="showLoadingProgress"/>
    <RCMenu v-if="CbSelector.isShowTriggerPanel">
      <div @click="CbSelector.select()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Select</div>
      </div>
      <div @click="remove()" class="flex hover:bg-gray-100 py-1 px-2 rounded cursor-pointer text-gray-900">
        <div class="text-sm">Remove</div>
      </div>
    </RCMenu>
  </div>
</template>