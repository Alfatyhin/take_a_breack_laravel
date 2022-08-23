/* загрузка страницы */

let deliveryMethodInputs = document.querySelectorAll('.order__deliveryMethodField input');

let fieldsPhone = document.querySelectorAll('.inputCall');
let fieldsDates = document.querySelectorAll('.dateMask');
// let fieldsDate = document.querySelectorAll('.inputDate');
let textFields = document.querySelectorAll('input[type="text"], textarea');

let preloader = document.querySelector('.preloader');

window.onload = function() {

  loadPage();

  /* отключаем фокус с элементов бургер меню */
  disableFocusForAll(burgerMenuLinks);

  if (document.readyState === "complete") {
    if (preloader !== null) {
      preloader.classList.add("blockHide");
    }
  }
}

/****************************************/


function deleteElement(delElem) {
  if (delElem !== null) {
    delElem.parentNode.removeChild(delElem);
  }
}


/* показываем/скрываем список пункта "десерты" в шапке сайта */

let dessertBtn = document.querySelector('.header__navItem--dessert');
let dessertList = document.querySelector('.header__navSelectList--dessert');
let dessertListIsOpen = false;

function hideDessertList() {
  dessertList.classList.remove('showBlock');
  dessertBtn.classList.remove('showBlock');
  dessertListIsOpen = false;
}

function showDessertList() {
  dessertList.classList.add('showBlock');
  dessertBtn.classList.add('showBlock');
  dessertList.style.left = (dessertBtn.offsetLeft - ((dessertList.offsetWidth - dessertBtn.offsetWidth) / 2))  + "px";
  dessertListIsOpen = true;
}

if (dessertBtn !== null) {
  dessertBtn.addEventListener('click', function (e) {
    e.preventDefault();

    if (dessertListIsOpen) {
      hideDessertList();
    } else {
      showDessertList();
    }
  })
}


/* закрыть меню при клике вне его области */
document.onclick = function (e) {
  if (dessertListIsOpen) {
    if ( (e.target.parentElement.parentElement.parentElement.classList.item(0) !== 'header__navSelectList') &&
         (e.target.parentElement !== dessertBtn) ) {
      hideDessertList();
    }
  }
};


/* закрыть меню при прокрутки страницы */
window.onscroll = function() {
  if (dessertListIsOpen) {
    hideDessertList();
  }
}

/*************************************************************/


function clickOnProdType(prodType) {
  let headerNameField = document.querySelector('.select__headerItem[data-type="' + prodType + '"]');

  if (headerNameField === null) {
    sessionStorage.setItem('productType', prodType);
  } else {
    document.querySelector('.select__headerItem[data-type="' + prodType + '"]').click();
  }
}


/* включить фильтр при выборе десерта в меню шапки сайта*/
let dessertsMenuItemsHeader = document.querySelectorAll('.header__navSelectList--dessert a');

for (let i=0; i<dessertsMenuItemsHeader.length; i++) {
  dessertsMenuItemsHeader[i].addEventListener('click', function(e) {
    let prodType = e.target.getAttribute("data-type");

    clickOnProdType(prodType);
  })
}
/***********************************************/


/* включить фильтр при выборе десерта в меню подвала сайта */
let dessertsMenuItemsFooter = document.querySelectorAll('.footer__menu--dessert .footer__menuBody a');

for (let i=0; i<dessertsMenuItemsFooter.length; i++) {
  dessertsMenuItemsFooter[i].addEventListener('click', function(e) {
    let prodType = e.target.getAttribute("data-type");

    clickOnProdType(prodType);
  })
}

/***********************************************/


/* счетчик выбранного товара */

let btnCountMin;
let btnCountMax;
let totalCountFields;
let pricesProduct;
let totalProductList = document.querySelector('.listProduct__itemAllTotal');
let totalProductListWithPromo = document.querySelector('.listProduct__itemAllTotalPromo');
let minCount = 1;
let isMouseDown = false;
let speedChangeCount = 200;
let timeIsTicking = false;
let totalCount = 0;

