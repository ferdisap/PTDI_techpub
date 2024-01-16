import { defineStore } from 'pinia';
import { useTechpubStore } from './techpubStore';
import axios from 'axios';
  
  export const useSearchStore = defineStore('useSearchStore', {
    state: () => {
      return {
        techpubStore: useTechpubStore(),
      }
    },
    actions: {
      search(){
        console.log('aa');
        const formData = new FormData(event.target);
        const route = this.techpubStore.getWebRoute('api.csdb_search',formData);
        // dump        
        window.route = route;
        let params = new URLSearchParams(route.url.search);
        const result = {}
        for(const [key, value] of params) { // each 'entry' is a [key, value] tupple
          result[key] = value;
        }
        console.log(result);
      },
    }
  
  
  })
  