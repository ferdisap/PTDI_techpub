import {defineStore} from 'pinia';

export const useResponseStore = defineStore('response', {
  state: () => {
    return {
      response: undefined,
    }
  },
  actions: {
    setResult(response){
      this.response = response;
    }
  }
})