function changeTotalProductListWithPromo() {

  let totalPromo = 0;
  let totalAllPromo = 0;
  let pricesPromo = document.querySelectorAll('.listProduct__itemPromoPrice');
  let unitsPromo = document.querySelectorAll('.listProduct__itemPromoUnit');
  let promoTotalRow = document.querySelector('tfoot tr:last-child');
  let promoOrderNotActivated = document.querySelector('.order__promoItem:first-child');

  if (pricesPromo.length === 0) {
    promoTotalRow.classList.remove('showBlock');
    promoOrderNotActivated.classList.add('showBlock');
  } else {
    for (let i = 0; i < pricesPromo.length; i++) {

      if (unitsPromo[i].textContent.toString() === '%' ) {
         totalPromo = (Number(totalProductList.textContent) * Number(pricesPromo[i].textContent) / 100).toFixed(1);
      } else {
        totalPromo += Number(pricesPromo[i].textContent);
      }

      totalAllPromo += totalPromo;
    }
    promoTotalRow.classList.add('showBlock');
    promoOrderNotActivated.classList.remove('showBlock');
  }

  totalProductListWithPromo.textContent = (Number(totalProductList.textContent) - Number(totalAllPromo)).toString();

  getPremiumPrice();

  setTotalOrder();
}

function changeTotalPriceProductInCart(idProduct, countProduct) {

  let totalPriceProduct = document.querySelector('.listProduct__itemTotal[data-productId="' + idProduct + '"]');

  if (totalPriceProduct !== null) {

    let priceProduct = document.querySelector('.listProduct__itemPrice[data-productId="' + idProduct + '"]');

    let oldTotalPriceProduct = Number(totalPriceProduct.textContent);
    let newTotalPriceProduct = Number(countProduct) * Number(priceProduct.textContent);
    let newTotalProductList = Number(totalProductList.textContent) - oldTotalPriceProduct + newTotalPriceProduct;

    totalPriceProduct.textContent = newTotalPriceProduct.toString();
    totalProductList.textContent = newTotalProductList.toString();

    changeTotalProductListWithPromo();

  }

}

function changeCount(minMax, btnMinMax) {

  if (timeIsTicking === false) {

    timeIsTicking = true;

    let timerId = setInterval(function () {

      let idProduct = btnMinMax.getAttribute('data-productId');
      let totalCountField = document.querySelector('.productCount__countNumber[data-productId="' + idProduct + '"]');
      let btnMin = document.querySelector('.productCount__countMin[data-productId="' + idProduct + '"]');

      totalCount = Number(totalCountField.textContent);

      if (minMax === "min") {
        if ( totalCount > minCount ) {
          totalCount--;
        }

        if ( totalCount === minCount ) {
          btnMin.classList.add('productCount__countMin--disabled');
        }
      }

      if (minMax === "max") {
        totalCount++;
        btnMin.classList.remove('productCount__countMin--disabled');
      }

      totalCountField.textContent = totalCount.toString();

      saveCountInCart(btnMinMax.getAttribute('data-productid'), totalCount.toString());

      changeTotalPriceProductInCart(idProduct, totalCount);

      timeIsTicking = false;

      if (isMouseDown === false) {
        clearInterval(timerId)
      }

    }, speedChangeCount);

  }

}


/* сохраняем новое количество в корзине, если товар там есть */
function saveCountInCart(productId, newCount) {

  /* если товар есть в корзине, заносим кол-во выбранного товара */
  let cartItem = sessionStorage.getItem(productId);

  if (cartItem !== null) {

    let numSep = 0;
    let firstPartyCartItem = "";
    let lastPartyCartItem = "";

    for (let i=0; i < cartItem.length; i++) {

      if (cartItem[i] === '|') {
        numSep++;

        if (numSep === 3) {
          firstPartyCartItem = cartItem.substring(0, i+1);
        }

        if (numSep === 4) {
          lastPartyCartItem = cartItem.substring(i);
        }

      }

    }

    cartItem = firstPartyCartItem + newCount + lastPartyCartItem;

    sessionStorage.setItem(productId, cartItem);
  }
  /************************************************/

}
/*************************************************************/


