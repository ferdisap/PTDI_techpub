import Randomstring from "randomstring";
/**
 * HOW TO USE
 * 
 * 1. attach attribute 'cb-window' on td or parent of cb
 * 2. attach attribute 'cb-room' on tr or grandparent of cb
 * 3. Instance new Checkbox() with id
 */

class Checkbox{
  
  homeId;
  selectionMode;

  cbRoomId; // current cbRoomId
  display = this.display;
  
  constructor(homeId){
    console.log('constructed');
    this.homeId = homeId;
    this.selectionMode = false;
    // document.addEventListener('click', function(){
      // this.cbRoomId = '';
      // console.log(this.cbRoomId);
    // },true)
  }

  register(){
    // register event to checkbox room
    const cbRooms = document.querySelectorAll(`#${this.homeId} *[cb-room]`);
    cbRooms.forEach(r => {
      r.id = Randomstring.generate({charset:'alphabetic'});
      r.addEventListener('contextmenu', this.setCbRoomId.bind(this),true);
      r.addEventListener('click', this.pushSingle.bind(this),true); // it will be checked if in selectedMode
      this.queryCbWindow(r).style.display = 'none';
    });
  }

  setCbRoomId(event){
    this.cbRoomId = event.target.closest(`*[cb-room]`).id;
    // const cbRoom = document.querySelectorAll(`#${id} *[cb-window] *[cb-room] input[type='checkbox']`);
  }

  /**
   * digunakan untuk event click pada cbRoom element atau <tr>
   * @param {Event} event 
   */
  pushSingle(event){
    event.preventDefault();
    if(this.selectionMode){
      event.stopPropagation(); // jika tidak dalam selection mode maka single click filename/folder listTree tidak ngefek pindah route
      const cbRoom = this.queryCbRoom(event.target);
      const cbWindow = this.queryCbWindow(cbRoom);
      cbWindow.style.display = this.display;      
      const cbTarget = this.queryCbTarget(cbWindow);
      cbTarget.checked = !cbTarget.checked;
      this.cbRoomId = cbRoom.id;
    }
  }

  /**
   * digunakan untuk event click pada context menu
   */
  push(){
    if(this.cbRoomId){
      const cbRoom = document.getElementById(this.cbRoomId);
      const cbWindow = this.queryCbWindow(cbRoom);
      cbWindow.style.display = this.display;
      const cbTarget = this.queryCbTarget(cbWindow);
      cbTarget.checked = !cbTarget.checked;
    }
    this.openSelectionMode();
  }

  pushAll(state){
    document.querySelectorAll(`#${this.homeId} *[cb-window]`).forEach(w => {
      w.style.display = this.display;
      this.queryCbTarget(w).checked = state;
    })
  }

  cancel(){
    this.pushAll(false);
    this.closeSelectionMode();
    this.cbRoomId = ''
  }

  openSelectionMode(){
    document.querySelectorAll(`#${this.homeId} *[cb-window]`).forEach(v => {
      v.style.display = this.display;
      this.selectionMode = true;
    });
  }
  
  closeSelectionMode(){
    document.querySelectorAll(`#${this.homeId} *[cb-window]`).forEach(v => {
      v.style.display = 'none';
      this.selectionMode = false;
    });
  }

  value(){
    let val = [];
    document.querySelectorAll(`#${this.homeId} *[cb-window] input[type="checkbox"]:checked`).forEach(c => {
      val.push(c.value);
    });
    return val;
  }

  // helper function
  queryCbRoom(descendant){
    return descendant.closest('*[cb-room]');
  }
  queryCbWindow(ancestor){
    return ancestor.querySelector('*[cb-window]');
  }
  queryCbTarget(ancestor){
    return ancestor.querySelector('input[type="checkbox"]');
  }
}

class CbListTreeVue extends Checkbox{

  constructor(homeId){
    super(homeId);
  }

  /**
   * jika path di checked, maka akan mengambil seluruh value checkbox didalamnya meskipun cb nya unchecked
   * @returns {Array}
   */
  value(){
    let val = [];
    document.querySelectorAll(`#${this.homeId} *[cb-window] input[type="checkbox"]:checked`).forEach(c => {
      if((!c.value)){
        c.closest('details').querySelectorAll("input[type='checkbox']").forEach(d => {
          val.push(d.value);
        })
      }
      else val.push(c.value);
    });
    return val.filter(v => v && (v != ''));
  }
}

export {Checkbox, CbListTreeVue}