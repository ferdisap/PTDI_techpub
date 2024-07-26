import {formDataToObject, isEmpty, isString, isArray, findAncestor} from '../../helper';
import { isProxy, toRaw } from "vue";

async function getObjs(data = {}) {
  data = Object.keys(data).forEach(key => data[key] === undefined && delete data[key]);
  let response = await axios({
    route: {
      name: 'api.get_deletion_list',
      data: data,
    }
  });
  if (response.statusText === 'OK') {
    this.storingResponse(response);
  }
}

function storingResponse(response) {
  this.data.csdb = response.data.csdbs;
  delete response.data.csdbs;
  this.data.paginationInfo = response.data;
}

async function goto(url, page) {
  if (page) {
    url = new URL(this.pagination['path']);
    url.searchParams.set('page', page)
  }
  if (url) {
    let response = await axios.get(url);
    if (response.statusText === 'OK') {
      this.storingResponse(response);
    }
  }
}

/** sama denga FolderVue.js */
function removeList(filename) {
  let csdb = this.data.csdb.find((obj) => obj.filename === filename);
  let index = this.data.csdb.indexOf(csdb);
  this.data.csdb.splice(index,1);
  return csdb
}

async function restore(filename) {
  // let response = await axios({
  //   route: {
  //     name: 'api.restore_object',
  //     data: { filename: filename }
  //   },
  // })
  // if (response.statusText === 'OK') {
  //   this.removeList(filename);
  //   this.emitter.emit('RestoreCSDBobejctFromDeletion', response.data.data);
  // }
  let values = await this.CbSelector.restore(this.CbSelector.cancel); // output array contains filename
  if(isEmpty(values)) return; // jika fetch hasilnya reject (not resolve)
  else if(values instanceof FormData) values = formDataToObject(values);
  if(isString(values.filename)) values.filename = values.filename.split(',');

  // hapus list di folder, tidak seperti listtree yang ada level dan list model, dan emit csdbDelete
  const csdbDeleted = [];
  values.filename.forEach((filename) => {
    let csdb = this.removeList(filename);
    csdbDeleted.push(isProxy(csdb) ? toRaw(csdb) : csdb);
  });

  // emit
  this.emitter.emit('RestoreCSDBobejctFromDeletion',csdbDeleted);
}

async function permanentDelete(filename) {
  if (!(await this.$root.alert({ name: 'beforePermanentDeleteCsdbObject', filename: filename }))) {
    return;
  }

  let values = await this.CbSelector.permanentDelete(this.CbSelector.cancel); // output array contains filename
  if(isEmpty(values)) return; // jika fetch hasilnya reject (not resolve)
  else if(values instanceof FormData) values = formDataToObject(values);
  if(isString(values.filename)) values.filename = values.filename.split(',');

  // hapus list di folder, tidak seperti listtree yang ada level dan list model, dan emit csdbDelete
  const csdbDeleted = [];
  values.filename.forEach((filename) => {
    let csdb = this.removeList(filename);
    csdbDeleted.push(isProxy(csdb) ? toRaw(csdb) : csdb);
  });
}

async function download(filename) {
  let response = await axios({
    route: {
      name: 'api.get_deletion_object',
      data: { filename: filename },
    },
    responseType: 'blob',
  });
  if (response.statusText === 'OK') {
    let typeblob = response.headers.getContentType();
    if (typeblob.includes('xml')) {
      // let raw = await response.data.text(); // tidak dipakai
      let srcblob = URL.createObjectURL(await response.data);

      let a = $('<a/>')
      a.attr('download', filename);
      a.attr('href', srcblob);
      a[0].click();
    }
  }
}

function refresh(data) {
  // data adalah Array contain csdb deleted Object
  data.forEach((obj) => {
    this.data.csdb.push(obj)
  });
}

function select(event){
  let el;
  if(this.CbSelector.selectionMode) {
    this.CbSelector.select();
    el = document.getElementById(this.CbSelector.cbHovered);
  } else el = event.target;
  this.selectedRow ? this.selectedRow.classList.remove('bg-blue-300') : null;
  this.selectedRow = findAncestor(el, 'tr');
  this.selectedRow.classList.add('bg-blue-300');
}

function clickFilename(event, filename){
  this.CbSelector.select();
}

function preview(){

}

export {getObjs, storingResponse, goto, removeList, restore, permanentDelete, download, refresh, select, preview, clickFilename}