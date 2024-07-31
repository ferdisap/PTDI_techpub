<script>
import jp from 'jsonpath';
import { resolve_pmCode } from '../../S1000DHelper.js';
import { DML} from './DMLVue.js';

export default {
  data() {
    return {
      jp: jp,
      DMLObject: DML(this.$props.json),
    }
  },
  props: ['json'],
  methods: {
    resolve_pmCode: resolve_pmCode
  },
  mounted() {
    window.jp = jp;
    window.json = this.$props.json;
  }
}
</script>
<template>
  <form @submit.prevent>
    <div class="dmlIdentAndStatusSection">
      <div>Code: {{ DMLObject.code }}</div>
      <div>InWork: {{ DMLObject.inWork }}</div>
      <div>Issue: {{ DMLObject.issueNumber }}</div>
      <div>Date: {{ DMLObject.issueDate }}</div>
      <div>Security: <input type="text" name="ident-securityClassification" :value="DMLObject.securityClassification"/></div>
      <div>BREX:  <input name="ident-brexDmRef" :value="DMLObject.BREX"/> </div>
      <div>Remarks: <textarea v-for="para in DMLObject.remarks">{{ para }}</textarea></div>
    </div>
    <div>
      <table>
        <thead>
          <tr>
            <th>Ident</th>
            <th>Type</th>
            <th>Security</th>
            <th>Company</th>
            <th>Answer</th>
            <th>Remarks</th>
          </tr>
        </thead>
        <tbody>
          <tr class="dmlEntry" v-for="(v) in DMLObject.content">
            <!-- <td>{{ v }}</td> -->
            <!-- <td class="dmlEntry-ident">{{ resolve_pmCode(jp.query(v,"$..pmCode")[0]) }}</td> -->
            <td class="dmlEntry-ident">{{ (entry = DMLObject.getEntryData(v['dmlEntry'])).ident  }}</td>
            <td>{{ entry.issueType }}</td>
            <td>{{ entry.securityClassification }}</td>
            <td>{{ entry.enterpriseName }} {{ entry.enterpriseCode }}</td>
            <td>-</td>
            <td>
              <textarea v-for="remark in entry.remarks">{{ remark }}</textarea>
            </td>
            <!-- v[0]['dmlEntry'] adalah array berisi content dmlEntry  -->
          </tr>
        </tbody>
      </table>
    </div>
  </form>
</template>