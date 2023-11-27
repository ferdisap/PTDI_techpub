/**
 * 
 * @param {*} dmIdent 
 * @param {array} idents contains dmCode, issueInfo, language
 * @param {*} prefix 
 * @param {*} format 
 */
export function resolve_dmIdent(dmIdent = null, idents = [], prefix = 'DMC-', format = '.xml')
{
  console.log('aabb');
  return;
  if(idents === undefined || idents === null || idents.length <= 0){
    if(Array.isArray(dmIdent)){
      dmIdent = dmIdent[0];
    }
    console.log(dmIdent);
    // dmCode = resolve_dmCode(dmIdent->getElemenetByTagName('dmCode')[0], prefix);
    // console.log(dmCode);
  }
  
}