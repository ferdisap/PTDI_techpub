import jp from 'jsonpath';
import Randomstring from 'randomstring';
import { findAncestor } from '../../helper';
import {
  fetchJsonFile,
  resolve_dmIdent, resolve_dmlIdent, resolve_pmIdent, resolve_commentIdent, resolve_infoEntityIdent,
  resolve_pmCode, resolve_dmCode, resolve_dmlCode, resolve_commentCode,
  resolve_issueInfo, resolve_issueDate, resolve_language
} from '../../S1000DHelper';

function inWork(json) {
  return json[1]['dml'][1]['identAndStatusSection'][0]['dmlAddress'][0]['dmlIdent'][1]['issueInfo'][0]['at_inWork'];
}
function issueDate(json) {
  let iss = jp.query(json, '$..dmlAddressItems..issueDate')[0];
  return iss.find(v => v['at_day'])['at_day'] + "/" +
    iss.find(v => v['at_month'])['at_month'] + "/" +
    iss.find(v => v['at_year'])['at_year'];
}
function securityClassification(json, path) {
  let sc;
  return (sc =
    ((sc = jp.query(json, path)[0]) ? sc.find(v => v['at_securityClassification']) : '')
  ) ? sc['at_securityClassification'] : '';
}
function remarks(json, path) {
  path += '..simplePara';
  let remarks = [];
  try {    
    jp.query(json, path).forEach(v => remarks.push(v[0]));
  } catch (error) {
    console.error('cannot resolve remarks element.');
  }
  return remarks;
}
function dmlContent(json) {
  return jp.query(json, '$..dmlContent')[0]
}

function getAttributeValue(json, pathToElemen, attributeName) {
  let attrValue; (attrValue =
    ((attrValue = jp.query(json, pathToElemen)[0]) ? (
      ((attrValue = attrValue.find(v => v['at_' + attributeName])) ? attrValue['at_' + attributeName] : '')
    ) : '')
  )
  return attrValue;
};

// dmlEntry
function defaultTemplateEntry(entry = {}, trId = '', cbValue = '', no = '') {
  let cbId = Randomstring.generate({ charset: 'alphabetic' });
  if (!cbValue) cbValue = entry.ident ? entry.ident : '';
  const answer = entry.answer ? entry.answer.join("<br/>") : '';
  const rm = entry.remarks ? entry.remarks.join("<br/>") : '';
  let str = `
      <tr cb-room class="dmlEntry hover:bg-blue-300 cursor-pointer" ${trId ? 'id="' + trId + '"' : ''}>
        <td modal-input-ref="no">${no}</td>
        <td cb-window>
          <input file="true" id="${cbId}" value="${cbValue}" type="checkbox">
        </td>
        <td modal-input-ref="entryIdent" class="dmlEntry-ident">${entry.ident ?? ''}</td>
        <td>
          <span modal-input-ref="dmlEntryType" class="text-sm">${entry.dmlEntryType ?? ''}</span>
          <span modal-input-ref="issueType" class="text-sm" >${entry.issueType ?? ''}</span>
        </td>
        <td modal-input-ref="securityClassification">${entry.securityClassification ?? ''}</td>
        <td>
          <span modal-input-ref="enterpriseName" class="text-sm">${entry.enterpriseName ?? ''}</span>   
          <span modal-input-ref="enterpriseCode" class="text-sm italic">${entry.enterpriseCode ?? ''}</span></td>
        <td>
          <span modal-input-ref="answerToEntry" style="display:none">${entry.answerToEntry}</span>
          <div modal-input-ref="answer[]">${answer}</div>
        </td>
        <td modal-input-ref="remarks[]">${rm}</td>
      </tr>`;
  return str.replace(/\s{2,}/g, '');
  // <td modal-input-ref="remarks[]">aaa<br/>bbbb</td>
}
function createEntryData(DMLObject = {}) {
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
      answer: entry.answer,
      answerToEntry: entry.answerToEntry,
      remarks: entry.remarks,
    };
  })
  return entryData;
}
function createEntryVueTemplate(entryData = {}) {
  let entryTemplate = '';
  Object.keys(entryData).forEach((trId, no) => {
    entryTemplate += defaultTemplateEntry(entryData[trId], trId, '', no + 1);
  })
  return entryTemplate;
}
// function addEntry(){
//   // this.entriesString = window.str;return;
//   const data = fetchDataFromRenderedEntries();
//   let str = '';
//   data.forEach(v => {
//     str += defaultTemplateEntry(v);
//   });
//   str += defaultTemplateEntry({});
//   this.entriesString = str;
//   // console.log(str);
//   return str;
//   // console.log(this.entriesString);
//   // console.log(window.str = str);
// }
function fetchDataFromRenderedEntries() {
  const data = [];
  $('.dmlEntry').each((i, v) => {
    data[i] = {};
    v = $(v);
    data[i].ident = v.find("*[name='entryIdent[]']").val();
    data[i].issueType = v.find("*[name='issueType[]']").val();
    data[i].dmlEntryType = v.find("*[name='dmlEntryType[]']").val();
    data[i].securityClassification = v.find("*[name='securityClassification[]']").val();
    data[i].enterpriseName = v.find("*[name='enterpriseName[]']").val();
    data[i].enterpriseCode = v.find("*[name='enterpriseCode[]']").val();
    data[i].remarks = [];
    $(v).find('.remarks textarea').each((n, v) => {
      if (v.value) data[i].remarks.push(v.value)
    });
  });
  return data;
}

