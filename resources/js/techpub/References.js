

const References = {
  defaultStore: undefined,
  clickImg: () => {
    console.log(event.target);
  },
  to(internalRefTargetType, internalRefId){
    event.preventDefault();
    let irttGoto = ['irtt01', 'irtt02', 'irtt03', 'irtt04', 'irtt05', 'irtt06', 'irtt07', 'irtt08', 'irtt09', 'irtt10', 'irtt14'];
    // internalReference biasa
    if(irttGoto.includes(internalRefTargetType)){
      document.getElementById(internalRefId).scrollIntoView();
    }
    // hotspot
    else if(internalRefTargetType == 'irtt11'){
      this.defaultStore.entityURL = event.target.getAttribute('src');
    }
    // hotspot by icnObject IMF
    else if(internalRefTargetType == 'irtt51'){
      let infoEntityIdent = event.target.getAttribute('infoEntityIdent');
      let targetId = internalRefId;
      
      let src = `/route/get_transform_csdb/?filename=${infoEntityIdent}`;
      this.icnDetail(src);
      infoEntityIdent = infoEntityIdent.replace(/.\w+$/,''); // menghapus extension pada filename
      let data = $(`map[name = ${infoEntityIdent}] #${targetId}`).mouseout().data('maphilight') || {};
      data.alwaysOn = !data.alwaysOn;
      $(`map[name = ${infoEntityIdent}] #${targetId}`).data('maphilight', data).trigger('alwaysOn.maphilight');      
    }
  },
  icnDetail(src, containerId = 'icn-detail-container'){
    this.defaultStore.isOpenICNDetailContainer = true;
    let filename = src.replace(/.+(?=ICN[\w\-_.]+$)/, '');
    let filename_without_extension = filename.replace(/.\w+$/,''); // menghapus extension pada filename
    let contentType = $(`.icnMetadataFile#imf-${filename_without_extension} .iiit51`).text();
    if(contentType.includes('image')){
      let width_in_px = $(`.icnMetadataFile#imf-${filename_without_extension} .iiit52`).text();
      let img = `<img class="map" src="${src}" usemap="#${filename_without_extension}" width="${width_in_px}px"/>`;
      $(`#${containerId}`).html(img);
      $('.map').maphilight();
    }
  },
};

export default References;
