import { defineStore } from 'pinia';

export const useIetmStore = defineStore('response', {
  state: () => {
    return {
      // tokenRepo: '',
      // response: undefined,
      listDMC: [],
      listPMC: [],
      // repo: {},
      detailObject: undefined,
      showListObject: false,
      showIdentSection: false,
      showEntity: false,
      show:true,
    }
  },
  actions: {
    isEmpty(value) {
      return (value == null || (typeof value === "string" && value.trim().length === 0));
    },
    async pmEntryHandler(filename) {
      let url = window.location.origin + `/api/ietm-pmc/${filename}`;
      let response = await axios.get(url);
      if (response.statusText == 'OK') {
        return response.data;
      }
    },
    // unshow(){
    //   this.showListObject = false;
    //   this.showIdentSection = false;
    //   this.showEntity = false;
    // }
  }
})
