console.log('Brdp2.js')

const Brdp = {
  BrList: {
    get parent() {return Brdp;},
    // url: "view/brdp/style/xsl/brList.xsl",
    url: "/requestFile/n219/brList.xsl?dataType=view-brdp",
    xslDoc: null,
    async setXslDoc(){
      // return this.xslDoc = await this.parent.createXML(this.url);
      return this.xslDoc = await createXML(null, this.url, 'GET');
    },
    htmlDoc:null,
    async setHtmlDoc(){
      return this.htmlDoc = this.parent.xmlToHtml(this.parent.brdpDoc.firstElementChild, this.xslDoc.firstElementChild);
    },  
    async refresh(){
      await this.setXslDoc();
      await (this.parent.addAllXslStyle.bind(this))(); //pake await agar xsl tambahan tergabung ke xsl ini
      await this.setHtmlDoc();      
      return this.xslDoc ? true : false;
    }
  },
  BrDetail: {
    get parent() {return Brdp;},
    // url: "/ietp_n219/view/brdp/style/xsl/brDetail.xsl",
    url: "/requestFile/n219/brDetail.xsl?dataType=view-brdp",
    xpath: (id) => {
      // return `//brPara/brDecision[@brDecisionIdentNumber='${id}']/parent::*`
      return `//brPara[@brDecisionPointUniqueIdent='${id}']`;
    },
    xslDoc: null,
    async setXslDoc(){
      // return await this.parent.createXML(this.url);
      return await createXML(null, this.url, 'GET');
    },
    detailOpen: [],
    async openDetail(brIdent, brDecisionId, trId, el){
      //set window hash
      // window.location.hash = "#" + trId; // tidak pakai yang ini karena window akan langsung menuju hash
      window.history.pushState({},"",window.location.origin + window.location.pathname + '#' + trId);

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
        // this.xmlNode = Brdp.brdpDoc.evaluate(this.xpath(brDecisionId), this.parent.brdpDoc).iterateNext();
        this.xmlNode = Brdp.brdpDoc.evaluate(this.xpath(brIdent), this.parent.brdpDoc).iterateNext();
        console.log(this.xmlNode);
        this.renderBrdp(brIdent, trId + '_detail', brDecisionId);
      } else {
        // close detail
        let tr = document.getElementById(trId + '_detail');
        tr.style.display = 'none';
        this.detailOpen = this.parent.removeArrayCertainItem(trId,this.detailOpen);
      }
    }, 
    async renderBrdp(brIdent, containerId, brDecisionId){
      let td = document.createElement('td');
      td.setAttribute('colspan', 5);
      td.style.border = 'inherit';

      // get xml node from Brdp.brdpDoc
      let doc = this.parent.xmlToHtml(this.xmlNode, this.xslDoc);
      console.log(this.xslDoc);
      td.innerHTML = doc.firstElementChild.outerHTML;

      // get decision document from server
      let decisionXmlNode = await this.parent.BrDecision.getXmlDoc(brDecisionId);
      if (decisionXmlNode){
        decisionXmlNode = decisionXmlNode.firstElementChild;
        let docDecision = this.parent.xmlToHtml(decisionXmlNode, this.xslDoc).firstElementChild;
        td.appendChild(docDecision);
      }

      this.parent.showContent(containerId, td);
    },
    createDetailContainer(id = null) {
      let tr = document.createElement('tr');
      tr.setAttribute('id', id);
      tr.setAttribute('class', 'brdp_detail');
      tr.innerHTML = 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Laboriosam, velit?';
      return tr;
    },
    trMouseOver: document.createElement('tr'),
    changeBgColor(el) {
      this.trMouseOver.style.backgroundColor = null;
      el.style.backgroundColor = "aliceblue";
      this.trMouseOver = el;
    },    
    async refresh(){
      this.xslDoc = await this.setXslDoc();
      // add Allstyle to this XSL
      await (this.parent.addAllXslStyle.bind(this))(); //pake await agar xsl tambahan tergabung ke xsl ini
      return true;
    }
  },
  BrDecision: {
    get parent() {return Brdp;},
    
    /**
     * 
     * @param {String} brDecisionId 
     * @returns XMLDoc
     */
    async getXmlDoc(brDecisionId) {
      // return await this.parent.createXML(`dmodule/brdp/br_s1000d/decision/${brDecisionId}.xml`, 'GET');
      return await createXML(null, `/requestFile/n219/${brDecisionId}.xml?dataType=brdp-decision`, 'GET');
    },
    requestToServer: false,
    async getListAllDecision(){
      if (this.requestToServer == true){
        localStorage.removeItem('allDecision');
      }
      if (localStorage.allDecision != undefined){
        return localStorage.allDecision;
      }
      let allDecisionArr = this.parent.brdpDoc.querySelectorAll("brDecision[brDecisionIdentNumber]");
      let lists = [];
      let serialized; 
      await allDecisionArr.forEach(async (el, i) => {        
        let brDecisionIdentNumber = el.getAttribute("brDecisionIdentNumber");
        // console.log(brDecisionIdentNumber);
        let doc = await this.getXmlDoc(brDecisionIdentNumber); 
        if (doc){
          doc = this.removerAttr(doc);
          lists.push(doc.firstElementChild.outerHTML);
        }
        if(i == allDecisionArr.length-1){
          serialized = JSON.stringify(lists);
          localStorage.setItem("allDecision", serialized);
          return serialized;
        }
      });
      this.requestToServer = false;
      return serialized; // sudah di serialize to json
    },

    removerAttr(doc){
      doc.firstElementChild.removeAttribute("xsi:noNamespaceSchemaLocation");
      doc.firstElementChild.removeAttribute("xmlns:xlink");
      doc.firstElementChild.removeAttribute("xmlns:rdf");
      doc.firstElementChild.removeAttribute("xmlns:dc");
      doc.firstElementChild.removeAttribute("xmlns:xsi");
      return doc;
    },

    async attachDecisionToParentXmlDoc(){
      let serialized = await this.getListAllDecision(); // jika null akan mengambil semua Litst
      // console.log(serialized);
      let lists = JSON.parse(serialized);
      const parser = new DOMParser();
      
      await lists.forEach(async (outerHtml) => {
        let doc = parser.parseFromString(outerHtml, 'text/xml');
        let brDecisionPointUniqueIdent = doc.firstElementChild.getAttribute('brDecisionIdentNumber');
        if (brDecisionPointUniqueIdent == null){
          return;
        }
        let xpath = `//brPara[@brDecisionPointUniqueIdent = '${brDecisionPointUniqueIdent}']/brDecision`;
        let brDecision = await this.parent.brdpDoc.evaluate(xpath,this.parent.brdpDoc).iterateNext();
        brDecision.innerHTML = doc.firstElementChild.innerHTML;          
      });
      return this.parent.brdpDoc;
    },

    async refresh(requestToServer = false){
      if(requestToServer){
        this.requestToServer = true;
      }
      await this.getListAllDecision();
      return await this.attachDecisionToParentXmlDoc();
    }
  },
  BrSearch: {    
    get parent() {return Brdp;},
    listener(evt) {
      // console.log(window.evt = evt)
      this.parent.BrDetail.detailOpen = [];
      if (evt.keyCode === 13 || evt.type == 'click') { // enter button
        evt.preventDefault();
        if (!this.applSchemaXPath){
          this.setApplSchema(document.querySelectorAll("#applSchema input"));
        }

        /** script baru */
        let searchFilterInput = document.querySelectorAll("input[filterBy]");
        let searchInput = [[searchFilterInput[0]]];
        // let searchInput = searchFilterInput[0].value != '' ? [[searchFilterInput[0]]] : []; // ngga pakai ini agar bisa pilih applSchema
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
    useApplSchema: false,
    setApplSchema(cheklistAll = []){
      let additionXPath = []; // hasilnya [".//@comrepXsd='1'", ".//@commentXsd='1'"]
      cheklistAll.forEach(el => {
        if (el.checked){
          let xpath = `.//@${el.value}='1'`;
          additionXPath.push(xpath);
        }
      });
      this.applSchemaXPath =  additionXPath.join(" and ");
      return this.applSchemaXPath;
    },
    async runEngine(searchInput = []) {
      let db = this.parent.brdpDoc;
  
      let brDataModule = document.implementation.createDocument(null, 'dmodule');
      brDataModule.firstElementChild.innerHTML = db.firstElementChild.innerHTML;
  
      // 1. prepare array berisi XPath (filter) berurutan
      let step_xpaths = this.getXPaths(searchInput);
      // console.log(step_xpaths);
  
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
    getXPaths(searchInput = []) {
      let xpaths = [];
      for (let i = 0; i < searchInput.length; i++) {
        let subXPaths = [];
        searchInput[i].forEach(el => {
          // separate each input value as per ";"
          /** Script Baru */
          let strArr = el.value.split(";");
          strArr.forEach(str => {
            str = str.trimStart();
            str = str.trimEnd();
            subXPaths.push(this.setXpath(el.getAttribute('filterBy'), str));
          });
          // subXPaths.push(this.setXpath(el.getAttribute('filterBy'), el.value));
          /** end of Script Baru */
        });
        xpaths.push(subXPaths.join(' | '));
      }
      return xpaths;
    },
    
    setXpath(filterBy, text) {
      // // CASE Sensitive
      let xpath_ident = `//@brDecisionPointUniqueIdent[contains(.,'${text}')]/ancestor::brPara`;
      let xpath_title = `//brDecisionPointContent/title[contains(.,'${text}')]/ancestor::brPara`;
      let xpath_category = `//@brCategoryNumber[contains(.,'${text}')]/ancestor::brPara | //brCategory[contains(.,'${text}')]/ancestor::brPara`;
      let xpath_decision = `//brDecision[contains(.,'${text}')]/ancestor::brPara | //brDecision/@brDecisionIdentNumber[contains(.,'${text}')]/ancestor::brPara`;
      let xpath_audit = `//brAudit[contains(.,'${text}')]/ancestor::brPara | //brCurrentStatus[@brStatus = '${text}']/ancestor::brPara`;
      let xpath_all;
      if (this.useApplSchema){
        xpath_all = `//*[contains(.,'${text}')]/ancestor::brPara[${this.applSchemaXPath}] | //@*[contains(.,'${text}')]/ancestor::brPara[${this.applSchemaXPath}]`;
      } else {
        xpath_all = `//*[contains(.,'${text}')]/ancestor::brPara | //@*[contains(.,'${text}')]/ancestor::brPara`;
      }

      // // INCASE Sensitive
      // let xpath_ident = `//@brDecisionPointUniqueIdent[contains(translate(., 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'),'${text}')]/ancestor::brPara`;
      // let xpath_title = `//brDecisionPointContent/title[contains(translate(., 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'),'${text}')]/ancestor::brPara`;
      // let xpath_category = `//@brCategoryNumber[contains(translate(., 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'),'${text}')]/ancestor::brPara | //brCategory[contains(translate(., 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'),'${text}')]/ancestor::brPara`;
      // let xpath_decision = `//brDecision[contains(translate(., 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'),'${text}')]/ancestor::brPara | //brDecision/@brDecisionIdentNumber[contains(translate(., 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'),'${text}')]/ancestor::brPara`;
      // let xpath_audit = `//brAudit[contains(translate(., 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'),'${text}')]/ancestor::brPara`;
      // let xpath_all;
      // if (this.useApplSchema){
      //   xpath_all = `//*[contains(translate(., 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'),'${text}')]/ancestor::brPara[${this.applSchemaXPath}] | //@*[contains(translate(., 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'),'${text}')]/ancestor::brPara[${this.applSchemaXPath}]`;
      // } else {
      //   xpath_all = `//*[contains(translate(., 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'),'${text}')]/ancestor::brPara | //@*[contains(translate(., 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'),'${text}')]/ancestor::brPara`;
      // }

      /**
       * INCASESENSITIVE example
       * tambahkan translate(., 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz') sebagai parameter[0] contains();
       * let xpath_decision = `//brDecision/descendant::text()[contains(translate(., 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'), 'the')]/ancestor::brPara | //brDecision/@brDecisionIdentNumber[contains(.,'${text}')]/ancestor::brPara`;
       */
  
      switch (filterBy) {
        case 'ident':
          return xpath_ident;
        case 'title':
          return xpath_title;
        case 'category':
          return xpath_category;
        case 'decision':
          return xpath_decision;
        case 'audit':
          return xpath_audit;
        case 'all':
          return xpath_all;
        default:
          return '';
      }
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
      let xslSearch = this.parent.BrList.xslDoc;
      this.xslSearch = xslSearch;
      xsltProcessor.importStylesheet(xslSearch);
  
      // rootNode berupa <dmodule><brPara/>...<brPara/>...</dmodule>
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
    },
    select: 'apart',
    selectDeselectAll(btn){
      let s = this.select == 'all' ? true : false;
      document.querySelectorAll("#applSchema input").forEach(el => {
        el.checked = s;
      });
      btn.innerText = s == true ? 'deselect all' : ' select all';
      this.select = this.select == 'all' ? 'apart' : 'all';
    },    
  },

  // url: "dmodule/brdp/br_s1000d/DMC-N219-A-00-00-0000-00A-024A-D_001-00_EN-US.xml",
  url: "/requestFile/n219/DMC-N219-A-00-00-0000-00A-024A-D_001-00_EN-US.xml?dataType=csdb-brdp",
  brdpDoc: null,

  /** 
   * @param {String} idContainer 
   * @param {HTML DOM Node} node 
   * @returns 
   */
  renderHtml(idContainer, node){
    let div = document.createElement('div');
    div.innerHTML = node.outerHTML;

    return this.showContent(idContainer, div);
  },
    
 /** 
   * @param {xmlNode} xmlDoc 
   * @param {XMLDocument} xslDoc 
   * @returns HTML Document - should use dom.firstChildElement or more.
   */
  xmlToHtml(xmlDoc, xslDoc){
    let xsltProcessor = new XSLTProcessor();
    xsltProcessor.importStylesheet(xslDoc)

    let dom = xsltProcessor.transformToDocument(xmlDoc);
    return dom;
  },

  /** 
   * @param {String} idContainer 
   * @param {HTMLNode} element 
   */
  showContent(idContainer, element = undefined){    
    if (element != undefined){
      let container = document.getElementById(idContainer);
      container.innerHTML = '';
      container.innerHTML = element.outerHTML;
      this.currentContent = idContainer;
    }
  },

  removeArrayCertainItem(item, arr) {
    let i = arr.indexOf(item);
    let newArr = arr;
    if (i > -1) {
      newArr.splice(i, 1);
    }
    return newArr;
  },

  /**
   * this adalah object yang memanggil fungsi ini agar bisa ditambah ke xsl original (setelah xsl original nya telah di download)
   * jadi harus di bind(this);
   */
  async addAllXslStyle(){
    if (AllStyle.xslDoc == undefined){
      await AllStyle.getListAllGeneralStyle();
      AllStyle.cache;
    }
    this.xslDoc.firstElementChild.innerHTML += AllStyle.xslDoc.firstElementChild.innerHTML;
  },
  
  /** */
  async refresh(){
    // return this.brdpDoc = await this.createXML(this.url);
    return this.brdpDoc = await createXML(null, this.url, 'GET');
  },
}

