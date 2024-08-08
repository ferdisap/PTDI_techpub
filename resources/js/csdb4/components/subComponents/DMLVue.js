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
function defaultTemplateEntry(entry = {}, trId, cbValue, no) {
  let cbId = Randomstring.generate({ charset: 'alphabetic' });
  if(!cbValue) cbValue = entry.ident ? entry.ident : '';
  const rm = entry.remarks.join("<br/>");
  let str = `
      <tr cb-room class="dmlEntry hover:bg-blue-300 cursor-pointer" id="${trId}">
        <td modal-input-ref="no">${no}</td>
        <td cb-window>
          <input file="true" id="${cbId}" value="${cbValue}" type="checkbox">
        </td>
        <td modal-input-ref="entryIdent" class="dmlEntry-ident">${entry.ident}</td>
        <td>
          <span modal-input-ref="dmlEntryType" class="text-sm">${entry.dmlEntryType}</span>
          <span modal-input-ref="issueType" class="text-sm" >${entry.issueType}</span>
        </td>
        <td modal-input-ref="securityClassification">${entry.securityClassification}</td>
        <td>
          <span modal-input-ref="enterpriseName" class="text-sm">${entry.enterpriseName}</span>   
          <span modal-input-ref="enterpriseCode" class="text-sm italic">${entry.enterpriseCode}</span></td>
        <td>-</td>
        <td modal-input-ref="remarks[]">${rm}</td>
      </tr>`;
  return str.replace(/\s{2,}/g,'');
  // <td modal-input-ref="remarks[]">aaa<br/>bbbb</td>
}
function createEntryData(DMLObject = {}){
  let entryData = {};
  DMLObject.content.forEach(v => {
    let entry = DMLObject.getEntryData(v['dmlEntry']);
    let trId = Randomstring.generate({ charset: 'alphabetic' })
    entryData[trId] = {
      ident: entry.ident,
      issueType: entry.issueType,
      dmlEntryType: entry.dmlEntryType,
      securityClassification: entry.securityClassification,
      enterpriseName: entry.enterpriseName,
      enterpriseCode: entry.enterpriseCode,
      remarks: entry.remarks,
    };
  })
  return entryData;
}
function createEntryVueTemplate(entryData = {}) {
  let entryTemplate = '';
  Object.keys(entryData).forEach((trId,no) => {
    entryTemplate += defaultTemplateEntry(entryData[trId],trId,'',no+1);
  })
  return entryTemplate;
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

async function showDMLContent(filename){
  this.showLoadingProgress = true;
  let response = await axios({
    route: {
      name: 'api.read_json',
      data: {filename: filename}
    },
    useMainLoadingBar: false,
  });
  if(response.statusText === 'OK'){
    // handle or arrange json file
    this.DMLObject = DML(response.data.json)

    // create entries string
    this.dmlEntryData = createEntryData(this.DMLObject);
    this.dmlEntryVueTemplate = createEntryVueTemplate(this.dmlEntryData);

    this.techpubStore.currentObjectModel = response.data.model;
  }
  this.showLoadingProgress = false;
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

export { showDMLContent, addEntry};