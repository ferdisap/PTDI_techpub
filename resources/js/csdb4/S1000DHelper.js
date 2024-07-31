import jp from 'jsonpath';

// code
function resolve_pmCode(pmCode, prefix = 'PMC-') {
  if(!pmCode) return '';
  let modelIdentCode; modelIdentCode = (modelIdentCode = pmCode.find(v => v['@modelIdentCode'])) ? modelIdentCode['@modelIdentCode'] : '';
  let pmIssuer; pmIssuer = (pmIssuer = pmCode.find(v => v['@pmIssuer'])) ? pmIssuer['@pmIssuer'] : '';
  let pmNumber; pmNumber = (pmNumber = pmCode.find(v => v['@pmNumber'])) ? pmNumber['@pmNumber'] : '';
  let pmVolume; pmVolume = (pmVolume = pmCode.find(v => v['@pmVolume'])) ? pmVolume['@pmVolume'] : '';
  return prefix+modelIdentCode+'-'+pmIssuer+'-'+pmNumber+'-'+pmVolume;
}
function resolve_dmCode(dmCode, prefix = 'DMC-'){
  if(!dmCode) return '';
  let modelIdentCode; modelIdentCode = (modelIdentCode = dmCode.find(v => v['@modelIdentCode'])) ? modelIdentCode['@modelIdentCode'] : '';
  let systemDiffCode; systemDiffCode = (systemDiffCode = dmCode.find(v => v['@systemDiffCode'])) ? systemDiffCode['@systemDiffCode'] : '';
  let systemCode; systemCode = (systemCode = dmCode.find(v => v['@systemCode'])) ? systemCode['@systemCode'] : '';
  let subSystemCode; subSystemCode = (subSystemCode = dmCode.find(v => v['@subSystemCode'])) ? subSystemCode['@subSystemCode'] : '';
  let subSubSystemCode; subSubSystemCode = (subSubSystemCode = dmCode.find(v => v['@subSubSystemCode'])) ? subSubSystemCode['@subSubSystemCode'] : '';
  let assyCode; assyCode = (assyCode = dmCode.find(v => v['@assyCode'])) ? assyCode['@assyCode'] : '';
  let disassyCode; disassyCode = (disassyCode = dmCode.find(v => v['@disassyCode'])) ? disassyCode['@disassyCode'] : '';
  let disassyCodeVariant; disassyCodeVariant = (disassyCodeVariant = dmCode.find(v => v['@disassyCodeVariant'])) ? disassyCodeVariant['@disassyCodeVariant'] : '';
  let infoCode; infoCode = (infoCode = dmCode.find(v => v['@infoCode'])) ? infoCode['@infoCode'] : '';
  let infoCodeVariant; infoCodeVariant = (infoCodeVariant = dmCode.find(v => v['@infoCodeVariant'])) ? infoCodeVariant['@infoCodeVariant'] : '';
  let itemLocationCode; itemLocationCode = (itemLocationCode = dmCode.find(v => v['@itemLocationCode'])) ? itemLocationCode['@itemLocationCode'] : '';
  return prefix +
  modelIdentCode + "-" + systemDiffCode + "-" +
  systemCode + "-" + subSystemCode + subSubSystemCode + "-" +
  assyCode + "-" + disassyCode + disassyCodeVariant + "-" +
  infoCode + infoCodeVariant + "-" + itemLocationCode;
}
function resolve_dmlCode(dmlCode, prefix = 'DML-'){
  if(!dmlCode) return '';
  let dmlType; dmlType = (dmlType = dmlCode.find(v => v['@dmlType'])) ? dmlType['@dmlType'] : '';
  let modelIdentCode; modelIdentCode = (modelIdentCode = dmlCode.find(v => v['@modelIdentCode'])) ? modelIdentCode['@modelIdentCode'] : '';
  let senderIdent; senderIdent = (senderIdent = dmlCode.find(v => v['@senderIdent'])) ? senderIdent['@senderIdent'] : '';
  let seqNumber; seqNumber = (seqNumber = dmlCode.find(v => v['@seqNumber'])) ? seqNumber['@seqNumber'] : '';
  let yearOfDataIssue; yearOfDataIssue = (yearOfDataIssue = dmlCode.find(v => v['@yearOfDataIssue'])) ? yearOfDataIssue['@yearOfDataIssue'] : '';
  return prefix + modelIdentCode + "-" + senderIdent + "-" + dmlType + "-" + yearOfDataIssue + "-" + seqNumber;
}
function resolve_commentCode(commentCode, prefix = 'COM-'){
  if(!commentCode) return '';
  let modelIdentCode; modelIdentCode = (modelIdentCode = commentCode.find(v => v['@modelIdentCode'])) ? modelIdentCode['@modelIdentCode'] : '';
  let senderIdent; senderIdent = (senderIdent = commentCode.find(v => v['@senderIdent'])) ? senderIdent['@senderIdent'] : '';
  let yearOfDataIssue; yearOfDataIssue = (yearOfDataIssue = commentCode.find(v => v['@yearOfDataIssue'])) ? yearOfDataIssue['@yearOfDataIssue'] : '';
  let seqNumber; seqNumber = (seqNumber = commentCode.find(v => v['@seqNumber'])) ? seqNumber['@seqNumber'] : '';
  let commentType; commentType = (commentType = commentCode.find(v => v['@commentType'])) ? commentType['@commentType'] : '';
  return prefix + modelIdentCode + "-" + senderIdent + "-" + commentType + "-" + yearOfDataIssue + "-" + seqNumber;
}

