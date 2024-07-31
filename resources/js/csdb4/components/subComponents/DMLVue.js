import jp from 'jsonpath';
import {
  resolve_dmIdent, resolve_dmlIdent, resolve_pmIdent, resolve_commentIdent, resolve_infoEntityIdent,
  resolve_pmCode, resolve_dmCode, resolve_dmlCode, resolve_commentCode,
   resolve_issueInfo, resolve_issueDate, resolve_language} from '../../S1000DHelper'; 

function inWork(json) {
  return json[1]['dml'][1]['identAndStatusSection'][0]['dmlAddress'][0]['dmlIdent'][1]['issueInfo'][0]['@inWork'];
}
function issueDate(json) {
  let iss = jp.query(json, '$..dmlAddressItems..issueDate')[0];
  return iss.find(v => v['@day'])['@day'] + "/" +
    iss.find(v => v['@month'])['@month'] + "/" +
    iss.find(v => v['@year'])['@year'];
}
function securityClassification(json,path) {
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

function getAttributeValue(json, pathToElemen, attributeName){
  let attrValue; (attrValue = 
    ((attrValue = jp.query(json, pathToElemen)[0]) ? (
        ((attrValue = attrValue.find(v => v['@'+attributeName])) ? attrValue['@'+attributeName] : '')
      ) : '')
    )
  return attrValue;
};

// hasilya sama
// pmRef1 = entry['dmlEntry'].find(v => v['pmRef'])['pmRef'];
// pmRef2 = jp.query(entry['dmlEntry'],'$..pmRef')[0]

const DML = function(json){
  return {
    code: resolve_dmlCode(jp.query(json, '$..identAndStatusSection..dmlAddress..dmlIdent..dmlCode')[0],'').toUpperCase(),
    inWork: inWork(json),
    issueNumber: jp.query(json, '$..dmlIdent..issueInfo')[0].find(v => v['@issueNumber'])['@issueNumber'].toUpperCase(),
    issueDate: resolve_issueDate(jp.query(json, '$..dmlAddressItems..issueDate')[0]),
    securityClassification: securityClassification(json,'$..dmlStatus..security'),
    BREX: resolve_dmIdent(jp.query(json, '$..dmlStatus..brexDmRef..dmRefIdent')[0]),
    remarks: remarks(json,'$..dmlStatus..remarks'),
    content: dmlContent(json),

    getEntryData(dmlEntry){
      let ident;
      if(ident = jp.query(dmlEntry,"$..pmRefIdent")[0]){
        ident = resolve_pmIdent(ident);
      } else
      if(ident = jp.query(dmlEntry,"$..dmRefIdent")[0]){
        ident = resolve_dmIdent(ident);
      } else
      if(ident = jp.query(dmlEntry,"$..dmlRefIdent")[0]){
        ident = resolve_dmlIdent(ident);
      } else
      if(ident = jp.query(dmlEntry,"$..commentRefIdent")[0]){
        ident = resolve_commentIdent(ident);
      } else
      if(ident = dmlEntry.find(v => v['@infoEntityRefIdent'])){
        ident = resolve_infoEntityIdent(ident['@infoEntityRefIdent']);
      }

      let issueType; issueType = (issueType = dmlEntry.find(v => v['@issueType'])) ? issueType['@issueType'] : '';

      let enterpriseName = jp.query(dmlEntry, '$..responsiblePartnerCompany..enterpriseName')[0][0];
      let enterpriseCode = getAttributeValue(dmlEntry, '$..responsiblePartnerCompany','enterpriseCode');     

      return {
        ident: ident,
        issueType: issueType,
        securityClassification: securityClassification(dmlEntry, '$..security'),
        enterpriseName: enterpriseName,
        enterpriseCode: enterpriseCode,
        remarks: remarks(dmlEntry,'$..remarks'),
      }
    }
  }
}

export {DML, getAttributeValue, inWork, issueDate, securityClassification, remarks, dmlContent };