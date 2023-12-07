<!-- <script>
import InsertToken from './subcomponents/insert-token.vue';
import {ref} from 'vue';

const objs = ref([
  {
    ident: 'foo'
  },
]);

export default {
  data(){
    return {
      objs: ref([
        {
          ident: 'foo'
        }
      ])
    }
  },
  components: {
    InsertToken,
  }
}
</script> -->

<template>
  <div class="container mx-auto text-center">
    <h1 class="text-2xl font-bold mt-5">SELECT YOUR REPO</h1>

    <!-- <RouterView/> -->
    <!-- <router-view :data="{token:'bar'}"/> -->
    <router-view/>
  </div>
</template>

<!-- <script>
  import ListObject from './subcomponents/list-object.vue';
  import InsertToken from './subcomponents/insert-token.vue';

  // const nm = ['InsertToken'];
  // const routes = {
  //   'ListObject': ListObject,
  //   'InsertToken': InsertToken
  // }

  export default {
    components: {
      ListObject, InsertToken
    },
    data(){
      return {
        currentPath: window.location.hash,
        // view(name){
        //   return name;
        // }
      }
    },
    // mounted() {
    //   window.addEventListener('hashchange', () => {
    //     this.currentPath = window.location.hash
    //   });
    // }
  }
</script> -->
