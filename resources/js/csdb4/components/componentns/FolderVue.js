import {findAncestor, isNumber, isEmpty, array_unique, isString, formDataToObject} from '../../helper';
import { isProxy, toRaw } from "vue";
import $ from 'jquery';

async function getObjs(data = {}){
  this.showLoadingProgress = true;
  if (!data.sc) data.sc = '';
  if (data.path) {
    if (data.sc.search(/path::\S+\s/) < 0) data.sc += " path::" + data.path; // nambah ' path::...' ke dalam sc
    else data.sc = data.sc.replace(/path::\S+\s/, "path::" + data.path + " "); // mengganti 'path::... ' dengan path data.path
    delete data.path;
  }
  // ini dicancel. Semua CSDBObject akan di fetch ditampilkan Explorer.vue
  // switch (this.$props.routeName) {
  //   case 'Explorer':
  //     data.sc += " typeonly::DMC,PMC,ICN";
  //     break;
  //   case 'ManagementData':
  //     data.sc += " typeonly::DML";
  //     break;
  //   default:
  //     break;
  // }
  let response = await axios({
    route: {
      name: 'api.requestbyfolder.get_allobject_list',
      data: data// akan receive data: [model1, model2, ...]
    },
    useMainLoadingBar: false,
  });
  this.storingResponse(response);
  this.showLoadingProgress = false;
}

function storingResponse(response) {
  if (response.statusText === 'OK') {
    this.data.csdb = response.data.csdbs; // array contain object csdb
    this.data.folders = response.data.folders; // array contain string path
    // this.data.current_path = response.data.current_path ?? this.$props.dataProps.path;
    this.data.current_path = response.data.current_path ? response.data.current_path : this.$props.dataProps.path;
    delete response.data.data;
    delete response.data.folder;
    delete response.data.message;
    delete response.data.current_path;
    this.data.paginationInfo = response.data;
  }
}

async function goto(url, page) {
  if (page) {
    url = new URL(this.pagination['path']);
    url.searchParams.set('page', page)
  }
  if (url) {
    let response = await axios.get(url);
    if(response.statusText === 'OK'){
      this.storingResponse(response);
    }
  }
}

async function back(path = undefined) {
  if(!this.selectionMode){
    if(!path){
      path = this.currentPath.replace(/\/\w+\/?$/, "");
    }
    this.getObjs({path: path});
    this.data.current_path = path;
  }
}

function clickFolder(event, path){
  // window.path = path; window.getObjs = this.getObjs;
  if(!this.CB.selectionMode) this.back(path);
}

function clickFilename(event, filename){
  if(!this.CB.selectionMode){
    this.techpubStore.currentObjectModel = Object.values(this.data.csdb).find((obj) => obj.filename === filename);
    this.$root.gotoExplorer(filename, 'pdf');
    this.emitter.emit('clickFilenameFromFolder', {filename: filename}) // key filename saja karena bisa diambil dari techpubstore atau server jika perlu
  }
}

function sortTable(event){
  const getCellValue = function (row, index) {
    return $(row).children('td').eq(index).text();
  };
  const comparer = function (index) {
    return function (a, b) {
      let valA = getCellValue(a, index), valB = getCellValue(b, index);
      // return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB);
      return isNumber(valA) && isNumber(valB) ? valA - valB : valA.toString().localeCompare(valB);
    }
  };
  let table = $(event.target).parents('table').eq(0);
  let th = $(event.target).parents('th').eq(0);
  if(th.index() === 0){
    let filerows = table.find('.file-row').toArray().sort(comparer(th.index()));
    let folderrows = table.find('.folder-row').toArray().sort(comparer(th.index()));
    this.asc = !this.asc;
    if (!this.asc) {
      filerows = filerows.reverse();
      folderrows = folderrows.reverse();
    }
    for (let i = 0; i < folderrows.length; i++) {
      table.append(folderrows[i]);
    }
    for (let i = 0; i < filerows.length; i++) {
      table.append(filerows[i]);
    }
  } else {
    let filerows = table.find('.file-row').toArray().sort(comparer(th.index()));
    this.asc = !this.asc;
    if (!this.asc) {
      filerows = filerows.reverse();
    }
    for (let i = 0; i < filerows.length; i++) {
      table.append(filerows[i]);
    }
  }
}

function search(){
  this.getObjs({sc: this.sc});
}

function removeList(filename){
  let csdb = this.data.csdb.find((obj) => obj.filename === filename);
  let index = this.data.csdb.indexOf(csdb);
  this.data.csdb.splice(index,1);
  return csdb;
}

async function dispatch(cond = 0){
  const emitName = !cond ? 'dispatchTo' : (cond === 1 ? 'AddDispatchTo' : (cond === 2 ? 'RemoveDispatchTo' : ('dispatchTo')));
  // const csdbs = await this.CbSelector.getCsdbFilenameFromFolderVue(this);
  const csdbs = await this.CB.value();
  console.log(csdbs);
  if(!csdbs) return;
  this.emitter.emit(emitName, csdbs);
}

/**
 * hanya untuk membirukan background table row
*/
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

async function changePath(event){
  // insert filenames to formdata
  const fd = new FormData(event.target)
  const filenames = await this.CB.value();
  fd.set('filename', filenames.join(","))

  // fetch
  const response = await axios({
    route:{
      name: 'api.change_object_path',
      data: fd
    }
  });
  if(!(response.statusText === 'OK')) throw new Error(`Failed to change path of ${filename}`);
  
  // handle changed csdb object
  const path = fd.get('path').toUpperCase();
  const csdbChangedPath = [];
  filenames.forEach((filename) => {
    let csdb = this.removeList(filename);
    this.data.folders.push(path);
    this.data.folders = array_unique(this.data.folders);
    csdb.path = path;
    csdbChangedPath.push(isProxy(csdb) ? toRaw(csdb) : csdb); // nanti dicoba pakai proxy untuk menghemat memori
  });

  // emit
  this.emitter.emit('ChangePathCSDBObjectFromFolder',csdbChangedPath);
}

async function deleteObject(){
  let values = await this.CbSelector.delete(this.CbSelector.cancel); // output array contains filename
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
  this.emitter.emit('DeleteCSDBObjectFromFolder',csdbDeleted);
}

/**
 * untuk emitter.on('Folder-refresh)
 * @param {Object} data 
 */
function refresh(data){
  if(data.path === this.data.current_path){
    this.getObjs({path: data.current_path})
  }
}

export {getObjs, storingResponse, goto, back, clickFolder, clickFilename, 
  sortTable, search, removeList, dispatch, select, changePath, deleteObject, refresh};