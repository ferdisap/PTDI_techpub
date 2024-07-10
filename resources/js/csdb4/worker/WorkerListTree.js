// jika ingin pakai modul, begini
// import setListTreeData from './setListTreeData.js';
const ListTree = {
  request: async function (e) {
    let response;
    let route = e.data.route;
    if (Object.values(route.method).includes('GET') || route.method.includes('HEAD')) {
      response = await fetch(route.url);
    }
    return new Promise(async (resolve, reject) => {
      // tes;
      if (response.ok) {
        let json = await response.json();
        // resolve(setListTreeData(json.data)); // jika ingin pakai modul
        resolve(this.setListTreeData(json.data));
      } else {
        reject([]);
      }
    })
  },
  /**
   * @return {Array}
   * @param {Object} response from axios Response 
   */
  setListTreeData: function (data) {
    // sortir berdasarkan path
    data = data.sort((a, b) => {
      return a.path > b.path ? 1 : (a.path < b.path ? -1 : 0);
    });
    // sortir object dan level path nya eg: "/csdb/n219/amm" berarti level 3
    let obj = {};
    let levels = {};
    for (const v of data) {
      let path = v.path
      let split = path.split("/");
      let l = split.length;

      let p = [];
      for (let i = 1; i <= l; i++) {
        p.push(split[i - 1]);
        levels[i] = levels[i] ?? [];
        levels[i].push(p.join("/"));
      }
      levels[l].indexOf(path) < 0 ? levels[l].push(path) : '';

      obj[path] = obj[path] || [];
      obj[path].push(v);

    }
    return [obj, levels];
  }
}

onmessage = async function (e) {
  let ret = await ListTree.request(e);
  postMessage(ret);
}