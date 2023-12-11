import {defineStore} from 'pinia';

export const useIetmStore = defineStore('response', {
  state: () => {
    return {
      // contentView: '',
      tokenRepo: '',
      response: undefined,
      // view: 'Content',
      listDMC: [],
      listPMC: [],
      detailObject: undefined,
    }
  },
  actions: {
    setContentView(contentView){
      this.contentView = contentView;
    },
    setTokenRepo(token){
      this.tokenRepo = token;
    },
    setResponse(response){
      this.response = response;
    },
    isEmpty(value) {
      return (value == null || (typeof value === "string" && value.trim().length === 0));
    }
  }
})
