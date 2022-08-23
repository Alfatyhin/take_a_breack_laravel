let orderTextFields = document.querySelectorAll('.fastOrder .blockTextField');
let orderRadioBtns = document.querySelectorAll('.fastOrder__formItem .blockRadio:first-child input');

/* при загрузке страницы */

function loadPage() {

  /* очищаем значения полей выбора */
  for (let i=0; i<orderTextFields.length; i++) {
    orderTextFields[i].value = "";
  }
  /******************************************/

  /* выбор радио по умолачнию */
  for (let i=0; i<orderRadioBtns.length; i++) {
    orderRadioBtns[i].checked = true;
  }
  /******************************/

  //маска для номера телефона
  let maskOptions = {
    mask: '+972 00-000-0000',
    lazy: false
  }

  for (let i = 0; i <= fieldsPhone.length-1; i++) {
    new IMask(fieldsPhone[i], maskOptions);
  }
  //**************************

}

/**********************************************/


/* выпадающий список */

let selectFields = document.querySelectorAll('.fastOrder__select input');
let selectList = document.querySelector('.fastOrder__selectList');
let selectListValues = document.querySelectorAll('.fastOrder__selectList a');
let selectListDessertName = document.querySelectorAll('.fastOrder__formItem--dessertName .fastOrder__selectList a');
let selectListDessertSize = document.querySelectorAll('.fastOrder__formItem--dessertSize .fastOrder__selectList a');
let selectDessertTypeField = document.querySelector('.fastOrder__formItem--dessertType .blockTextField');
let selectDessertNameField = document.querySelector('.fastOrder__formItem--dessertName .blockTextField');
let selectDessertSizeField = document.querySelector('.fastOrder__formItem--dessertSize .blockTextField');
let selectDessertDateField = document.querySelector('.fastOrder__formItem--dessertDate .blockTextField');
let activeField = null;

function closeList() {
  activeField.parentElement.classList.remove('showBlock');
  activeField = null;
}

for (let i=0; i < selectFields.length; i++) {

  selectFields[i].addEventListener('click', function (e) {

    if (activeField !== null) {

      if (activeField === e.target) {
        closeList(activeField)
      } else if (activeField !== e.target) {
        closeList(activeField)
        activeField = e.target;
        activeField.parentElement.classList.add('showBlock');
      }

    } else {
      activeField = e.target;
      activeField.parentElement.classList.add('showBlock');
    }

  })

}

/* закрыть список при клике вне его области */
document.onclick = function (e) {
  if (activeField !== null) {
    if ( (e.target !== activeField) ) {
      closeList(activeField)
    }
  }
};


