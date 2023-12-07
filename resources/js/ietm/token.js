import {defineStore} from 'pinia';

export const useTokenStore = defineStore('token', {
  state: () => {
    return {
      tokenRepo: undefined,
    }
  },
  actions: {
    setTokenRepo(token){
      this.tokenRepo = token;
    }
  }
})
