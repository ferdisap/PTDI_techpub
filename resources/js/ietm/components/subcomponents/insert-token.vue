<script>
import {ref} from 'vue';
import { useResponseStore } from '@/ietm/response.js';
import { useTokenStore } from '../../token.js';

export default {
  data(){
    return {
      responseStore: useResponseStore(),
      tokenStore: useTokenStore(),
      token: ref(undefined),
    }
  },
  methods: {
    getListObject () {
      axios.get('/api/ietm/repo')
      .then((response) => {
        this.responseStore.setResult(response);
        this.tokenStore.setTokenRepo(token.value);
        this.$router.push({name: 'ListObject'});
      });
    }
  }
}
</script>

<template>
  <div>
    <label for="token">Repo Token</label>
    <br/>
    <input type="text" placeholder="token" name="token" id="token" :value="token">
    <button @click="getListObject">GET</button>

  </div>
</template>