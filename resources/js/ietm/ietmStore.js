import { defineStore } from 'pinia';

export const useIetmStore = defineStore('response', {
  state: () => {
    return {
      tokenRepo: '',
      response: undefined,
      listDMC: [],
      listPMC: [],
      detailObject: undefined,
    }
  },
  actions: {
    setContentView(contentView) {
      this.contentView = contentView;
    },
    setTokenRepo(token) {
      this.tokenRepo = token;
    },
    setResponse(response) {
      this.response = response;
    },
    isEmpty(value) {
      return (value == null || (typeof value === "string" && value.trim().length === 0));
    },
    async pmEntryHandler(filename) {
      let url = window.location.origin + `/api/ietm-pmc/${filename}`;
      let response = await axios.get(url);
      if (response.statusText == 'OK') {
        return response.data;
      }
    }
  }
})
