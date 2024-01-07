<script>
import axios from 'axios';
import { useTechpubStore } from '../../../techpub/techpubStore';

export default {
  data() {
    return {
      techpubStore: useTechpubStore(),
      transformedObject: '',
      applSchema_checkedAll: true,
    }
  },
  computed: {
    dynamic() {
      return {
        template: this.transformedObject,
        data() {
          return {
            store: useTechpubStore(),
          }
        },
        methods: {
          openDetail(brDecisionPointUniqueIdent, brDecisionIdentNumber, brParaId){

            let tr = event.target;
            while (tr.nodeName != 'TR') {
              tr = tr.parentElement;
            }
            if(tr.nextElementSibling.style.display == ''){
              tr.nextElementSibling.style.display = 'none';
              return;
            }
            
            const route = this.store.getWebRoute('get_brdp_transformBrPara',{project_name: this.$route.params.projectName, filename: this.$route.params.filename, brParaId: brParaId});
            axios({
              url: route.url,
              responseType: 'blob',
            })
            .then(async (response) => {
              let mime = response.headers.getContentType();
              const blob = new Blob([response.data], { type: mime });
              if (mime.includes('text/html')) {
                tr.nextElementSibling.firstElementChild.innerHTML = await blob.text();
                tr.nextElementSibling.style.display = ''
                this.store.showLoadingBar = false;
              }
            })
            .catch(error => {
              this.$root.error(error);
            });
          },

          search(){
            if (event.keyCode === 13) { // enter button
              event.preventDefault();

              let useApplSchema = document.querySelector('#useApplSchema').checked;
              let applSchema = [];
              if(useApplSchema){
                $('#applSchema input[type = checkbox]').each((i,input) => {
                  if(input.checked){
                    applSchema.push(input.value);   
                  }
                });
              }
              applSchema = applSchema.join(',')
              
              const formData = new FormData();
              
              // outputnya nanti adalah formData = {0all: '....', 1ident: '....'}
              let searchInput_onTopBar = document.querySelector('#search');
              formData.append(0+'all', searchInput_onTopBar.value);
              let fb = document.querySelectorAll(`.filterSort`);
              [1,2,3,4,5].forEach(no => {
                fb.forEach(el => {
                  if (el.value == no && el.nextElementSibling.value != ''){
                    formData.append(no+el.nextElementSibling.getAttribute('filterby'), el.nextElementSibling.value);
                  }
                });
              });

              formData.append('project_name', this.$route.params.projectName);
              formData.append('filename', this.$route.params.filename);
              formData.append('applSchema', applSchema);

              const route = this.store.getWebRoute('get_brdp_search', formData);
              axios({
                url: route.url,
                method: 'GET',
                data: route.params,                
              })
              .then( rsp => {
                $('table#brdpList-table > tbody > tr').hide();
                rsp.data.forEach(brParaId => {
                  $(`table#brdpList-table > tbody > tr#${brParaId}`).show();
                });
                $('#total-result').text(`Total Result: ${rsp.data.length}`)
              })
            }
          },
        }
      }
    }
  },
  props: ['projectName', 'filename'],
  methods: {
    transform() {
      this.filename_transformed = this.$props.filename;
      this.transformedObject = '';
      this.techpubStore.showLoadingBar = true;
      const route = this.techpubStore.getWebRoute('get_brdp_transform',{project_name: this.$route.params.projectName, filename: this.$route.params.filename});
      axios({
        url: route.url,
        method: 'GET',
        data: route.params,
        responseType: 'blob',
      })
        .then(async (response) => {
          let mime = response.headers.getContentType();
          const blob = new Blob([response.data], { type: mime });
          if (mime.includes('text/html')) {
            this.transformedObject = await blob.text();
            if (window.location.hash) {
              setTimeout(() => {
                document.getElementById(window.location.hash.slice(1)).scrollIntoView();
              }, 0);
            }
            this.techpubStore.showLoadingBar = false;
            setTimeout(() => {
              let totalBRDP = $('table#brdpList-table > tbody > tr[id]').length
              $('#total-result').text(`Total Result: ${totalBRDP}`)
            },0);
          }
        })
        .catch(error => {
          this.$root.error(error);
        });
    },
    selectDeselectAll(){
      $('#applSchema input[type = checkbox]').each((i,input) => {
        input.checked = !this.applSchema_checkedAll;
      });
      this.applSchema_checkedAll = !this.applSchema_checkedAll;
    },
  },
  async mounted() {
    if (!this.transformedObject) {
      this.transform();
    }
  }
}
</script>

