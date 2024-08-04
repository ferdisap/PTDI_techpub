/**
 * HOW TO USE
 * 
 * 1. Construct >> new ContextMenu();
 * 2. set id as per 'menuId' to the element
 * 3. >> ContextMenu.register(menuId)
 * 4. >> ContextMenu.toogle(false, menuId);
 */


class ContextMenu {

  id; // current or last context menu
  collection;
  anchorNode; // berupa target node saat di #trig

  constructor() {
    this.collection = {};
    this.id = '';
    // check di chrome engine berapa banyak listener di register => getEventListeners(document).click[0].listener
    // useCapture = true berarti tidak buble, source: https://stackoverflow.com/questions/7398290/unable-to-understand-usecapture-parameter-in-addeventlistener
    document.addEventListener('click', (event) => {
      if (window.getSelection().type === 'Range') return;
      // event.preventDefault(); // kalau di prevent, misal submit event tidak berjalan jika klik button
      // event.stopPropagation(); // kalau pakai stopPropagation, maka tidak bisa click2 apapun di vue
      switch (event.target.tagName) {
        case 'INPUT':
          break;
        default:
          this.toggle(false, this.id);
          break;
      }
    }, true); // kalau false, menu tidak menutup otomatis
    // },false);
    window.ContextMenu = new Proxy(this,{});
  }

  register(menuId, defaultDisplayOnTrue = 'block', defaultDisplayOnFalse = 'none') {
    this.collection[menuId] = {
      state: false,
      displayOnFalse: defaultDisplayOnFalse,
      displayOnTrue: defaultDisplayOnTrue,
      // disabled: false,
    }

    const menu = document.getElementById(menuId);
    menu.style.position = 'fixed';
    menu.style.zIndex = '100';

    const area = menu.parentElement;
    area.setAttribute('cm-target-id', menuId);
    area.addEventListener('contextmenu', this.#trig.bind(this));

    // console.log(`${menuId} has been registered.`);
  }

  /**
   * jika state TRUE dan id EXIST maka akan mengOFFkan previous menu dan mengONkan current menu id
   * @param {Bool} state 
   * @param {String} id 
   */
  toggle(state = false, id = '') {
    if (state && id) {
      // turnoff previous menu
      this.collection[this.id].state = !state;
      this.display(this.id);
      // turnon current menu
      this.id = id;
      this.collection[this.id].state = state;
      this.display(this.id);
    } else {
      // kalau 'state' TRUE dan 'id' FALSE maka turn ON current this.id. 
      // kalau 'state' FALSE dan 'id' TRUE maka akan turn OFF the id.
      // kalau 'state' FALSE dam 'id' FALSE maka akan turn OFF current ID;
      if (id) this.id = id;
      this.collection[this.id].state = state;
      this.display(this.id);
    }
    // sengaja dibuat pakai setTimeout agar setiap event terkait bisa dijalankan sebeleum document mentup context menu, contohnya fitur copy di helper;
    if(!state) setTimeout(()=>this.anchorNode = '',0);
  }

  display(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = this.#getDisplay(id);
  }

  #getDisplay(id) {
    return (this.collection[id].state ? this.collection[id].displayOnTrue : this.collection[id].displayOnFalse);
  }

  #trig(event) {
    event.preventDefault();
    event.stopPropagation();
    const id = event.target.closest("*[cm-target-id]").getAttribute('cm-target-id');
    this.#positionMenu(event, id);
    this.toggle(true, id);
    this.anchorNode = event.target;
  }

  // position the Context Menu in right position.
  #positionMenu(e, id) {
    const clickCoords = this.#getPosition(e);
    const clickCoordsX = clickCoords.x;
    const clickCoordsY = clickCoords.y;

    const menu = document.getElementById(id);

    const menuWidth = menu.offsetWidth + 4;
    const menuHeight = menu.offsetHeight + 4;

    const windowWidth = window.innerWidth;
    const windowHeight = window.innerHeight;

    if ((windowWidth - clickCoordsX) < menuWidth) {
      menu.style.left = windowWidth - menuWidth + "px";
    } else {
      menu.style.left = clickCoordsX + "px";
    }

    if ((windowHeight - clickCoordsY) < menuHeight) {
      menu.style.top = windowHeight - menuHeight + "px";
    } else {
      menu.style.top = clickCoordsY + "px";
    }
  }

  // get the position of the right-click in window and returns the x and y coordinates
  #getPosition(e) {
    let posX = 0;
    let posY = 0;

    if (!e) var e = window.event;

    if (e.pageX || e.pageY) {
      posX = e.pageX;
      posY = e.pageY;
    }
    else if (e.clientX || e.clientY) {
      posX = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
      posY = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
    }

    return {
      x: posX,
      y: posY,
    };
  }
}

export default ContextMenu;