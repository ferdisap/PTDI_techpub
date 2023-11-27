import { request } from './request?filename=./request.js';
// import { resolve_dmIdent } from './request?filename=./service/general.js';

function csdb_detail_test(param)
{
  alert(param ?? 'detail.js');
}

export function csdb_detail_render(conf)
{
  let url = `${window.location.origin}/csdb/object/CSDB`;
  let mime = conf.mime;
  
  if(mime == 'text/xml'){
    // let functions = ['title', 'resolve_issueDate', 'resolve_issueType', 'responsibleParnerCompany', 'originator', 'applicability', 'brexDmRef', 'qualityAssurance'];
    let functions = ['title', 'resolve_issueDate','resolve_issueType','resolve_responsibleParnerCompany', "resolve_originator", "getApplicability", 'resolve_brexDmRef', 'resolve_qualityAssurance'];
    
    let params = {};
    params.functions = functions;
    params.filename = conf.filename;
    params.mime = mime;

    let res = request(url, 'POST', params);

    
    functions.forEach((fn,k) => {
      if(fn == 'resolve_qualityAssurance'){
        let str = [];
        JSON.parse(res.return[fn]).forEach((index) => {
          let a = [index.applicRefId, index.status, index.verificationType].filter(n => n);
          str.push(a.join(" | "));
        });
        str = str.join(",<br/>");
        res.return.resolve_qualityAssurance = str;
      }
      document.getElementById(fn).innerHTML = res.return[fn];
    });

    return res;
  }



  // let xml = request(url, method, {filename: filename})
  // console.log(window.location.origin);
  // console.log(res);
}

function resolve_pmTitle(conf){
  
  let filename = conf.filename;
  
  let title = request(`${window.location.origin}/csdb/object/CSDB`,'POST', {
    function: "resolve_pmTitle",
    filename: filename,
  });
  return title.return
}

function resolve_issueDate(conf){

  let filename = conf.filename;
  
  let issueDate = request(`${window.location.origin}/csdb/object/CSDB`,'POST', {
    function: "resolve_issueDate",
    filename: filename,
  });
  return issueDate.return
}


