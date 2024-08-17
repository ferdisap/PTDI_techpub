<script>
import jp from 'jsonpath';
import Remarks from './Remarks.vue';
import {showDDNContent, refresh} from './DDNVue';
export default {
  data(){
    return {
      showLoadingProgress: false,
      DDNObject: {},
    }
  },
  components:{Remarks},
  methods:{
    showDDNContent: showDDNContent,
    refresh: refresh,
  },
  mounted(){
    window.ddn = this;
    window.jp = jp;
    if (this.$props.filename && (this.$props.filename.substring(0, 3) === 'DDN')) {
      this.showDDNContent(this.$props.filename);
    }

    let emitters = this.emitter.all.get('DDN-refresh'); // 'emitter.length < 2' artinya emitter max. hanya dua kali di instance atau baru sekali di emit, check ManagementData.vue
    if (emitters) {
      const indexEmitter = emitters.indexOf(emitters.find((v) => v.name === 'bound refresh')) // 'bound addObjects' adalah fungsi, lihat scrit dibawah ini. Jika fungsi anonymous, maka output = ''
      if (emitters.length < 1 && indexEmitter < 0) this.emitter.on('DDN-refresh', this.refresh);
    } else this.emitter.on('DDN-refresh', this.refresh);
  }
}
</script>
<template>
  {{ DDNObject.dispatchFileNames }}
  <div class="ddn">
    <h1>Ident and Status Section</h1>
    <div class="mb-2 flex">
      <div class="mr-2">
        <div class=" font-bold italic">Code: </div>
        <div>{{ DDNObject.code }}</div>
      </div>
      <div class="mr-2">
        <div class=" font-bold italic">InWork: </div>
        <div>{{ DDNObject.inWork }}</div>
      </div>
      <div class="mr-2">
        <div class=" font-bold italic">Date: </div>
        <div>{{ DDNObject.issueDate }}</div>
      </div>
    </div>
    <div class="mb-2 flex">
      <div class="mr-2">
        <div class=" font-bold italic">Security</div>
        <div>{{ DDNObject.securityClassification }}</div>
      </div>
      <div class="mr-2">
        <div class=" font-bold italic">BREX</div>
        <div>{{ DDNObject.brex }}</div>
      </div>
    </div>
    <div>
      <Remarks :para="DDNObject.remarks" class_label="text-sm font-bold italic"/>
    </div>
    
    <h1>Content Section</h1>
    <div >
      <ol v-if="DDNObject.dispatchFileNames && DDNObject.dispatchFileNames.length">
        <li v-for="(filename) in DDNObject.dispatchFileNames">
          {{ filename }}
        </li>
      </ol>
      <ol v-if="DDNObject.mediaIdents && DDNObject.mediaIdents.length">
        <li v-for="(filename) in DDNObject.dispatchFileNames">
          {{ filename }}
        </li>
      </ol>
    </div>
  </div>
</template>