import Checkbox from "../../Checkbox";
import Randomstring from "randomstring";
import { findAncestor, matchSel } from "../../helper";
import axios from 'axios';

class CommentVueCb extends Checkbox{

  // latestCbRoomQueried = [];
  containerEditorId = '';

  setCbRoomId(event, cbRoom, prev_cbRoom){
    // handle previous cbRoom
    if(prev_cbRoom || (prev_cbRoom = document.getElementById(this.cbRoomId))) {
      if(prev_cbRoom.parentElement.id === this.homeId) prev_cbRoom.querySelector("*[comment-type]").style.border = 'none';
      else prev_cbRoom.style.border = 'none';
      // if(prev_cbRoom.tagName === 'DETAILS') prev_cbRoom.firstElementChild.style.border = 'none'; // set supaya summary yang di unborder
      // else prev_cbRoom.style.border = 'none';
    }

    // handle current cbRoom
    cbRoom = cbRoom ? cbRoom : event.target.closest(`*[cb-room]`);
    if(cbRoom.parentElement.id === this.homeId) cbRoom.querySelector("*[comment-type]").style.border = this.cbRoomBorder;
    else cbRoom.style.border = this.cbRoomBorder;
    this.cbRoomId = cbRoom.id;
  }

  /**
   * Sengaja dibuat lagi karena yang di parent ada preventDefault. fungsi ini kan di jalankan ketika ada event click pada cbRoom atau childnya. oleh karena itu ini untuk menghilangkan preventDefault nya saja. Lagian pushSingle tidak dipakai di Checkbox comment ini
   * @param {*} event 
   */
  pushSingle(event){}

  reply(modalId){
    const cbRoom = this.getCbRoom();
    if (this.containerEditorId) {
      cbRoom.appendChild(document.getElementById(this.containerEditorId));
    } else {
      this.createEditor(cbRoom);
    }

    // ngosongin input yang ada di modal
    document.querySelectorAll(`#${modalId} *[name]`).forEach(input => {
      switch (input.name) {
        case 'parentCommentFilename':
          const ancestorOrSelf = cbRoom.closest("*[cb-room].type-q");
          input.value = (ancestorOrSelf ? ancestorOrSelf.querySelector('input[type="checkbox"]').value : '') ;
          break;
        case 'position': 
          let index = 0;
          const ancestor = findAncestor(cbRoom, "*[cb-room].type-q");
          console.log(ancestor, window.cbRoom = cbRoom);
          if(ancestor) index = [...cbRoom.parentElement.querySelectorAll("*[cb-room]")].indexOf(cbRoom) + 1;
          input.value = index;break;
        case 'commentType': 
          input.value = (cbRoom.closest("*[cb-room].type-q") ? 'i' : 'q');break;
        case 'commentPriorityCode':
          input.value = input.firstElementChild.value; break;
        case 'responseType':
          input.value = input.firstElementChild.value; break;
        case 'remarks':
          input.value = ''; break;
      }
    })
  }

  cancel(){
    document.getElementById(this.containerEditorId).remove();
    this.containerEditorId = '';
  }

  createEditor(cbRoom){
    this.containerEditorId = Randomstring.generate({ 'charset': 'alphabetic' });
    const div = document.createElement('div');
    div.id = this.containerEditorId;
    div.setAttribute('class', 'editor-container');
    cbRoom.appendChild(div);

    div.innerHTML = `<text-editor name="commentContentSimplePara"></text-editor>
    <button id="sendBtn" type="submit" class="material-icons send">send</button>`;
  }
}
export default CommentVueCb;