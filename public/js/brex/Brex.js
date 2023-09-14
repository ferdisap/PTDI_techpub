console.log('Brex.js')

const Brex = {

  SnsRule: {
    get parent() {return Brex;},
    // url: "/ietp_n219/view/brex/style/xsl/snsRules.xml",
    url: "/requestFile/n219/snsRules.xml?dataType=view-brex",
    xslDoc: async () => {
      // console.log(this);
      // return await Brex.createXML(Brex.SnsRule.url);
      return await createXML(null, Brex.SnsRule.url, 'GET');
    },
    htmlDoc: () => {
      return Brex.xmlToHtml(Brex.brexDoc, Brex.SnsRule.xslDoc).firstElementChild
    },  
    async refresh(){
      this.xslDoc = await this.xslDoc();
      this.htmlDoc = await this.htmlDoc();

      // add Allstyle to this XSL
      await (this.parent.addAllXslStyle.bind(this))(); //pake await agar xsl tambahan tergabung ke xsl ini
    }
  },

  ContextRule: {
    get parent() {return Brex;},
    // url: "/ietp_n219/view/brex/style/xsl/contextRules.xml",
    url: "/requestFile/n219/contextRules.xml?dataType=view-brex",
    xslDoc: async () => {
      // console.log(this);
      // return await Brex.createXML(Brex.ContextRule.url);
      return await createXML(null, Brex.ContextRule.url, 'GET');
    },
    htmlDoc: () => {
      return Brex.xmlToHtml(Brex.brexDoc, Brex.ContextRule.xslDoc).firstElementChild
    },  
    async refresh(){
      this.xslDoc = await this.xslDoc();
      this.htmlDoc = await this.htmlDoc();

      // add Allstyle to this XSL
      await (this.parent.addAllXslStyle.bind(this))(); //pake await agar xsl tambahan tergabung ke xsl ini
    }
  },

  // url: "/ietp_n219/dmodule/brex/DMC-N219-A-00-00-0000-00A-022A-D_001-00_EN-US.xml",
  // url: "/ietp_n219/csdb/business_rule/DMC-N219-A-00-00-0000-00A-022A-D_001-00_EN-US.xml",
  url: "/requestFile/n219/DMC-N219-A-00-00-0000-00A-022A-D_001-00_EN-US.xml?dataType=csdb-brex",
  brexDoc: null,

  createXML(url, method = 'GET'){
    // console.log(url);
    xhr = new XMLHttpRequest();
    let prom = new Promise((resolve,reject) => {
      xhr.open(method, "/?utility=getfile&ct=xml&path=" + url, false); 
      xhr.onload = () => {
        if (xhr.status >= 200 && xhr.status < 300) {
            return resolve(xhr.responseXML)
        } else {
            reject({
                status: this.status,
                statusText: xhr.statusText
            });
        }
      };
      xhr.onerror = () => {
          reject({
              status: this.status,
              statusText: xhr.statusText
          });
      };
      xhr.send();
    });
    return prom
  },

  renderHtml(idContainer, node){
    let div = document.createElement('div');
    div.innerHTML = node.outerHTML;

    return this.showContent(idContainer, div);
  },
    
  xmlToHtml(xmlDoc, xslDoc){
    let xsltProcessor = new XSLTProcessor();
    xsltProcessor.importStylesheet(xslDoc)

    let dom = xsltProcessor.transformToDocument(xmlDoc);
    return dom;
  },

  showContent(idContainer, element = undefined){
    if (this.currentContent != undefined){
      let currentContentContainer = document.getElementById(this.currentContent);
      currentContentContainer.innerHTML = '';
    }
    
    if (element != undefined){
      let container = document.getElementById(idContainer);
      // console.log(element, idContainer, container);
      container.innerHTML = element.outerHTML;
      this.currentContent = idContainer;
    }

    localStorage.setItem('currentContent', this.currentContent);
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

  async refresh(){
    this.brexDoc = await createXML(null, this.url, 'GET');
  },
}


document.addEventListener('DOMContentLoaded', async () => {
  let prom = new Promise(async (resolve, reject) => {
    await Brex.refresh();
    await Brex.SnsRule.refresh();
    await Brex.ContextRule.refresh();

    if (Brex.brexDoc instanceof XMLDocument){
      resolve(true);    
    } else {
      reject(false);
    }
  });

  // berfungsi agar diawal kita akan otomatis buka menu SNS Rules atau Context Rules atay Non Context Rules
  prom.then( v => {
    if(v){
      let currentContent = localStorage.getItem('currentContent');
      if (currentContent == null){
        Brex.renderHtml('SnsRule', Brex.SnsRule.htmlDoc);
      } else {
        // console.log(`Brex.${currentContent}`);
        eval(`Brex.renderHtml('${currentContent}', Brex.${currentContent}.htmlDoc)`);
      }
    }
  });
})