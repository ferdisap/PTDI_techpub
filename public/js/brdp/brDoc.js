console.log('brDoc');

/** Object BRDP Table */
const BrdpTable = {
  detailOpen: [],
  trMouseOver: document.createElement('tr'),

  setURLHash(id) {
    window.history.pushState({}, "", window.location.origin + window.location.pathname + '#' + id);
  },
  goToHash(hash) {
    window.location.hash = "";
    setTimeout(() => {
      window.location.hash = hash;
    }, 0);
  },
  openDetail(brIdent, brDecisionId, trId, el) {
    if (!this.detailOpen.includes(trId)) {
      // open detail
      let tr = document.getElementById(trId + '_detail');
      if (tr == undefined) { // jika tr belum pernah dibuat
        tr = this.createDetailContainer(trId + '_detail');
        el.parentElement.insertBefore(tr, el.nextElementSibling ?? el);
      } else {
        tr.style.display = '';
      }
      this.detailOpen.push(trId);

      // render brdp
      this.renderBrdp(brIdent, trId + '_detail', brDecisionId);
      this.setURLHash(trId);
    } else {
      // close detail
      let tr = document.getElementById(trId + '_detail');
      tr.style.display = 'none';
      this.removeArrayCertainItem(trId);
    }
  },
  closeDetailAll() {
    this.detailOpen.forEach(trId => {
      document.getElementById(trId + '_detail').style.display = 'none';
    });
    this.detailOpen = [];
  },
  createDetailContainer(id = null) {
    let tr = document.createElement('tr');
    tr.setAttribute('id', id);
    tr.setAttribute('class', 'brdp_detail');
    tr.innerHTML = 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Laboriosam, velit?';
    return tr;
  },
  removeArrayCertainItem(item) {
    let i = this.detailOpen.indexOf(item);
    if (i > -1) {
      this.detailOpen.splice(i, 1);
    }
  },
  renderBrdp(brIdent, containerId, brDecisionId) {
    let brdp = new BrdpDetail(brIdent, brDecisionId)
    brdp.renderBrdptoContainer(containerId);
  },
  changeBgColor(el) {
    this.trMouseOver.style.backgroundColor = null;
    el.style.backgroundColor = "aliceblue";
    this.trMouseOver = el;
  }
};

BrdpTable.goToHash(window.location.hash);


/** Object BRDP Search */
const BrdpSearch = {

  evetListener(el, evt) {
    if (evt.keyCode === 13) { // enter button
      evt.preventDefault();
      /** script baru */
      let searchFilterInput = document.querySelectorAll("input[filterBy]");
      let searchInput = [[searchFilterInput[0]]];
      let fb = document.querySelectorAll(`.filterSort`);
      [1,2,3,4,5].forEach(no => {
        let subSearchInput = [];
        fb.forEach(el => {
          if (el.value == no && el.nextElementSibling.value != ''){
            subSearchInput.push(el.nextElementSibling);
          }
        });
        if (subSearchInput.length > 0) {
          searchInput.push(subSearchInput);
        }
      });
      this.runEngine(searchInput);
    }
  },
  async runEngine(searchInput = []) {
    let brDataModule = (BrdpTable.brDataModule != undefined ? await BrdpTable.brDataModule : this.createXML("brdp/dmodule/br/tes.xml", false));
    BrdpTable.brDataModule = brDataModule;

    // 1. prepare array berisi urutan filter
    let step_xpaths = this.getXPaths(searchInput);

    // 2. untuk setiap item dalam urutan (index 1 adalah filter utama)
    for (let i = 0; i < step_xpaths.length; i++) {
        let xPathRes = this.evaluate(step_xpaths[i],brDataModule.firstElementChild); // output object XPathResult

        let extractedNode = [];
        for (let i = 0; i < xPathRes.snapshotLength; i++) {
          let xmlNode = xPathRes.snapshotItem(i); // output = "<brPara>"
          extractedNode.push(xmlNode);
        }  
          
        // 4. delete all brPara in updatedDb
        while (brDataModule.firstElementChild.firstElementChild){
          brDataModule.firstElementChild.removeChild(brDataModule.firstElementChild.lastChild)
        }

        // 5. add brPara to brDataModule
        for (let n = 0; n < extractedNode.length; n++) {
            brDataModule.firstElementChild.appendChild(extractedNode[n]);
        }
    }
    this.renderResult(brDataModule.firstElementChild);
  },

  removeArrayItemOnce(arr, value) {
    var index = arr.indexOf(value);
    if (index > -1) {
      arr.splice(index, 1);
    }
    return arr;
  },

  setXpath(filterBy, text) {
    let xpath_ident = `//@brDecisionPointUniqueIdent[contains(.,'${text}')]/ancestor::brPara`;
    let xpath_title = `//brDecisionPointContent/title[contains(.,'${text}')]/ancestor::brPara`;
    let xpath_category = `//@brCategoryNumber[contains(.,'${text}')]/ancestor::brPara | //brCategory[contains(.,'${text}')]/ancestor::brPara`;

    switch (filterBy) {
      case 'ident':
        return xpath_ident;
      case 'title':
        return xpath_title;
      case 'category':
        return xpath_category;
      case 'all':
        return xpath_ident + ' | ' + xpath_title + ' | ' + xpath_category;
      default:
        return '';
    }
  },

  getXPaths(searchInput = []) {
    let xpaths = [];
    for (let i = 0; i < searchInput.length; i++) {
      let subXPaths = [];
      searchInput[i].forEach(el => {
        subXPaths.push(this.setXpath(el.getAttribute('filterBy'), el.value));
      });
      xpaths.push(subXPaths.join(' | '));
    }
    return xpaths;
  },
  createXML(url) {
    let xhr = new XMLHttpRequest();
    xhr.open('GET', "/brdp?utility=getfile&path=" + url, false);
    xhr.send(null);
    return xhr.responseXML;
  },
  evaluate(xpath, xmlRef) {    
    // xmlRef instanceof Node, jadi perlu dijadikan Document supaya bisa pakai fungsi evaluate(xpath)
    let newXmlDoc = document.implementation.createDocument(null, 'dmodule');
    newXmlDoc.firstElementChild.innerHTML = xmlRef.innerHTML;

    // evaluate xpath terhadap xmlDoc
    const searchResult = newXmlDoc.evaluate(xpath, newXmlDoc, null, XPathResult.ORDERED_NODE_SNAPSHOT_TYPE, null);
    return searchResult;
  },
  async renderResult(rootNode) {    
    const xsltProcessor = new XSLTProcessor();
    let xslList_search = (BrdpTable.xslList_search != undefined ? BrdpTable.xslList_search : this.createXML("brdp/style/js/brList_search.xsl", false));
    BrdpTable.xslList_search = await xslList_search;
    xsltProcessor.importStylesheet(await xslList_search);

    // rootNode berupa <root><brPara/>...<brPara/>...</root>
    let nodes = rootNode.children;
    let innerHTMLTbody = '';
    for(let node of nodes){
      let dom = xsltProcessor.transformToDocument(node);
      let tr = dom.getRootNode().firstChild
      innerHTMLTbody += tr.outerHTML
    };
    // hapus semua tr dari tbody table brdp
    let oriTbody = document.querySelector('#brdpList-table tbody');
    oriTbody.remove();

    // tambahkan tr baru yang didapat dari search
    let newTbody = document.createElement('tbody');
    newTbody.innerHTML = innerHTMLTbody;
    document.getElementById('brdpList-table').appendChild(newTbody);

    // menambahkan informasi total jumlah pencarian.
    document.getElementById('totalSearchResult').innerHTML = nodes.length + " result(s) found.";
  }
}