async function showDMLContent(filename) {
  this.showLoadingProgress = true;
  const response = await fetchJsonFile({ filename: filename });
  // let response = await axios({
  //   route: {
  //     name: 'api.read_json',
  //     data: { filename: filename }
  //   },
  //   useMainLoadingBar: false,
  // });
  if (response.statusText === 'OK') {
    // handle or arrange json file
    this.DMLObject = DML(response.data.json);

    // create entries string
    // this.dmlEntryData = createEntryData(this.DMLObject);
    // this.dmlEntryVueTemplate = createEntryVueTemplate(this.dmlEntryData);
    const dmlEntryData = createEntryData(this.DMLObject);
    this.dmlEntryVueTemplate = createEntryVueTemplate(dmlEntryData);

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
    issueNumber: jp.query(json, '$..dmlIdent..issueInfo')[0].find(v => v['at_issueNumber'])['at_issueNumber'].toUpperCase(),
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
              if (ident = dmlEntry.find(v => v['at_infoEntityRefIdent'])) {
                ident = resolve_infoEntityIdent(ident['at_infoEntityRefIdent']);
              }

      let issueType; issueType = (issueType = dmlEntry.find(v => v['at_issueType'])) ? issueType['at_issueType'] : '';
      let entryType; entryType = (entryType = dmlEntry.find(v => v['at_entryType'])) ? entryType['at_entryType'] : '';

      const enterpriseName = jp.query(dmlEntry, '$..responsiblePartnerCompany..enterpriseName')[0][0];
      const enterpriseCode = getAttributeValue(dmlEntry, '$..responsiblePartnerCompany', 'enterpriseCode');

      const answerToEntry = getAttributeValue(dmlEntry, '$..answer', 'answerToEntry');
      return {
        ident: ident,
        issueType: issueType,
        dmlEntryType: entryType,
        securityClassification: securityClassification(dmlEntry, '$..security'),
        enterpriseName: enterpriseName,
        enterpriseCode: enterpriseCode,
        answer: remarks(dmlEntry, '$..answer..remarks'),
        answerToEntry: answerToEntry,
        // karena element <remarks> ada di dalam answer dan dmlEntry maka tidak bisa kasi path langsung seperti '$..remarks.simplePara' karena akan mengambil remarks parennya answer juga, padahal pengennya remarks yang parentnya dmlEntry
        remarks: remarks(dmlEntry.find(v => v['remarks']), '$..remarks'), 
      }
    }
  }
}

async function addEntry(next = true, duplicate = false) {

  // let clonned; let etarget;
  // etarget = document.querySelector(`#${this.cbId} tbody`);
  // const template = document.createElement('template');
  // template.innerHTML = defaultTemplateEntry();
  // clonned = template.content.cloneNode(true);
  // etarget.appendChild(clonned);
  // console.log(clonned);
  // return;

  // create cloning element
  let etarget = this.ContextMenu.triggerTarget;
  let clonned;
  // jika saat contextmenu di klik maka akan cari yang ada cb-roomnya, lalu di clone
  if (etarget = findAncestor(etarget, "*[cb-room]")) {
    clonned = etarget.cloneNode(true);
    clonned.id = Randomstring.generate({ charset: 'alphabetic' });
    if (!duplicate) {
      clonned.querySelectorAll("*[modal-input-ref]").forEach(input => {
        input.innerHTML = '';
      });
    }

    const id = clonned.getAttribute("use-in-modal");
    const modal = this.Modal.start(clonned, id, { manualUnset: true });

    // wait the replace
    if (!(await this.Modal.replace(await modal.data, id))) clonned = false;

    // append clonned into DOM
    if (clonned) {
      next ? etarget.after(clonned) : etarget.before(clonned);
      this.CB.setCbRoomId(null, clonned, null);
    }
  } else {
    const template = document.createElement('tbody');
    template.innerHTML = defaultTemplateEntry();
    clonned = template.firstElementChild.cloneNode(true);
    clonned.setAttribute('use-in-modal', this.dmlEntryModalId);
    
    const modal = this.Modal.start(clonned, this.dmlEntryModalId, { manualUnset: true });
    
    // wait the replace
    if (!(await this.Modal.replace(await modal.data, this.dmlEntryModalId)))  clonned = false;

    // append clonned into DOM
    if (clonned) {
      document.querySelector(`#${this.cbId} tbody`).appendChild(clonned);
      this.CB.setCbRoomId(null, clonned, null);
    }
  }



  // unset the Modal collection;
  this.Modal.unsetCollection(2);
  // else {  
  //   clonned = document.createElement('template');
  //   clonned.innerHTML = defaultTemplateEntry();
  //   clonned.addAttribute('use-in-modal', this.dmlEntryModalId);
  //   if(!etarget) etarget = document.querySelector(`#${this.cbId} tbody`);
  // }
}

export { showDMLContent, addEntry };