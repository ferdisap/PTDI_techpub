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
    let db = (BrdpTable.brDataModule != undefined ? BrdpTable.brDataModule : this.createXML("dmodule/brdp/br_s1000d/DMC-N219-A-00-00-0000-00A-024A-D_001-00_EN-US.xml", false));
    BrdpTable.brDataModule = db;

    let brDataModule = document.implementation.createDocument(null, 'dmodule');
    brDataModule.firstElementChild.innerHTML = db.firstElementChild.innerHTML;

    console.log('brDataModule',brDataModule);

    // 1. prepare array berisi XPath (filter) berurutan
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
    let xslList_search = (BrdpTable.xslList_search != undefined ? BrdpTable.xslList_search : this.createXML("view/brdp/style/js/brList_search.xsl", false));
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