/** Class BRDP Detail */

class BrdpDetail {
  brIdent = null;
  brDecisionId = null;
  #xhr = new XMLHttpRequest();
  #parser = new DOMParser();
  #xsltProcessor = new XSLTProcessor();
  #xmlSerializer = new XMLSerializer();
  constructor(brIdent, brDecisionId) {
    this.brIdent = brIdent;
    this.brDecisionId = brDecisionId;
  }
  #createXML(url) {
    this.#xhr.open('GET', "/brdp?utility=getfile&path=" + url, false);
    this.#xhr.send(null);
    return this.#xhr.responseXML;
  }
  async #getBrdpXML(brIdent) {
    let brDataModule = (BrdpTable.brDataModule != undefined ? BrdpTable.brDataModule : this.#createXML("brdp/dmodule/br/tes.xml", false));
    BrdpTable.brDataModule = brDataModule;

    let xPath = `//brPara[@brDecisionPointUniqueIdent = '${brIdent}']`;
    let evaluate = await brDataModule.evaluate(xPath, await brDataModule, null, XPathResult.ORDERED_NODE_SNAPSHOT_TYPE, null);
    return evaluate.snapshotItem(0);
  }
  async #getDecisionXML(brDecisionId) {
    let xmlDecision = (BrdpTable.xmlDecision != undefined ? BrdpTable.xmlDecision : this.#createXML("brdp/dmodule/br/" + brDecisionId + ".xml", false));
    BrdpTable.xmlDecision = xmlDecision;
    return xmlDecision;
  }
  async renderBrdptoContainer(containerId) {
    let container = document.getElementById(containerId);
    container.innerHTML = '';
    let td = document.createElement('td');
    td.setAttribute('colspan', 5);
    td.style.border = 'inherit';

    let xslDetail = (BrdpTable.xslDetail != undefined ? BrdpTable.xslDetail : this.#createXML("brdp/style/php/brDetail.xsl", false));
    BrdpTable.xslDetail = xslDetail;
    this.#xsltProcessor.importStylesheet(await xslDetail);

    // render BRDP
    let brdp = await this.#getBrdpXML(this.brIdent);
    let fragment = this.#xsltProcessor.transformToFragment(brdp, document);
    td.appendChild(fragment);

    //render Decision
    let decision = await this.#getDecisionXML(this.brDecisionId);
    fragment = this.#xsltProcessor.transformToFragment(decision, document);
    td.appendChild(fragment);

    // final render
    container.appendChild(td);


  }

};