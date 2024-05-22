const rcmcloseevent = new Event("rcm-close");
document.addEventListener("click", (e) => {
  // let button = e.which || e.button;
  // if (button === 1) {}
  document.dispatchEvent(rcmcloseevent);
});
window.onkeyup = function(e){
  if (e.keyCode === 27){
    document.dispatchEvent(rcmcloseevent);
  }
}



class RightClickMenu{
  name= undefined;
  state = 0;
  context= undefined;

  constructor(name, contextMenu, area){
    this.name = name;
    this.context = contextMenu;
    let run = function(event){
      event.preventDefault();
      this.positionMenu();
      this.toggleOn;  
    }
    run = run.bind(this);
    contextMenu.style.display = 'none';
    area.addEventListener("contextmenu", run);
    document.addEventListener("rcm-close", () => {
      this.toggleOff;
    });
  }

  get toggleOn(){
    this.context.style.display = "block";
    return this.state = 1;
  }

  get toggleOff(){
    this.context.style.display = "none";
    return this.state = 0;
  }

  // get the position of the right-click in window and returns the x and y coordinates
  getPosition(e){
    let posX = 0;
    let posY = 0;

    if(!e) var e = window.event;

    if(e.pageX || e.pageY){
      posX = e.pageX;
      posY = e.pageY;
    }
    else if(e.clientX || e.clientY){
      posX = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
      posY = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
    }

    return {
      x: posX,
      y: posY,
    };
  }

  // position the Context Menu in right position.
  positionMenu(e){
    let clickCoords = this.getPosition(e);
    let clickCoordsX = clickCoords.x;
    let clickCoordsY = clickCoords.y;

    let menuWidth = this.context.offsetWidth + 4;
    let menuHeight = this.context.offsetHeight + 4;

    let windowWidth = window.innerWidth;
    let windowHeight = window.innerHeight;

    if (windowWidth - clickCoordsX < menuWidth){
      this.context.style.left = windowWidth - menuWidth + "px";
    } else {
      this.context.style.left = clickCoordsX + "px";
    }

    if (windowHeight - clickCoordsY < menuHeight){
      this.context.style.top = windowHeight - menuHeight + "px";
    } else {
      this.context.style.top = clickCoordsY + "px";
    }
  }
}

export default RightClickMenu;

// /**
//  * 
//  * @returns Object menu
//  */
// function instanceMenu(name, contextMenu){
//   return {
//     name: name,
//     context: contextMenu,
//     state: 0,
//     activeClass: "block",
//     toggle: toggle,
//   }
// }

// export default instanceMenu;