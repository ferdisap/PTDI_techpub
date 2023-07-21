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
    console.log('done');
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
      let searchInput = [];
      document.querySelectorAll('.brdp_input_search').forEach(el => {
        if (el.value != '') {
          searchInput[el.getAttribute('filterBy')] = el.value;
        }
      });
      console.log(window.searchInput = searchInput);
      this.runEngine(searchInput); // input value
    }
  },
  async runEngine(filterBy = []) {
    let brDataModule = (BrdpTable.brDataModule != undefined ? await BrdpTable.brDataModule : this.createXML("brdp/dmodule/br/tes.xml", false));
    BrdpTable.brDataModule = brDataModule;

    let XPathResult = this.evaluate(this.getXPath(filterBy), await brDataModule); // output object XPathResult
    this.renderResult(XPathResult);
  },

  removeArrayItemOnce(arr, value) {
    var index = arr.indexOf(value);
    if (index > -1) {
      arr.splice(index, 1);
    }
    return arr;
  },

  setXpath(key, text) {
    switch (key) {
      case 'ident':
        return `//@brDecisionPointUniqueIdent[contains(.,'${text}')]/ancestor::brPara`;
      case 'title':
        return `//brDecisionPointContent/title[contains(.,'${text}')]/ancestor::brPara`;
      case 'category':
        return `//@brCategoryNumber[contains(.,'${text}')]/ancestor::brPara | //brCategory[contains(.,'${text}')]/ancestor::brPara`;
      default:
        return;
    }
  },

  getXPath(filterBy = [], text) {
    let filters = ['all', 'ident', 'title', 'category'];
    let xpath = [];
    for (let ky in searchInput) {
      xpath[ky] = this.setXpath(ky, searchInput[ky]);
      this.removeArrayItemOnce(filters, ky)
    }
    if (searchInput['all'] != undefined) {
      filters.forEach(filter => {
        xpath[filter] = this.setXpath(filter, searchInput['all'])
      });
      delete xpath['all'];
    }
    let finalXpath = [];
    for (let ky in xpath) {
      finalXpath.push(xpath[ky]);
    }
    finalXpath = finalXpath.join(' | ');
    console.log('finalXpath', finalXpath);
    return finalXpath;
  },
  createXML(url) {
    let xhr = new XMLHttpRequest();
    xhr.open('GET', "/brdp?utility=getfile&path=" + url, false);
    xhr.send(null);
    return xhr.responseXML;
  },
  evaluate(xpath, xmlRef) {
    const searchResult = xmlRef.evaluate(xpath, xmlRef, null, XPathResult.ORDERED_NODE_SNAPSHOT_TYPE, null);
    return searchResult;
  },
  async renderResult(rootNode) { // to generate result
    // prepare xslt processor khusus brList_search
    const xsltProcessor = new XSLTProcessor();
    let xslList_search = (BrdpTable.xslList_search != undefined ? BrdpTable.xslList_search : this.createXML("brdp/style/js/brList_search.xsl", false));
    BrdpTable.xslList_search = await xslList_search;
    xsltProcessor.importStylesheet(await xslList_search);

    let innerHTMLTbody = '';
    // ektrak hasil result dari object XPathResult
    for (let i = 0; i < rootNode.snapshotLength; i++) {
      let xmlNode = rootNode.snapshotItem(i); // output = "<brPara>"

      // brPara hasil result akan di transform pakai xslt menghasilkan <tr>
      let dom = xsltProcessor.transformToDocument(xmlNode);
      let tr = dom.getRootNode().firstChild
      innerHTMLTbody += tr.outerHTML
    }

    // hapus semua tr dari tbody table brdp
    let oriTbody = document.querySelector('#brdpList-table tbody');
    oriTbody.remove();

    // tambahkan tr baru yang diadapt dari search
    let newTbody = document.createElement('tbody');
    newTbody.innerHTML = innerHTMLTbody;
    document.getElementById('brdpList-table').appendChild(newTbody);

    // menambahkan informasi total jumlah pencarian.
    document.getElementById('totalSearchResult').innerHTML = rootNode.snapshotLength + " result(s) found.";
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