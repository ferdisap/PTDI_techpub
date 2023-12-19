import { defineStore } from 'pinia';

export const useIetmStore = defineStore('response', {
  state: () => {
    return {
      listDMC: [],
      listPMC: [],
      detailObject: undefined,
      showListObject: false,
      showIdentSection: false,
      showEntity: false,
      show:true,
      objects: [],
      showLoadingBar: false


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
    addObjects(obj = {}){
      /*
      obj = {
        repoName: '',
        filename: '',
        transformed_html = ''
      }
      */
      if(obj.hasOwnProperty('transformed_html')){
        this.objects.push(obj);
        if(this.objects.length > 10){
          this.objects.shift();
        }
      }
    },
    getObj(repoName, filename){
      return this.objects.find(e => (e.filename == filename && e.repoName == repoName));
    },
    async getRepos(token){
      this.showLoadingBar = true;
      const url = new URL(window.location.origin + "/api" + "/ietm/repo");
      url.search = new URLSearchParams({ tokenRepo: token });
      let response = await axios.get(url).catch(e => e.response);
      this.showLoadingBar = false;
      return response;
    },
    async getDetailObject(repoName, filename){
      this.showLoadingBar = true;
      const url = new URL(window.location.origin + "/api" + `/ietm/${repoName}/${filename}`);
      let response = await axios.get(url).catch(e => e.response);
      this.showLoadingBar = false;
      return response;
    },
    async getObjects(repoName, params = {}){
      this.showLoadingBar = true;
      const url = new URL(window.location.origin + '/api/ietm/repo/' + repoName);
      url.search = new URLSearchParams(params);
      let response = await axios.get(url).catch(e => e.response);;
      this.showLoadingBar = false;
      return response;
    },
    // unshow(){
    //   this.showListObject = false;
    //   this.showIdentSection = false;
    //   this.showEntity = false;
    // }
  }
})
