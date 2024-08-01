import jp from 'jsonpath';
import Randomstring from 'randomstring';
import {
  resolve_dmIdent, resolve_dmlIdent, resolve_pmIdent, resolve_commentIdent, resolve_infoEntityIdent,
  resolve_pmCode, resolve_dmCode, resolve_dmlCode, resolve_commentCode,
  resolve_issueInfo, resolve_issueDate, resolve_language
} from '../../S1000DHelper';

function inWork(json) {
  return json[1]['dml'][1]['identAndStatusSection'][0]['dmlAddress'][0]['dmlIdent'][1]['issueInfo'][0]['@inWork'];
}
function issueDate(json) {
  let iss = jp.query(json, '$..dmlAddressItems..issueDate')[0];
  return iss.find(v => v['@day'])['@day'] + "/" +
    iss.find(v => v['@month'])['@month'] + "/" +
    iss.find(v => v['@year'])['@year'];
}
function securityClassification(json, path) {
  let sc;
  return (sc =
    ((sc = jp.query(json, path)[0]) ? sc.find(v => v['@securityClassification']) : '')
  ) ? sc['@securityClassification'] : '';
}
function remarks(json, path) {
  path += '..simplePara';
  let remarks = [];
  jp.query(json, path).forEach(v => remarks.push(v[0]));
  return remarks;
}
function dmlContent(json) {
  return jp.query(json, '$..dmlContent')[0]
}

function getAttributeValue(json, pathToElemen, attributeName) {
  let attrValue; (attrValue =
    ((attrValue = jp.query(json, pathToElemen)[0]) ? (
      ((attrValue = attrValue.find(v => v['@' + attributeName])) ? attrValue['@' + attributeName] : '')
    ) : '')
  )
  return attrValue;
};

// dmlEntry
function defaultTemplateEntry(entry = {}, cbValue) {
  // let trId = 'tr' + Randomstring.generate({ charset: 'alphabetic' });
  let cbId = 'cb' + Randomstring.generate({ charset: 'alphabetic' });
  if(!cbValue) cbValue = entry.ident ? entry.ident : '';
  let remarks = JSON.stringify(entry.remarks);
  // let remarks = entry.remarks;
  console.log(remarks, entry.remarks);
  let str = `
      <tr class="dmlEntry"
        v-on:click.stop.prevent="CbSelector.select('${cbId}')"
        v-on:mousemove.stop.prevent="CbSelector.setCbHovered('${cbId}')"
        >
        <td v-show="CbSelector.selectionMode">
          <input file="true" id="${cbId}" value="${cbValue}" type="checkbox">
        </td>
        <td class="dmlEntry-ident">
          <textarea name="entryIdent[]" class="w-full">${entry.ident}</textarea>
        </td>
        <td>
          <input class="dmlEntry-dmlEntryType w-2/5" name="dmlEntryType[]" value="${entry.dmlEntryType}"/> | <input class="dmlEntry-issueType w-2/5" name="issueType[]" value="${entry.issueType}"/>
        </td>
        <td>
          <input type="number" class="dmlEntry-securityClassification w-full" name="securityClassification[]" value="${entry.securityClassification}"/>
        </td>
        <td>
          <input class="dmlEntry-enterpriseName w-2/5" name="enterpriseName[]" value="${entry.enterpriseName}"/> | <input class="dmlEntry-enterpriseCode w-2/5" name="enterpriseCode[]" value="${entry.enterpriseCode}"/>
        </td>
        <td>-</td>
        <td>
          <component is="Remarks" class_label="hidden" para='${remarks}' /> 
        </td>
      </tr>`;
  window.s = str;
  return str;
}
function entriesStringFromProps() {
  const dmlContent = this.DMLObject.content;
  window.dmlContent = dmlContent;
  let str = '';
  dmlContent.forEach(v => {
    let entry = this.DMLObject.getEntryData(v['dmlEntry']);
    str += defaultTemplateEntry({
      ident: entry.ident,
      issueType: entry.issueType,
      dmlEntryType: entry.dmlEntryType,
      securityClassification: entry.securityClassification,
      enterpriseName: entry.enterpriseName,
      enterpriseCode: entry.enterpriseCode,
      remarks: entry.remarks,      
    });
  })
  return str;
}
function addEntry(){
  // this.entriesString = window.str;return;
  const data = fetchDataFromRenderedEntries();
  let str = '';
  data.forEach(v => {
    str += defaultTemplateEntry(v);
  });
  str += defaultTemplateEntry({});
  this.entriesString = str;
  // console.log(str);
  return str;
  // console.log(this.entriesString);
  // console.log(window.str = str);
}
function fetchDataFromRenderedEntries(){
  const data = [];
  $('.dmlEntry').each((i,v) => {
    data[i] = {};
    v = $(v);
    data[i].ident = v.find("*[name='entryIdent[]']").val();
    data[i].issueType = v.find("*[name='issueType[]']").val();
    data[i].dmlEntryType = v.find("*[name='dmlEntryType[]']").val();
    data[i].securityClassification = v.find("*[name='securityClassification[]']").val();
    data[i].enterpriseName = v.find("*[name='enterpriseName[]']").val();
    data[i].enterpriseCode = v.find("*[name='enterpriseCode[]']").val();
    data[i].remarks = [];
    $(v).find('.remarks textarea').each((n,v) => {
      if(v.value) data[i].remarks.push(v.value)
    });
  });
  return data;
}


