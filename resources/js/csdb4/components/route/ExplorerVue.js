const col2 = ['Editor', 'Folder', 'History'];
const col3 = ['Preview', 'DispatchTo'];

const bottomBarItems = {
  Folder: {
    iconName: 'folder',
    tooltipName: 'Folder',
    isShow: false,
    data: {},
    type: undefined,
  },
  Editor: {
    iconName: 'ink_pen',
    tooltipName: 'Editor',
    isShow: false,
    data: {}
  },
  History: {
    iconName: 'history_edu',
    tooltipName: 'History',
    isShow: false,
    data: {},
  },
  Preview: {
    iconName: 'preview',
    tooltipName: 'Preview',
    isShow: false,
    data: {}
  },
  DispatchTo: {
    iconName: 'local_shipping',
    'tooltipName': 'Dispatch To',
    isShow: false,
    data: {}
  }
}

const colWidth = {
  satu: { portion: 0.15 },
  dua: { portion: 0.5 }, // 0.5 karena col2 dan col3 ada pada satu div yang sama biar memudahkan resizing
  tiga: { portion: 0.5 }, // 0.5 karena col2 dan col3 ada pada satu div yang sama biar memudahkan resizing
}

function col1Width() {
  return `width:${this.colWidth['satu']['portion'] * 100}%`;
}
function col2Width() {
  this.colWidth['dua']['portion'] = (1 - this.colWidth['satu']['portion'] - this.colWidth['tiga']['portion']);
  return `width:${this.colWidth['dua']['portion'] * 100}%`;
}
function col3Width() {
  this.colWidth['tiga']['portion'] = (1 - this.colWidth['satu']['portion'] - this.colWidth['dua']['portion']);
  return `width:${this.colWidth['tiga']['portion'] * 100}%`;
}

// const sizing = function(e, colnum, parentLeft, parentWidth, acuanWidth, startEventXPortion) {
//   console.log('sizing');
//   const endEventXPortion = (e.clientX - parentLeft - parentWidth) / acuanWidth;
//   this.colWidth[colnum].portion = startEventXPortion + endEventXPortion;
//   if (colnum === 'dua') {
//     this.colWidth['tiga']['portion'] = 1 - this.colWidth['dua']['portion'];
//   }
//   top.localStorage.setItem('colWidthExplorer', JSON.stringify(this.colWidth));
// }

function turnOnSizing(event, colnum) {
  // let ex = event.target.getBoundingClientRect().left; // 272.466
  // let ewidth = event.target.getBoundingClientRect().width; // 3.5714
  const parentLeft = event.target.parentElement.getBoundingClientRect().left; //86.741
  const parentWidth = event.target.parentElement.getBoundingClientRect().width; // 189.296
  const acuanWidth = document.querySelector('.explorer-content').getBoundingClientRect().width // 1261.997802734375
  // parentLeft + parentWidth - ex - ewidth // -0.000002384185791015625
  const startEventXPortion = parentWidth / acuanWidth // 0.1499977849326281
  const sizing = (e) => {
    const endEventXPortion = (e.clientX - parentLeft - parentWidth) / acuanWidth;
    this.colWidth[colnum].portion = startEventXPortion + endEventXPortion;
    if (colnum === 'dua') {
      this.colWidth['tiga']['portion'] = 1 - this.colWidth['dua']['portion'];
    }
    top.localStorage.setItem('colWidthExplorer', JSON.stringify(this.colWidth));
  }
  document.addEventListener('mousemove', sizing);
  document.addEventListener('mouseup', this.turnOffSizing.bind(this, sizing), { once: true });
}
function turnOffSizing(callback) {
  document.removeEventListener('mousemove', callback, false)
}

/**
 * if 'satu' then it will be charge column two then three.
 * if 'dua' then it will charge column three.
 * if 'tiga' then it will charge column two.
 * @param {String} colnum 'satu', 'dua', 'tiga'
 * @param {Integer} size less than 1 
 */
function colSize(data = {}){
  switch (data.colnum) {
    case 'satu':
      if(data.size >= 1) {
        this.colWidth.satu.portion = 1;
        this.colWidth.dua.portion = 0;
        this.colWidth.tiga.portion = 0;
      } 
      else if(((data.size + this.colWidth.dua.portion) > 1)){
        this.colWidth.satu.portion = (1 - this.colWidth.dua.portion);
        this.colWidth.tiga.portion = 0;
      }
      else if(((data.size + this.colWidth.tiga.portion) > 1)){
        this.colWidth.satu.portion = (1 - this.colWidth.tiga.portion);
        this.colWidth.dua.portion = 0;
      }
      else {
        this.colWidth.satu.portion = data.size;
      }
      break;
    case 'dua':
      data.size = (data.size + this.colWidth.satu.portion > 1 ? (1 - this.colWidth.satu.portion) : data.size);
      this.colWidth.dua.portion  = data.size;
      this.colWidth.tiga.portion  = (1 - this.colWidth['satu']['portion'] - data.size);
      break;
    case 'tiga':
      data.size = (data.size + this.colWidth.satu.portion > 1 ? (1 - this.colWidth.satu.portion) : data.size);
      this.colWidth.tiga.portion  = data.size;
      this.colWidth.dua.portion  = (1 - this.colWidth['satu']['portion'] - data.size);
      break;
  }
}

export { bottomBarItems, colWidth, col1Width, col2Width, col3Width, turnOnSizing, turnOffSizing, colSize, col2, col3 };