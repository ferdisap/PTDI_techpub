import Checkbox from "../../Checkbox";

class ListTreeVueCb extends Checkbox{

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

export default ListTreeVueCb;