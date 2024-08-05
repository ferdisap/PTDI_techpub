const urlQueryDictionary = {
  bbi: '',
}

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
  },
};

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
function turnOnSizing(event, colnum) {
  // let ex = event.target.getBoundingClientRect().left; // 272.466
  // let ewidth = event.target.getBoundingClientRect().width; // 3.5714
  let parentLeft = event.target.parentElement.getBoundingClientRect().left; //86.741
  let parentWidth = event.target.parentElement.getBoundingClientRect().width; // 189.296
  let acuanWidth = document.querySelector('.explorer-content').getBoundingClientRect().width // 1261.997802734375
  // parentLeft + parentWidth - ex - ewidth // -0.000002384185791015625
  let startEventXPortion = parentWidth / acuanWidth // 0.1499977849326281
  let sizing = (e) => {
    let endEventXPortion = (e.clientX - parentLeft - parentWidth) / acuanWidth;
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


export { bottomBarItems, colWidth, col1Width, col2Width, col3Width, turnOnSizing, turnOffSizing};