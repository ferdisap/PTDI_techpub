import Checkbox from "../../Checkbox";

class ListTreeVueCb extends Checkbox{

  constructor(homeId){
    super(homeId,false);
    this.domObserver = new MutationObserver((mutationList)=>{
      this.register();
    });
    // ### jika tidak pakai table, maka config tambahkan subtree:true, supaya ke detect jika ada perubahan di descendant element
    this.domObserver.observe(document.querySelector('#'+homeId),{childList:true, subtree:true})
  }

  setCbRoomId(event, cbRoom, prev_cbRoom){
    // handle previous cbRoom
    if(prev_cbRoom || (prev_cbRoom = document.getElementById(this.cbRoomId))) {
      if(prev_cbRoom.tagName === 'DETAILS') prev_cbRoom.firstElementChild.style.border = 'none'; // set supaya summary yang di unborder
      else prev_cbRoom.style.border = 'none';
    }

    // handle current cbRoom
    cbRoom = cbRoom ? cbRoom : event.target.closest(`*[cb-room]`);
    if(cbRoom.tagName === 'DETAILS') cbRoom.firstElementChild.style.border = this.cbRoomBorder; // set supaya summary yang di border    
    else cbRoom.style.border = this.cbRoomBorder;
    this.cbRoomId = cbRoom.id;
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

export default ListTreeVueCb;