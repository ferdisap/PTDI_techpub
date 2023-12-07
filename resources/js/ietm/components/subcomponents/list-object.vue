<script setup>
import { useResponseStore } from '../../response';
import { useTokenStore } from '../../token';

const responseStore = useResponseStore();
const tokenStore = useTokenStore();

</script>
<template>
  <h1>LIST OBJECT</h1>
  <!-- <div v-for="(obj, i) in objs">
    <p>tes {{ obj.ident }}</p>
  </div> -->
  
  <p v-if="responseStore.response">{{ responseStore.response.data.repo }}</p>
  <p v-if="tokenStore.tokenRepo">{{ tokenStore.tokenRepo }}</p>
</template>