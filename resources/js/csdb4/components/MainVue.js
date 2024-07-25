// available action towards CSDB
/**
 * @param {*} data; if data.filenames is array then it'll be joined, otherwise data.filename is choosen 
 */
function joinFilename(data){
  let filenames = data.filenames || data.filename;
  if (Array.isArray(filenames)) filenames = filenames.join(', ');
  return filenames;
}

async function deleteCSDBs(data){
  if(!data) return;
  let filenames = this.joinFilename(data);
  if (!(await this.$root.alert({ name: 'beforeDeleteCsdbObject', filename: filenames }))) {
    return;
  }
  let response = await axios({
    route: {
      name: 'api.delete_objects',
      data: {filenames: filenames},
    }
  });
  this.emitter.emit('DeleteMultipleCSDBObject', response.data.models);
}

async function commitCSDBs(data){
  if(!data) return;
  let filenames = this.joinFilename(data);
  if (!(await this.$root.alert({ name: 'beforeCommitCsdbObject', filename: filenames }))) {
    return;
  }
  let response = await axios({
    route: {
      name: 'api.commit_objects',
      data: {filenames: filenames},
    }
  });
  this.emitter.emit('CommitMultipleCSDBObject', response.data.models);
}

/**
 * data must contain filename
 * @param {Object} data 
 */
function getCSDBObjectModel(data){
  if(data.filename){
    clearTimeout(this.to);
    this.to = setTimeout(()=>{
      const worker = this.createWorker('WorkerGetCsdbModel.js');
      if(worker){
        let route = this.techpubStore.getWebRoute('api.get_object_model', {filename: data.filename});
        worker.onmessage = (e) => {
          this.techpubStore.currentObjectModel = e.data.model;
          worker.terminate();
        }
        worker.postMessage({route: route});
      }
    },1000);
  }
}

export {joinFilename, deleteCSDBs, commitCSDBs, getCSDBObjectModel};