// ident
function resolve_dmlIdent(dmlIdent, prefix = "DML-", format = ".xml"){
  if(!dmlIdent) return '';
  const dmlCode = resolve_dmlCode(jp.query(dmlIdent,'$..dmlCode')[0],prefix);
  const issueInfo = resolve_issueInfo(jp.query(dmlIdent,'$..issueInfo')[0]);
  return (dmlCode + (issueInfo ? "_"+issueInfo : '')).toUpperCase()+format;
}
function resolve_dmIdent(dmIdent, prefix = "DMC-", format = ".xml"){
  if(!dmIdent) return '';
  const dmCode = resolve_dmCode(jp.query(dmIdent,'$..dmCode')[0],prefix);
  const language = resolve_language(jp.query(dmIdent,'$..language')[0]);
  const issueInfo = resolve_issueInfo(jp.query(dmIdent,'$..issueInfo')[0]);
  return (dmCode + (issueInfo ? "_"+issueInfo + (language ? "_"+language : ''): '')).toUpperCase()+format;
}
function resolve_pmIdent(pmIdent, prefix = "PMC-", format = ".xml"){
  if(!pmIdent) return '';
  const pmCode = resolve_pmCode(jp.query(pmIdent,'$..pmCode')[0],prefix);
  const language = resolve_language(jp.query(pmIdent,'$..language')[0]);
  const issueInfo = resolve_issueInfo(jp.query(pmIdent,'$..issueInfo')[0]);
  return (pmCode + (issueInfo ? "_"+issueInfo + (language ? "_"+language : ''): '')).toUpperCase()+format;
}
function resolve_commentIdent(commentIdent, prefix = "COM-", format = ".xml"){
  if(!commentIdent) return '';
  const commentCode = resolve_commentCode(jp.query(commentIdent,'$..commentCode')[0],prefix);
  const language = resolve_language(jp.query(commentIdent,'$..language')[0]);
  return (commentCode + (language ? "_"+language : '')).toUpperCase()+format;
}
function resolve_infoEntityIdent(infoEntityIdent, prefix = "", format = ""){
  if(!infoEntityIdent) return '';
  let infoEntityRefIdent; infoEntityRefIdent = (infoEntityRefIdent = issueInfo.find(v => v['@infoEntityRefIdent'])) ? infoEntityRefIdent['@infoEntityRefIdent'] : '';
  if(prefix) infoEntityRefIdent = infoEntityRefIdent.replace('ICN-',prefix);
  if(format) infoEntityRefIdent = infoEntityRefIdent.replace(/\.\w+$/,format);
  return infoEntityIdent;
}



function resolve_language(language){
  if(!language) return '';
  let languageIsoCode; languageIsoCode = (languageIsoCode = language.find(v => v['@languageIsoCode'])) ? languageIsoCode['@languageIsoCode'] : '';
  let countryIsoCode; countryIsoCode = (countryIsoCode = language.find(v => v['@countryIsoCode'])) ? countryIsoCode['@countryIsoCode'] : '';
  return languageIsoCode +'-'+countryIsoCode;
}

function resolve_issueInfo(issueInfo){
  if(!issueInfo) return '';
  let issueNumber; issueNumber = (issueNumber = issueInfo.find(v => v['@issueNumber'])) ? issueNumber['@issueNumber'] : '';
  let inWork; inWork = (inWork = issueInfo.find(v => v['@inWork'])) ? inWork['@inWork'] : '';
  return issueNumber + '-' + inWork;
}

function resolve_issueDate(issueDate){
  if(!issueDate) return '';
  let year; year = (year = issueDate.find(v => v['@year'])) ? year['@year'] : '';
  let month; month = (month = issueDate.find(v => v['@month'])) ? month['@month'] : '';
  let day; day = (day = issueDate.find(v => v['@day'])) ? day['@day'] : '';
  return (new Date(year,month,day)).toDateString()
}



export {
  resolve_dmIdent, resolve_dmlIdent, resolve_pmIdent, resolve_commentIdent, resolve_infoEntityIdent,
  resolve_pmCode, resolve_dmCode, resolve_dmlCode, resolve_commentCode,
   resolve_issueInfo, resolve_issueDate, resolve_language};