console.log('brDoc');

const BrdpTable = {
  detailOpen: [],

  setURLHash(id){
    window.history.pushState({},"",window.location.origin + window.location.pathname + '#' + id);
  },
  goToHash(hash){
    window.location.hash = "";
    setTimeout(()=>{
      window.location.hash = hash;
    },0);
    console.log('done');
  },
  openDetail(brIdent,brDecisionId,trId,el){
    if(!this.detailOpen.includes(trId)){
      // open detail
      let tr = document.getElementById(trId + '_detail');
      if(tr == undefined){ // jika tr belum pernah dibuat
        tr = this.createDetailContainer(trId + '_detail');
        el.parentElement.parentElement.insertBefore(tr, el.parentElement.nextElementSibling ?? el.parentElement);
      } else {
        tr.style.display = '';
      }
      this.detailOpen.push(trId);

      // render brdp
      this.renderBrdp(brIdent, trId + '_detail', brDecisionId);
    } else {
      // close detail
      let tr = document.getElementById(trId + '_detail');
      tr.style.display = 'none';
      this.removeArrayCertainItem(trId);      
    }
  },
  closeDetailAll(){
    this.detailOpen.forEach(trId => {
      document.getElementById(trId + '_detail').style.display = 'none';
    });
    this.detailOpen = [];
  },
  createDetailContainer(id = null){
    let tr = document.createElement('tr');
    tr.setAttribute('id', id);
    tr.setAttribute('class', 'brdp_detail');
    tr.innerHTML = 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Laboriosam, velit?';
    return tr;
  },
  removeArrayCertainItem(item){
    let i = this.detailOpen.indexOf(item);
    if (i > -1){
      this.detailOpen.splice(i,1);
    }
  },
  renderBrdp(brIdent, containerId, brDecisionId){
    let brdp = new BrdpDetail(brIdent, brDecisionId)
    brdp.renderBrdptoContainer(containerId);
  },
};

BrdpTable.goToHash(window.location.hash);

class BrdpDetail {
  brIdent= null;
  brDecisionId = null;
  #xhr = new XMLHttpRequest();
  #parser = new DOMParser();
  #xsltProcessor = new XSLTProcessor();
  #xmlSerializer = new XMLSerializer();
  constructor(brIdent, brDecisionId){
    this.brIdent = brIdent;
    this.brDecisionId = brDecisionId;
  }
  #createXML(url){
    this.#xhr.open('GET', "/brdp?utility=getfile&path=" + url, false);
    this.#xhr.send(null);
    return this.#xhr.responseXML;
  }
  async #getBrdpXML(brIdent){
    let xmlRef = (BrdpTable.xmlRef != undefined ? BrdpTable.xmlRef : this.#createXML("brdp/dmodule/br/tes.xml", false));
    BrdpTable.xmlRef = xmlRef;
    
    let xPath = `//brPara[@brDecisionPointUniqueIdent = '${brIdent}']`;
    let evaluate = await xmlRef.evaluate(xPath, await xmlRef,null,XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,null);
    return evaluate.snapshotItem(0);
  }
  async #getDecisionXML(brDecisionId){
    let xmlDecision = (BrdpTable.xmlDecision != undefined ? BrdpTable.xmlDecision : this.#createXML("brdp/dmodule/br/" + brDecisionId + ".xml", false));
    BrdpTable.xmlDecision = xmlDecision;
    return xmlDecision;
  }
  async renderBrdptoContainer(containerId){
    let container = document.getElementById(containerId);
    container.innerHTML = '';
    let td = document.createElement('td');
    td.setAttribute('colspan', 5);
    
    let xslDetail = (BrdpTable.xslDetail != undefined ? BrdpTable.xslDetail : this.#createXML("brdp/style/php/brDetail.xsl", false));
    BrdpTable.xslDetail = xslDetail;
    this.#xsltProcessor.importStylesheet(await xslDetail);
    
    // render BRDP
    let brdp = await this.#getBrdpXML(this.brIdent);
    let fragment = this.#xsltProcessor.transformToFragment(brdp,document);
    td.appendChild(fragment);

    //render Decision
    let decision = await this.#getDecisionXML(this.brDecisionId);
    fragment = this.#xsltProcessor.transformToFragment(decision,document);
    td.appendChild(fragment);

    // final render
    container.appendChild(td);


  }

};