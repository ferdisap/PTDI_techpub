<script>
import $ from 'jquery'
import { useTechpubStore } from '../../../techpub/techpubStore';

const dmlEntryRowTemplate = `<tr class="dmlEntry"><td class="dmlEntry-ident"><textarea name="entryIdent[]" class="w-full"></textarea><div class="text-red-600 text-sm error"></div></td><td><input class="dmlEntry-dmlEntryType w-2/5" name="dmlEntryType[]"> | <input class="dmlEntry-issueType w-2/5" name="issueType[]"><div class="text-red-600 text-sm error"></div></td><td><input class="dmlEntry-securityClassification w-full" name="securityClassification[]"><div class="text-red-600 text-sm error"></div></td><td class="responsibleCompany"><input class="dmlEntry-enterpriseName w-2/5" name="enterpriseName[]"> | <input class="dmlEntry-enterpriseCode w-2/5" name="enterpriseCode[]"><div class="text-red-600 text-sm error"></div></td><td>-</td><td><textarea name="remarks[]" class="w-full"></textarea><div class="text-red-600 text-sm error"></div></td></tr>`;
const commentContentRowTemplate = function(ident,type, issue_type,sc,rsc, rsc_code,ans,remarks){
  return `<tr class="dmlEntry">
    <td class="dmlEntry-ident"><textarea name="entryIdent[]" class="w-full"></textarea>
      <div class="text-red-600 text-sm error">${ident}</div>
    </td>
    <td>
      <input class="dmlEntry-dmlEntryType w-2/5" name="dmlEntryType[]" value="${type}"> | <input class="dmlEntry-issueType w-2/5" name="issueType[]" value="${issue_type}">
      <div class="text-red-600 text-sm error"></div>
    </td>
    <td>
      <input class="dmlEntry-securityClassification w-full" name="securityClassification[]" value="${sc}">
      <div class="text-red-600 text-sm error"></div>
    </td>
    <td class="responsibleCompany">
      <input class="dmlEntry-enterpriseName w-2/5" name="enterpriseName[]" value="${rsc}"> | <input class="dmlEntry-enterpriseCode w-2/5" name="enterpriseCode[]" value="${rsc_code}">
      <div class="text-red-600 text-sm error"></div>
    </td>
    <td>-</td>
    <td>
      <textarea name="remarks[]" class="w-full">${remarks}</textarea>
      <div class="text-red-600 text-sm error"></div>
    </td>
  </tr>`;
}
// `<tr class="commentContent">
//   <td colspan="6">
//     <textarea name="commentContent[]" commentIdent="foo" class="w-full"/>
//   </td></tr>`;

export default {
  data() {
    return {
      dmlEntryRows: '',
      // dmlEntryRowTemplate: '',
      dmlEntryRowTemplate: dmlEntryRowTemplate,
      commentContentRowTemplate: commentContentRowTemplate,
      store: useTechpubStore(),
    }
  },
  computed: {
    dynamic() {
      return {
        template: this.dmlEntryRows,
        data() {
          return {
            store: useTechpubStore(),
          }
        },
      }
    }
  },
  methods: {
    /** 
     * akan mengubah menambah v-html dan tampil di window.
     * index= [0 => 'foo'] atau [0 => 0]
     * @return jquery element
     */
    setVHtml(entries, index = []) {
      entries.each((no, dmlEntry) => {
        $(dmlEntry).find('.error').each((i, e) => {
          let names = [];
          $(e).parent('td').find('*[name]').each((i, input) => {
            if(index[no]){
              no = index[no];
            }
            names.push(`'${input.name.replace('[]', '')}.${no}'`);
          });
          $(e).attr('v-html', `store.error(${names.join(',')})`);
        })
      });
      return entries;
    },
    add_dmlEntry() {
      // dikasi timeout agar proses sebelumnya (misal sort) masih berjalan
      setTimeout(()=>{
        let dmlEntryRows = this.arrange_dmlEntry();
  
        // untuk input tambahan terakhir, semua value dikosongkan
        let str;
        if($('.dmlEntry').length){
          str = this.dmlEntryRowTemplate;
          str = this.setVHtml($(str),[$(dmlEntryRows).length])[0].outerHTML;
        } else {
          str = '';
        }
  
        if(this.dmlEntryRows == dmlEntryRows + str){
          this.dmlEntryRows = '';
          this.dmlEntryRows = dmlEntryRows + str
        } else {
          this.dmlEntryRows = dmlEntryRows + str
        }
      },100);
    },
    add_dmlEntry_commentRef(data){
      let template = this.commentContentRowTemplate(
        data['ident'], data['type'], data['issue_type'], data['securityClassification'], data['responsibleCompany'], data['responsibleCompany_code'], data['answer'], data['remarks']
      );
      this.dmlEntryRows += template;

    },
    remove_dmlEntry() {
      let dmlEntry = $('.dmlEntry');
      if(dmlEntry.length != 1){
        dmlEntry.last().remove();
      }
    },
    /**
     * @return String dmlEntries
     */
    arrange_dmlEntry(){
      let dmlEntryRows = '';
      let str = ''
      let dmlEntries = $('.dmlEntry');
      if (!dmlEntries.length) {
        dmlEntries = $(this.dmlEntryRowTemplate);
      }
      
      // tambah attribute v-html
      // dmlEntries = this.setVHtml(dmlEntries);
      dmlEntries = this.setVHtml(dmlEntries);

      // karena jika dijadikan outerHTML tidak ada valuenya, jadi ini menambah value input
      dmlEntries.each((i, dmlEntry) => {
        str = dmlEntry.outerHTML;
        $(dmlEntry).find('*[name]').each((ii, input) => {
          let value = $(input).val();
          let name = input.name;
          if (input.nodeName != 'TEXTAREA') {
            str = str.replace(`name="${name}"`, `name="${name}" value="${value}"`);
          } else {
            // bila TEXTAREA maka kalau tidak dilakukan ini, inputan/editan user akan kembali seperti awal (seperti tidak di edit/input);
            const regex = new RegExp(`(<textarea[\\s\\S]*?name="${name.replace('[]', '\\[]')}"[\\s\\S]*?>)[\\s\\S]*?(<\\\/textarea>)`, '');
            str = str.replace(regex,`$1${value}$2`);
          }
        });
        dmlEntryRows = dmlEntryRows + str;
      });

      return dmlEntryRows;
    },
  },
  mounted() {
    this.emitter.on('add_dmlEntry', this.add_dmlEntry); // trigger ada di DetailDML.vue
    this.emitter.on('remove_dmlEntry', this.remove_dmlEntry); // trigger ada di DetailDML.vue
    this.emitter.on('dmlEntry_sorted', () => {
      let arranged = this.arrange_dmlEntry();
      if(this.dmlEntryRows != arranged){
        $('.dmlEntry').remove();
      }
      this.dmlEntryRows = arranged;
    }); // trigger ada di DetailDML.vue
    this.emitter.on('add_dmlEntry_commentRef', (data) => {
      this.add_dmlEntry_commentRef(data);
    });
  },
}
</script>
<style>
.dmlEntry{
  vertical-align: top;
}
</style>
<template>
  <component v-if="dmlEntryRows" :is="dynamic" />
  <slot v-else />
</template>