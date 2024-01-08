<script>
import { useTechpubStore } from '../../../techpub/techpubStore';
import BrexDetailContextRule from './BrexDetailContextRule.vue'
import BrexDetailSNSRule from './BrexDetailSNSRule.vue'
import BrexDetailNonContextRule from './BrexDetailNonContextRule.vue'

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      view: ''
    }
  },
  components: {BrexDetailContextRule, BrexDetailSNSRule, BrexDetailNonContextRule}
}
</script>

<template>

  <div class="w-full">
    <div class="container text-center mt-3">
      <h1>N219 Project - BREX</h1>
      <div class="nav justify-content-center d-flex">
        <a href="javascript:void(0)" @click="view = 'BrexDetailSNSRule'" class="nav-link">SNS Rules</a>
        <a href="javascript:void(0)" @click="view = 'BrexDetailContextRule'" class="nav-link">Context Rules</a>
        <a href="javascript:void(0)" @click="view = 'BrexDetailNonContextRule'" class="nav-link">Non-Context Rules</a>
      </div>
      
      <div class="container mt-3 d-flex justify-content-center">
        <input class="form-control w-50" type="text" filterBy="all" placeholder="find everything of brex.." onkeypress="BrexSearch.evetListener(this,event)">
      </div>
      <p id="totalSearchResult" class="mb-0 my-1"></p>
    </div>

    <KeepAlive>
      <component :is="view"/>
    </KeepAlive>

  </div>



</template>