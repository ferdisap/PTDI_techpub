/**
 * ini akan di import oleh EditorDML.vue
 */

import { useTechpubStore } from '../../../techpub/techpubStore';
import DmlEntryForm from '../subComponents/DmlEntryForm.vue';
import Sort from '../../../techpub/components/Sort.vue';
import SearchDialog from './SearchDialog.vue';

const DynamicDML = function(){
  return {
    template: this.transformed,
    components: {DmlEntryForm, Sort, SearchDialog},
    data(){
      return {
        store: useTechpubStore(),
        showDialog: false,
        dialogCallback: () => {}
      }
    },
    methods:{
      turnOnSearchDialog(callback, state = true){
        if(callback) this.dialogCallback = callback;
        this.showDialog = state;
      }
      // sort() {
      //   const getCellValue = function (row, index) {
      //     return $(row).children('td').eq(index).text();
      //   };
      //   const comparer = function (index) {
      //     return function (a, b) {
      //       let valA = getCellValue(a, index), valB = getCellValue(b, index);
      //       return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB);
      //     }
      //   };
      //   let table = $(event.target).parents('table').eq(0);
      //   let th = $(event.target).parents('th').eq(0);
      //   let rows = table.find('tr:gt(0)').toArray().sort(comparer(th.index()));
      //   this.asc = !this.asc;
      //   if (!this.asc) {
      //     rows = rows.reverse();
      //   }
      //   for (let i = 0; i < rows.length; i++) {
      //     table.append(rows[i]);
      //   }
      // },
      
    }
  }
}

export default DynamicDML;