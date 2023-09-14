// need bootstrap css
function hideShow(el, displayClass = 'd-none'){

  (target = el.nextElementSibling).classList.contains(displayClass) ? target.classList.remove(displayClass) : target.classList.add(displayClass);
}