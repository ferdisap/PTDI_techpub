

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
      let data = $(`#${targetId}`).mouseout().data('maphilight') || {};
      data.alwaysOn = !data.alwaysOn;
      $(`#${targetId}`).data('maphilight', data).trigger('alwaysOn.maphilight');      
    }
  },
  icnDetail(src, containerId = 'icn-detail-container'){
    let filename = src.replace(/.+(?=ICN[\w\-_.]+$)/, '');
    let contentType = document.querySelector('.icnMetadataFile .iiit51').innerText;
    if(contentType.includes('image')){
      let img = `<img class="map" src="${src}" usemap="#${filename}"/>`;
      $(`#${containerId}`).html(img);
      console.log($('.map'));
      $('.map').maphilight();
    }
  },
};

export default References;