function btnChangeCount() {

  if (btnCountMin !== null) {

    for (let i = 0; i < btnCountMin.length; i++) {

      btnCountMin[i].addEventListener('mousedown', function (e) {

        isMouseDown = true;

        changeCount("min", e.target);

      })

      btnCountMin[i].addEventListener('mouseup', function () {

        isMouseDown = false;

      })

      btnCountMin[i].addEventListener('mouseout', function () {

        isMouseDown = false;

      })

    }

  }

  if (btnCountMax !== null) {

    for (let i = 0; i < btnCountMax.length; i++) {

      btnCountMax[i].addEventListener('mousedown', function (e) {

        isMouseDown = true;

        changeCount("max", e.target);

      })

      btnCountMax[i].addEventListener('mouseup', function () {

        isMouseDown = false;

      })

      btnCountMax[i].addEventListener('mouseout', function () {

        isMouseDown = false;

      })

    }

  }
}

/************************************************/


/* удаление промо из корзины */

function delPromoFromProductList() {

  let promoDelBtns = document.querySelectorAll('.listProduct__promoDelBtn');

  for (let i = 0; i < promoDelBtns.length; i++) {

    promoDelBtns[i].addEventListener('click', function (e) {

      let promoId = e.target.getAttribute('data-promoId');

      deleteElement(document.querySelector('tr[data-promoId="' + promoId + '"]'));
      deleteElement(document.querySelector('.order__promoItem[data-promoId="' + promoId + '"]'));
      deleteElement(document.querySelector('.order__promoPrice[data-promoId="' + promoId + '"]'));

      changeTotalProductListWithPromo();

    });

  }

}

/*******************************************************/


/* Бургер */

let burgerIcon = document.querySelector('.header__burgerIcon');
let burgerClose = document.querySelector('.header__burgerClose');
let burgerContainer = document.querySelector('.header__burgerContainer');
let burgerMenuItems = document.querySelectorAll('.header__burgerMenu li a');
let burgerMenuLinks = document.querySelectorAll('.header__burgerContainer a');

function closeBurgerMenu() {
  burgerIcon.classList.remove('showBlock');
  burgerContainer.classList.remove('showBlock');
  disableFocusForAll(burgerMenuLinks);
}

function disableFocusForAll(elements) {
  if (elements.length !== 0) {
    for (let i=0; i<elements.length; i++) {
      elements[i].setAttribute('tabindex', "-1");
    }
  }
}

function enableFocusForAll(elements) {
  if (elements.length !== 0) {
    for (let i=0; i<elements.length; i++) {
      elements[i].removeAttribute('tabindex');
    }
  }
}

if (burgerIcon !== null) {
  burgerIcon.addEventListener('click', function (e) {
    e.preventDefault();

    burgerIcon.classList.add('showBlock');
    burgerContainer.classList.add('showBlock');

    enableFocusForAll(burgerMenuLinks);

  })
}

if (burgerClose !== null) {
  burgerClose.addEventListener('click', function (e) {
    e.preventDefault();

    closeBurgerMenu();
  })
}

window.onresize = function() {
  if (burgerContainer.classList.contains('showBlock')) {
    closeBurgerMenu();
  }
}

if (burgerMenuItems.length !== 0) {
  for (let i = 0; i < burgerMenuItems.length; i++) {
    burgerMenuItems[i].addEventListener('click', () => {
      closeBurgerMenu();
    })
  }
} else {
  console.log('не найдены пункты бургер меню');
}

/******************************************************************************************************/


/* попап окно "цены и зоны доставки" */

