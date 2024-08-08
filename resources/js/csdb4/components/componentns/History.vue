<script>
import axios from 'axios';
import { useTechpubStore } from '../../../techpub/techpubStore';

export default {
  // props: ["filename", "history"],
  props:{
    filename: {
      type: String
    },
    history: {
      type: Object
    }
  },
  data(){
    return {
      H: [],
      techpubStore: useTechpubStore(),
    }
  },
  methods:{
    async setHistoryFromXHR(filename){
      let response = await axios({
        route: {
          name: 'api.get_object_model',
          data: {filename: filename}
        }
      })
      if (response.statusText === 'OK'){
        this.H = response.data.model.remarks.history;
      }
    }
  },
  mounted() {
    if(this.$props.history){
      this.H = this.$props.history;
    }
    else if(this.$props.filename){
      this.setHistoryFromXHR(this.$props.filename);
    }
    else if(this.$route.params.filename){
      this.setHistoryFromXHR(this.$route.params.filename);      
    }
  },
}
</script>
<template>
  <div data-sort="3" class="history">
    <div class="h-[5%] flex mb-3">
      <h1>History Log</h1>
    </div>
    <table>
      <thead>
        <tr>
          <th class="text-base">Date/Time</th>
          <th class="text-base">Code</th>
          <th class="text-base">Message</th>
        </tr>
      </thead>
      <tr v-for="history in H">
        {{ (() => {
          this.arrHistory = history.split(";")
          let dt = arrHistory[0].split(" ");
          this.historyDate = dt[0];
          this.historyTime = dt[1];
          return '';
        })() }}
        <td class="text-base text-nowrap text-center">{{ historyDate }}/<br/>{{historyTime}}</td>
        <td class="text-base">{{ arrHistory[1] }}</td>
        <td class="text-base">{{ arrHistory[2] }}</td>
      </tr>
    </table>
    <!-- <ol>
      <li v-for="history in H">{{ history }}</li>
    </ol> -->
  </div>
</template>