<template>
  <div class="block w-full">

    <div class="input-group d-flex justify-center mt-2">
      <input class="me-2" type="checkbox" id="useApplSchema" name="useApplSchema">
      <button class="underline text-primary" data-bs-toggle="modal" data-bs-target="#applSchema" style="max-width:130px">Select Schema</button>
    </div>

    <div class="text-center" id="total-result">total result: </div>
  
    <component :is="dynamic" v-if="transformedObject" />
  
    <div class="modal fade" id="applSchema" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="applSchemaLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="applSchemaLabel">Applicability Schema</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
          </div>
          <a href="javascript:void(0)" class="text-end mx-3" @click="selectDeselectAll">select/deselect all</a>
          <div class="modal-body">
            <!-- check box -->
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="appliccrossreftableXsd" name="appliccrossreftableXsd" id="appliccrossreftableXsd" checked>
              <label class="form-check-label" for="appliccrossreftableXsd">Applicable Cross Reference Table</label><br/>
              
              <input class="form-check-input" type="checkbox" value="brDocXsd" name="brDocXsd" id="brDocXsd" checked>
              <label class="form-check-label" for="brDocXsd">Business rule data module</label><br/>
              
              <input class="form-check-input" type="checkbox" value="brexXsd" name="brexXsd" id="brexXsd" checked>
              <label class="form-check-label" for="brexXsd">Business rule exchange index</label><br/>
    
              <input class="form-check-input" type="checkbox" value="checklistXsd" name="checklistXsd" id="checklistXsd" checked>
              <label class="form-check-label" for="checklistXsd">Crew checklist</label><br/>
    
              <input class="form-check-input" type="checkbox" value="commentXsd" name="commentXsd" id="commentXsd" checked>
              <label class="form-check-label" for="commentXsd">Comment</label><br/>
    
              <input class="form-check-input" type="checkbox" value="comrepXsd" name="comrepXsd" id="comrepXsd" checked>
              <label class="form-check-label" for="comrepXsd">Common information repository</label><br/>
    
              <input class="form-check-input" type="checkbox" value="condcrossreftableXsd" name="condcrossreftableXsd" id="condcrossreftableXsd" checked>
              <label class="form-check-label" for="condcrossreftableXsd">Applicability conditions cross reference table</label><br/>
    
              <input class="form-check-input" type="checkbox" value="containerXsd" name="containerXsd" id="containerXsd" checked>
              <label class="form-check-label" for="containerXsd">Container</label><br/>
    
              <input class="form-check-input" type="checkbox" value="crewXsd" name="crewXsd" id="crewXsd" checked>
              <label class="form-check-label" for="crewXsd">Crew</label><br/>
    
              <input class="form-check-input" type="checkbox" value="ddnXsd" name="ddnXsd" id="ddnXsd" checked>
              <label class="form-check-label" for="ddnXsd">Data dispatch note</label><br/>
    
              <input class="form-check-input" type="checkbox" value="descriptXsd" name="descriptXsd" id="descriptXsd" checked>
              <label class="form-check-label" for="descriptXsd">Descriptive</label><br/>
    
              <input class="form-check-input" type="checkbox" value="dmlXsd" name="dmlXsd" id="dmlXsd" checked>
              <label class="form-check-label" for="dmlXsd">Data management list</label><br/>
    
              <input class="form-check-input" type="checkbox" value="faultXsd" name="faultXsd" id="faultXsd" checked>
              <label class="form-check-label" for="faultXsd">Fault</label><br/>
    
              <input class="form-check-input" type="checkbox" value="frontmatterXsd" name="frontmatterXsd" id="frontmatterXsd" checked>
              <label class="form-check-label" for="frontmatterXsd">Front matter</label><br/>
    
              <input class="form-check-input" type="checkbox" value="icnMetadataXsd" name="icnMetadataXsd" id="icnMetadataXsd" checked>
              <label class="form-check-label" for="icnMetadataXsd">ICN metadata</label><br/>
    
              <input class="form-check-input" type="checkbox" value="ipdXsd" name="ipdXsd" id="ipdXsd" checked>
              <label class="form-check-label" for="ipdXsd">IPD data module</label><br/>
    
              <input class="form-check-input" type="checkbox" value="learningXsd" name="learningXsd" id="learningXsd" checked>
              <label class="form-check-label" for="learningXsd">Learning data module</label><br/>
    
              <input class="form-check-input" type="checkbox" value="pmXsd" name="pmXsd" id="pmXsd" checked>
              <label class="form-check-label" for="pmXsd">Pulication module</label><br/>
    
              <input class="form-check-input" type="checkbox" value="prdcrossreftableXsd" name="prdcrossreftableXsd" id="prdcrossreftableXsd" checked>
              <label class="form-check-label" for="prdcrossreftableXsd">Product crossreference table data module</label><br/>
    
              <input class="form-check-input" type="checkbox" value="procedXsd" name="procedXsd" id="procedXsd" checked>
              <label class="form-check-label" for="procedXsd">Procedural data module</label><br/>
    
              <input class="form-check-input" type="checkbox" value="processXsd" name="processXsd" id="processXsd" checked>
              <label class="form-check-label" for="processXsd">Process data module</label><br/>
    
              <input class="form-check-input" type="checkbox" value="sbXsd" name="sbXsd" id="sbXsd" checked>
              <label class="form-check-label" for="sbXsd">Service bulletin data module</label><br/>
    
              <input class="form-check-input" type="checkbox" value="schedulXsd" name="schedulXsd" id="schedulXsd" checked>
              <label class="form-check-label" for="schedulXsd">Maintenance planning data module</label><br/>
    
              <input class="form-check-input" type="checkbox" value="scocontentXsd" name="scocontentXsd" id="scocontentXsd" checked>
              <label class="form-check-label" for="scocontentXsd">SCO content data module</label><br/>
    
              <input class="form-check-input" type="checkbox" value="scormcontentpackageXsd" name="scormcontentpackageXsd" id="scormcontentpackageXsd" checked>
              <label class="form-check-label" for="scormcontentpackageXsd">SCORM</label><br/>
    
              <input class="form-check-input" type="checkbox" value="updateXsd" name="updateXsd" id="updateXsd" checked>
              <label class="form-check-label" for="updateXsd">Update file Schema</label><br/>
    
              <input class="form-check-input" type="checkbox" value="wrngdataXsd" name="wrngdataXsd" id="wrngdataXsd" checked>
              <label class="form-check-label" for="wrngdataXsd">Wiring data module</label><br/>
    
              <input class="form-check-input" type="checkbox" value="wrngfldsXsd" name="wrngfldsXsd" id="wrngfldsXsd" checked>
              <label class="form-check-label" for="wrngfldsXsd">Wiring fields data module</label><br/>         
            </div>
            <!-- end check box -->
          </div>
          <div class="modal-footer">
            <button type="button" class="button-violet" data-bs-dismiss="modal">Set</button>
          </div>
        </div>
      </div>
    </div>

  </div>

</template>