let areaAndPriceBtn = document.querySelectorAll('.areaAndPriceBtn');
let popupAreasInfoCloseBtn = document.querySelector('.popupAreasInfo__closeBtn');
let popupAreasInfo = document.querySelector('.popupAreasInfo');

if (areaAndPriceBtn.length !== 0) {
  for (let i=0; i<areaAndPriceBtn.length; i++) {
    areaAndPriceBtn[i].addEventListener('click', function () {
      popupAreasInfo.classList.add('showBlock');
    });
  }
}

if (popupAreasInfoCloseBtn !== null) {
  popupAreasInfoCloseBtn.addEventListener('click', function() {
    popupAreasInfo.classList.remove('showBlock');
  });
}

/***********************************/


/* валидация полей формы */

let pageForms = document.querySelectorAll('form');

function phoneIsVerified(phoneField) {
  let regExpPhone = '\\+\\d\\d\\d \\d\\d\\d-\\d\\d\\d-\\d\\d\\d[0-9_]';
  let regPhone = new RegExp('\\+\\d\\d\\d \\d\\d-\\d\\d\\d-[0-9_]{3,4}');

  return (phoneField.value.search(regExpPhone) !== -1);
}

function analyzeValueField(form, className, condition, elementClass=null, elementFocus=null) {

  let result = false;
  let validationsFields = form.querySelectorAll('.' + className);

  if (validationsFields !== null) {
    for (let j=0; j<validationsFields.length; j++) {

      let element = validationsFields[j];
      if (elementClass !== null) {
        element = elementClass(validationsFields[j]);
      }

      if ( !condition(validationsFields[j]) ) {

        if (elementFocus === null) {
          validationsFields[j].focus();
        } else {
          elementFocus(validationsFields[j]).focus();
        }

        element.classList.add('errorSend');

        result = true;

      } else {

        element.classList.remove('errorSend');

      }
    }
  }

  return result;
}

if (pageForms !== null) {
  for (let i=0; i<pageForms.length; i++) {
    pageForms[i].addEventListener('submit', (e) => {


      let haveEmptyTextFields = false;
      let haveEmptyPhoneFields = false;

      /*проверяем текстовые поля*/
      haveEmptyTextFields = analyzeValueField( e.target,'validateText', (field) => (field.value !== "") );

      /*проверяем телефон*/
      haveEmptyPhoneFields = analyzeValueField( e.target,'validatePhone', (field) => (phoneIsVerified(field)) );

      if (!haveEmptyTextFields && !haveEmptyPhoneFields) {

        sendForm(e);
      } else {
        haveError();
          e.preventDefault();
      }

    });
  }
}

/************************************************************/

function setObjToLocalStorage(name, obj) {
    let str = JSON.stringify(obj);
    localStorage.setItem(name, str);
}
function getObjToLocalStorage(name) {
    let str = localStorage.getItem(name);
    if (str) {
        var obj = JSON.parse(str);
    } else {
        var obj = false;
    }
    return obj;
}
function isObjectEmpty(object) {
    for (let key in object) {
        if (object.hasOwnProperty(key)) {
            return false;
        }
        return true;
    }
}
function isObjectNoEmpty(object) {
    for (let key in object) {
        if (object.hasOwnProperty(key)) {
            return true;
        }
        return false;
    }
}

var products_cart = getObjToLocalStorage('products_cart');

checkProduct_cart(products_cart);


function checkProduct_cart(products_cart) {
    var cart_count = 0;
    if (isObjectNoEmpty(products_cart)) {
        console.log(products_cart);
        for (k in products_cart) {
            cart_count++;
        }
    }

    console.log(cart_count);
    if (cart_count > 0) {
        $('.header__cart').addClass('visible');
        $('.header__cart .count').text(cart_count);
    } else {
        $('.header__cart').removeClass('visible');
    }
}

var client = getObjToLocalStorage('client');
