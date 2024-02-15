import $ from "jquery";

let selector = undefined;
let message = '';
let alert;
let show = true;

/**
 * create an Alert Object
 * @returns javascript object, contains function for ok, not ok, and wait
 */
function run(){    
  let resolve = undefined;
  let reject = undefined;

  const promise = new Promise((r,j) => {
    resolve = r;
    reject = j;
  })

  function ok(){
    show = false;
    return resolve(1);
  }
  function not_ok(){
    show = false;
    return reject(0);
  }
  function wait(){
    show = true;
    return promise;
  }

  return {ok, not_ok, wait};
}

/**
 * initiate an Alert message
 * sementara ini, dialog tidak di aktifkan. 
 * Jika diaktifkan, maka button 'OK' harus punya attribute 'alert-ok'. 
 * Kalau button 'NOT OK' harus ada attribute 'alert-not-ok'
 * Untuk message: div harus memiliki attribute 'message'
 * @param {Object} conf contains key {message:String}
 * @returns {Object} if dialog box is exist
 * @return {false} if dialog box is none exist
 */
function createAlert(conf = {}){
  let aa = function(){
    // selector = conf.selector ?? undefined;
    message = conf.message ?? '';
    alert = run();
    return {
      show: show,
      message: message,
      button: function(state){
        if(state){
          this.show = false;
          return alert.ok();
        } else {
          this.show = false;
          return alert.not_ok();
        }
      },
      result: alert.wait(),
    }
  }
  return aa();
}

export default createAlert;








// import $ from "jquery";

// let selector = undefined;
// let message = '';
// let alert;

// /**
//  * create an Alert Object
//  * @returns javascript object, contains function for ok, not ok, and wait
//  */
// function Alert(){    
//   let resolve = undefined;
//   let reject = undefined;

//   const promise = new Promise((r,j) => {
//     resolve = r;
//     reject = j;
//   })

//   function ok(){
//     resolve(1);
//   }
//   function not_ok(){
//     reject(0);
//   }
//   function wait(){
//     return promise;
//   }

//   return {ok, not_ok, wait};
// }

// /**
//  * initiate an Alert message
//  * @param {Object} conf contains key {selector:String, message:String}
//  * @returns {Object} if dialog box is exist
//  * @return {false} if dialog box is none exist
//  */
// function createAlert(conf = {}){
//   selector = conf.selector ?? undefined;
//   message = conf.message ?? '';
//   alert = Alert();
  
//   let dialog = selector ? $(selector)[0] : document.querySelector(`dialog`);
//   if(!dialog){
//     return false;
//   }
//   window.dialog = dialog
//   let show = undefined;
//   let hide = undefined;
//   if(dialog.nodeName == 'DIALOG'){
//     show = () => {dialog.open = true};
//     hide = () => {dialog.open = false};
//   } else {
//     show = () => {$(dialog).css('display','')}
//     hide = () => {$(dialog).css('display','none')}
//   }

//   $(dialog).find('*[message]').html(message);
//   let onok = () => {
//     alert.ok();
//     hide();
//     $(dialog).find('*[alert-ok]').off('click', onok);
//   };
//   let onnotok = () => {
//     alert.not_ok();
//     hide();
//     $(dialog).find('*[alert-not-ok]').off('click', onnotok)
//   };
    
//   $(dialog).find('*[alert-ok]').on('click', onok);
//   $(dialog).find('*[alert-not-ok]').on('click', onnotok);
//   show();
  
//   return {
//     message: message,
//     alert: alert,
//     result: alert.wait(),
//   }
// }

// export default createAlert;