// hasilya sama
// pmRef1 = entry['dmlEntry'].find(v => v['pmRef'])['pmRef'];
// pmRef2 = jp.query(entry['dmlEntry'],'$..pmRef')[0]

const DML = function (json) {
  return {
    code: resolve_dmlCode(jp.query(json, '$..identAndStatusSection..dmlAddress..dmlIdent..dmlCode')[0], '').toUpperCase(),
    inWork: inWork(json),
    issueNumber: jp.query(json, '$..dmlIdent..issueInfo')[0].find(v => v['@issueNumber'])['@issueNumber'].toUpperCase(),
    issueDate: resolve_issueDate(jp.query(json, '$..dmlAddressItems..issueDate')[0]),
    securityClassification: securityClassification(json, '$..dmlStatus..security'),
    BREX: resolve_dmIdent(jp.query(json, '$..dmlStatus..brexDmRef..dmRefIdent')[0]),
    remarks: remarks(json, '$..dmlStatus..remarks'),
    content: dmlContent(json),

    getEntryData(dmlEntry) {
      let ident;
      if (ident = jp.query(dmlEntry, "$..pmRefIdent")[0]) {
        ident = resolve_pmIdent(ident);
      } else
        if (ident = jp.query(dmlEntry, "$..dmRefIdent")[0]) {
          ident = resolve_dmIdent(ident);
        } else
          if (ident = jp.query(dmlEntry, "$..dmlRefIdent")[0]) {
            ident = resolve_dmlIdent(ident);
          } else
            if (ident = jp.query(dmlEntry, "$..commentRefIdent")[0]) {
              ident = resolve_commentIdent(ident);
            } else
              if (ident = dmlEntry.find(v => v['@infoEntityRefIdent'])) {
                ident = resolve_infoEntityIdent(ident['@infoEntityRefIdent']);
              }

      let issueType; issueType = (issueType = dmlEntry.find(v => v['@issueType'])) ? issueType['@issueType'] : '';
      let entryType; entryType = (entryType = dmlEntry.find(v => v['@entryType'])) ? entryType['@entryType'] : '';

      let enterpriseName = jp.query(dmlEntry, '$..responsiblePartnerCompany..enterpriseName')[0][0];
      let enterpriseCode = getAttributeValue(dmlEntry, '$..responsiblePartnerCompany', 'enterpriseCode');

      return {
        ident: ident,
        issueType: issueType,
        dmlEntryType: entryType,
        securityClassification: securityClassification(dmlEntry, '$..security'),
        enterpriseName: enterpriseName,
        enterpriseCode: enterpriseCode,
        remarks: remarks(dmlEntry, '$..remarks'),
      }
    }
  }
}

export {
  DML, entriesStringFromProps, defaultTemplateEntry, addEntry,
  getAttributeValue, inWork, issueDate, securityClassification, remarks, dmlContent
};