/* выбор пункта списка */
for (let i=0; i < selectListValues.length; i++) {
  selectListValues[i].addEventListener('click', function(e) {
    e.preventDefault();

    let tmpListValues = e.target.parentElement.parentElement.querySelectorAll('a');
    for (let j=0; j < tmpListValues.length; j++) {
      tmpListValues[j].classList.remove('active');
    }

    activeField.value = e.target.textContent;

    if (e.target.hasAttribute('data-type')) {
      activeField.setAttribute('data-type', e.target.getAttribute('data-type')); //тип товара
    }

    if (e.target.hasAttribute('data-valType')) {
      activeField.setAttribute('data-valType', e.target.getAttribute('data-valType')); //наименование товара
    }

    if (e.target.hasAttribute('data-img')) {
      activeField.setAttribute('data-img', e.target.getAttribute('data-img')); //картинка товара
    }

    if (e.target.hasAttribute('data-price')) {
      activeField.setAttribute('data-price', e.target.getAttribute('data-price')); //стоимость товара
    }

    if (e.target.hasAttribute('data-unit')) {
      activeField.setAttribute('data-unit', e.target.getAttribute('data-unit')); //валюта товара
    }

    if (e.target.hasAttribute('data-size')) {
      activeField.setAttribute('data-size', e.target.getAttribute('data-size')); //размер товара
    }

    if (e.target.hasAttribute('data-id')) {
      activeField.setAttribute('data-id', e.target.getAttribute('data-id')); //id товара, id размера
    }

    if (e.target.hasAttribute('data-valSize')) {
      activeField.setAttribute('data-valSize', e.target.getAttribute('data-valSize')); //размер товара
    }

    e.target.classList.add('active');

    /* фильтрация списков */

    //привязка наименований десертов к типу десерта
    if (e.target.hasAttribute('data-type')) {
      for (let i=0; i < selectListDessertName.length; i++) {
        if (selectListDessertName[i].getAttribute('data-valType').indexOf(e.target.getAttribute('data-type')) !== -1 ) {
          selectListDessertName[i].parentElement.classList.remove("blockHide");
        } else {
          selectListDessertName[i].parentElement.classList.add("blockHide");
        }
      }

      if (selectDessertNameField.getAttribute('data-valType').indexOf(e.target.getAttribute('data-type')) === -1 ) {
        selectDessertNameField.value = "";
      }

    }

    //привязка размера десерта к наименованию десерта
    if (e.target.hasAttribute('data-size')) {

      let listSize = e.target.getAttribute('data-size');
      let sizeHaveInField = false;

      for (let i = 0; i < selectListDessertSize.length; i++) {
        if (listSize.indexOf(selectListDessertSize[i].getAttribute('data-valSize')) !== -1) {
          selectListDessertSize[i].parentElement.classList.remove("blockHide");

          if (selectDessertSizeField.getAttribute('data-valSize') === selectListDessertSize[i].getAttribute('data-valSize')) {
            sizeHaveInField = true;
          }
        } else {
          selectListDessertSize[i].parentElement.classList.add("blockHide");
        }
      }

      if (sizeHaveInField === false) {
        selectDessertSizeField.value = "";
      }

    }

    /*****************************/

  })
}

/***********************************/


/*передача данных с формы быстрого заказа*/

function sendForm(e) {

  /*инфо по клиенту*/
  let clientName = document.querySelector('input[name="clientName"]').value;
  let clientPhone = document.querySelector('input[name="phone"]').value;

  let deliveryFields = document.querySelectorAll('input[type="radio"][name="delivery"]');
  let deliveryMethod;
  for (let i=0; i<deliveryFields.length; i++) {
    if (deliveryFields[i].checked) {
      deliveryMethod = deliveryFields[i].id;
    }
  }

  sessionStorage.setItem('fastOrder', clientName + '|' + clientPhone + '|' + deliveryMethod);
  /**************************************************************/


  /*инфо по выбранному товару*/
  let productNameField = document.querySelector('input[name="selectNameProduct"]');
  let productSizeField = document.querySelector('input[name="selectSize"]');

  let productName = productNameField.value;
  let productSize = productSizeField.value;

  if (productName !== "" && productSize !== "") {

    let idProduct = productNameField.getAttribute('data-id');
    let sizeIndex = productNameField.getAttribute('data-type').toString() +
      productSizeField.getAttribute('data-id').toString();

    let arPrice = productNameField.getAttribute('data-price').split(" ");
    let arSize = productNameField.getAttribute('data-size').split(" ");
    let indPrice;
    for (let i = 0; i < arSize.length; i++) {
      if (productSizeField.getAttribute('data-valSize') === arSize[i]) {
        indPrice = i;
        break;
      }
    }
    let curPrice = arPrice[indPrice];

    let countProd = 1;

    let imgProd = productNameField.getAttribute('data-img');
    let unitProd = productNameField.getAttribute('data-unit');

    //заносим название, цену и кол-во выбранного товара в хранилище, для передачи в корзину
    sessionStorage.setItem(sizeIndex + "-" + idProduct,
      productName + "|" +
      productSize + "|" +
      curPrice + "|" +
      countProd + "|" +
      imgProd + "|" +
      unitProd);
  }
  /*******************************************/


  window.location.href="./cart.html";
}

function haveError() {

}

/*******************************************/
