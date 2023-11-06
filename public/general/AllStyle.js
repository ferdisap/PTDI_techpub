/**
 * need createXML 
 */
const AllStyle = {

  driver: 'local',

  xslDoc: undefined,

  async getListAllGeneralStyle(refresh = false){

    let data = this.withDrawCached;
    // console.log(data);

    if (refresh) {
      data = false
    }
    if(data == false ){
      // jika refresh == true
      // let arr = await createXML(null,"GET", "", 'requestList', 'json');
      // let arr = await createXML(null, "refreshLocalStorage/requestList", "GET",);
      let arr = await createXML(null, "/generateAllStyle/n219", "GET",);
      
    
      let xsl;
      // await createXML(["http://www.w3.org/1999/XSL/Transform", 'xsl:stylesheet'], null).then(doc => xsl = doc);  
      await createXML(["http://www.w3.org/1999/XSL/Transform", 'xsl:stylesheet']).then(doc => xsl = doc);  
  
      new Promise(async (resolve) => {
        arr.forEach(async (file, i) => {
          // let styleDoc = await this.createXML('','getfile','xml',`view/general/xsl/${file}`,'GET');
          // let styleDoc = await createXML(null, 'GET', '', 'getfile','xml',`view/general/xsl/${file}`);
          // let styleDoc = await createXML(null, `ietp_n219/view/general/xsl/${file}`, 'GET');
          let styleDoc = await createXML(null, `/requestFile/n219/${file}?dataType=general-xsl`, 'GET');
          for(const node of styleDoc.firstElementChild.children){
            if(node.nodeName != 'xsl:include'){
              // console.log(node);
              setTimeout(() => {
                // ga pakai outerHTML karena akan megenerate xlmns nya
                xsl.firstElementChild.appendChild(node);
                if(arr[i+1] == undefined){
                  return resolve(xsl);
                }
              }, 0);
            }
          };
        });
      });
      return this.xslDoc = xsl;
    } else {
      let doc = AllStyle.withDrawCached
      return doc;
    } 
  },

  get cache(){
    if(this.xslDoc == undefined){
      return false;
    }
    const s = new XMLSerializer();
    const str = s.serializeToString(this.xslDoc);

    if(this.driver == 'session'){
      sessionStorage.setItem('allStyle', str);
      return true;
    } else {
      localStorage.setItem('allStyle', str);
      return true;
    }

  },

  get withDrawCached(){
    if ((data = sessionStorage.allStyle ? sessionStorage.allStyle : localStorage.allStyle) != undefined){
      const parser = new DOMParser();
      const doc = parser.parseFromString(data, "application/xml");
      return this.xslDoc = doc;      
    } else {
      return false;
    }    
  }

}