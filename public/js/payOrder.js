
/* загрузка страницы */
function loadPage() {


}

/****************************/
/* возврат на прошлую страницу */
let payOrderBtnBack = document.querySelectorAll('.payOrder__btnBack');

for (let i=0; i < payOrderBtnBack.length; i++) {
  payOrderBtnBack[i].addEventListener('click', function (e) {
    history.back();
  })
}
/*******************************/

///////////////////////////////////////////////////
// очищение от заказа
sessionStorage.clear();
var products_cart = {};
console.log(products_cart);
setObjToLocalStorage('products_cart', products_cart);
setObjToLocalStorage('client', client);

