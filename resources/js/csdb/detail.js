// import { request } from './request?filename=./request.js';
import { request } from "./request.js";


// import { resolve_dmIdent } from './request?filename=./service/general.js';


// let xml = request(`${window.location.origin}/csdb/object/request`,'GET',{filename:'PMC-MALE-0001Z-A0001-00_000-03_EN-EN.xml'});
// console.log(xml);

function csdb_detail_test(param)
{
  alert(param ?? 'detail.js');
}

// csdb_detail_test();
// window.csdb_detail_test = csdb_detail_test;


export function csdb_detail_render(conf){
  let csdb_transformed = request(window.route.get_transform_csdb,'GET',conf);
  console.log(csdb_transformed);
}

export function csdb_detail_render_tidakDipakai(conf){
  let csdb_object = request(window.route.get_request_csdb_object, 'GET', conf);
  let type = csdb_object.firstElementChild.nodeName;
  let xsl = request(window.route.get_request_csdb_xsl, 'GET', {filename: `csdb_detail_${type}.xsl`});
  
  const xsltProcessor = new XSLTProcessor();
  xsltProcessor.importStylesheet(xsl);
  xsltProcessor.setParameter(null, 'filename', conf.filename);
  // xsltProcessor.setParameter(null, 'get_request_csdb_xsl', window.get_request_csdb_xsl);
  let detail = xsltProcessor.transformToFragment(csdb_object,document);

  let container = document.getElementById(conf.id);
  container.innerHTML = '';
  container.appendChild(detail);
  // console.log(container);
}


export function csdb_detail_render_tidakDIpakai(conf)
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


