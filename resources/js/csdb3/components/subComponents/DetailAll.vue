<script>
import AnalyzeDML2 from './AnalyzeDML2.vue';
import DetailDML2 from './DetailDML2.vue';

export default {
  data() {
    return {
      model: {},
      // model: { "id": "01hq8g75frb9d7km5wrr5w73jt", "filename": "DML-MALE-0001Z-P-2024-00002_000-01.xml", "path": "csdb", "editable": 1, "remarks": { "securityClassification": "01", "ident": { "dmlCode": { "modelIdentCode": "MALE", "senderIdent": "0001Z", "dmlType": "p", "yearOfDataIssue": "2024", "seqNumber": "00002" }, "prefix": "DML-", "issueInfo": { "issueNumber": "000", "inWork": "01" } } }, "created_at": "2024-02-22T20:31:29.000000Z", "updated_at": "2024-02-22T20:31:29.000000Z", "initiator": { "name": "Luffy", "email": "luffy@example.com" } },
      type: undefined,
      imfmodel: {},
      transformed: '',

      /*
       * properti penunjang
       */
      showDetailDML: false
    };
  },
  components: { AnalyzeDML2, DetailDML2 },
  computed: {
    filename() {
      return this.model.filename;
    },
    path() {
      return "/" + this.model.path;
    },
    isDML() {
      if(this.type === 'dmrl' || this.type === 'csl'){
        this.setTransformed(this.filename);
        return true;
      } else {
        return false;
      }
    }
  },
  methods: {
    async getIMFModel() {
      return {};
    },
    async setTransformed(filename) {
      let response = await axios({
        route: {
          name: 'api.transform_csdb',
          data: { filename: filename, ignoreError: 1 },
        },
      });
      if (response.statusText === 'OK') {
        let text = response.data.file;
        text = text.replace("\\r", "\r");
        text = text.replace("\\n", "\n");
        this.transformed = text;
        this.settingTransformed();
      }
    },

    /*
     * digunakan untuk menyesuaikan transformed sesuai dengan type object
    */
    settingTransformed() {
      if (this.type === 'dml' || this.type === 'csl') {
        if (!this.model.editable) {
          setTimeout(() => {
            $('#dml *[name]').each((i, e) => {
              e.setAttribute('disabled', true);
              e.style.border = 'none'
            })
            $('.add-remove_button_container').remove()
          }, 0);
        }
      }
    }
  },
  mounted() {
    this.emitter.on('openfile', (data) => {
      this.model = {};
      this.type = undefined;
      this.imfmodel = {};
      this.transformed = '';
      let prefix = data.model.remarks.ident.prefix;
      if (prefix === 'DML-' || prefix === 'CSL-') {
        if (data.model.remarks.ident.dmlCode.dmlType === 'p' || data.model.remarks.ident.dmlCode.dmlType === 'c') {
          this.type = 'dmrl';
        }
        else {
          this.type = 'csl';
        }
      }
      else if (prefix === 'DMC-') {
        if (data.model.remarks.ident.dmCode.infoCode === '022') {
          this.type = 'brex';
        }
        else if (data.model.remarks.ident.dmCode.infoCode === '024') {
          this.type = 'brdoc';
        }
        else {
          this.type = this.prefx;
        }
      }
      else if (prefix === 'ICN-') {
        this.type = prefix;
        this.getIMFModel();
      }
      else if (prefix === 'IMF-') {
        this.type = prefix;
      }
      this.model = data.model;
    });
  },
}
</script>
<template>
  <div class="detail-all overflow-auto mb-5">
    <div class="bg-blue-500 py-3 px-2 text-white mb-3 border rounded-t-xl text-center">
      <span class="text-2xl">{{ filename }}</span>
      <br />
      <span class="text-sm">{{ path }}</span>
    </div>

    <!-- DML -->
    <div v-if="isDML">
      <AnalyzeDML2 :filename="model.filename" />
      <button class="underline text-blue-500" @click="showDetailDML = !showDetailDML">Show Detail View</button>
      &nbsp;
      <a href="#" class="underline text-blue-500">Open Detail in New Window</a>
      <DetailDML2 v-if="showDetailDML" :transformed="transformed" :model="model" />
    </div>
  </div>
</template>