/* настройки слайдера "дополнительный заказ" */

$('.additional__slider').slick({
  slidesToShow: 6,
  slidesToScroll: 6,
  dots: true,
  infinite: false,
  appendArrows: $('.additional__sliderPagination'),
  appendDots: $('.additional__sliderPagination'),
  responsive: [
    {
      breakpoint: 1025,
      settings: {
        slidesToShow: 5,
        slidesToScroll: 5,
      }
    },
    {
      breakpoint: 851,
      settings: {
        slidesToShow: 4,
        slidesToScroll: 4,
      }
    },
    {
      breakpoint: 601,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 3,
      }
    },
    {
      breakpoint: 376,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 2,
      }
    },
  ]
});

/********************************************/

let methodPayBtns = document.querySelectorAll('.order__methodPayField input[type="radio"]');

let priceAreasField = document.querySelector('.order__clientInfoPrice .order__clientInfoCarPrice');

let arAreasAndPrices= [];

/* при загрузке страницы */
function loadPage() {

    /* заполняем корзину */
    sessionStorageToArray();

    let productTable = document.querySelector('.listProduct__table tbody');
    if (productTable !== null) {
        for (let i = 0; i < arCart.length; i++) {
            productTable.insertAdjacentHTML('afterbegin', createProductInTable(i));
        }
    }

    delBtnProductList(); //вешаем клик на кнопку удаления товара
    /***********************************/


    /* заполняем данные по клиенту из формы быстрого заказа */
    if (arClientInfo !== null) {
        document.querySelector('.order__clientInfo input[name="clientName"]').value = arClientInfo[0];
        document.querySelector('.order__clientInfo input[name="phone"]').value = arClientInfo[1];
        document.getElementById(arClientInfo[2]).checked = true;
    }
    /*******************************************************/


    /* установка способа доставки по умолчанию */

    if (arClientInfo === null) {
        document.getElementById('idDelivery').checked = true;
    }
    /*****************************************************/


    /* активируем поля в зависимости от способа доставки */
    if (document.getElementById('idDelivery').checked) {
        selectDeliveryNote("idDelivery");

        visibleBlockSelf(false);
        visibleClientInfoFields(true);
    } else {
        selectDeliveryNote("idSelf");

        visibleBlockSelf(true);
        visibleClientInfoFields(false);
    }
    /*******************************************************/


    /* кнопка "минус" в счетчике корзины неактивна, если количество товара равно минимуму */
    totalCountFields = document.querySelectorAll('.productCount__countNumber');
    pricesProduct = document.querySelectorAll('.listProduct__itemPrice');
    btnCountMin = document.querySelectorAll('.productCount__countMin');
    btnCountMax = document.querySelectorAll('.productCount__countMax');

    btnChangeCount(); //вешаем клик на кнопку изменения количества товара

    if (totalCountFields !== null) {
        for (let i = 0; i < totalCountFields.length; i++) {
            if (Number(totalCountFields[i].textContent) === minCount) {
                btnCountMin[i].classList.add('productCount__countMin--disabled');
            }
        }
    }

    /*******************************************/

    /* установка чаевых по умолчанию */
    let premiumRadioBtn = document.querySelector('[name="premium"][value="10"]');

    if (premiumRadioBtn !== null) {
        premiumRadioBtn.checked = true;
        premiumPrice.textContent = getPriceFromTotal(Number(premiumRadioBtn.value), Number(totalProductListWithPromo.textContent));
    }
    /***********************************/


    /* загрузка списка зон доставок */
    let areasAndPricesList = document.querySelectorAll('.order__listAreasPrices ul li');

    for (let i = 0; i < areasAndPricesList.length; i++) {
        arAreasAndPrices.push(
            (areasAndPricesList[i].textContent.toLowerCase() + "|" +
                areasAndPricesList[i].getAttribute('data-order') + "|" +
                areasAndPricesList[i].getAttribute('data-price')).split("|")
        )
    }
    /**********************************/


    /*проверка города доставки*/
    searchAreasAndPrice(fieldCity[0].value);
    /****************************/


    /* расчет итоговой суммы продуктов в корзине */

    if (pricesProduct.length !== 0) {
        let curTotalProductList = 0;

        for (let i = 0; i < pricesProduct.length; i++) {
            curTotalProductList = curTotalProductList + Number(pricesProduct[i].textContent) * Number(totalCountFields[i].textContent);
        }

        totalProductList.textContent = curTotalProductList.toString();

        changeTotalProductListWithPromo();
    } else {
        setTotalOrder();
    }
    /************************************/


    /* удаление промокода из списка продуктов */
    delPromoFromProductList();
    /*****************************************/


    /* способ оплаты по умолчанию */
    if (!methodPay) {
        if (methodPayBtns.length !== 0) {
            methodPayBtns[0].checked = true;
        }
    }
  /******************************/

    //маска для номера телефона
    let maskOptions = {
        mask: '+972 000-000-0000',
        lazy: false
    }

    for (let i = 0; i <= fieldsPhone.length-1; i++) {
        new IMask(fieldsPhone[i], maskOptions);
    }
    //**************************

    $(function() {

        $('input[name="phone"]').on('input',function () {
            let number = $(this).val();
            console.log(number);
        });



        var products_cart = getObjToLocalStorage('products_cart');

        $('.listProduct__table tr').each(function () {

            var cart_key = $(this).attr('data-productid');

            if (!products_cart[cart_key] && cart_key) {
                $(this).remove();
            }

        });

        for (k in products_cart) {
            var item = $('.listProduct__table tr[data-productid="' + k + '"]').text();
            if (!item) {
                delete (products_cart[k]);

            }
        }
        checkProduct_cart(products_cart);
        setObjToLocalStorage('products_cart', products_cart);
    });

}
/************************/


//прокрутка страницы до нужного блока при выборе пункта меню
const anchors = document.querySelectorAll('a[href^="#anchor"]')

for (let i = 0; i < anchors.length; i++) {
  let anchor = anchors[i];
  anchor.addEventListener('click', function (e) {
    e.preventDefault()

    const blockID = anchor.getAttribute('href').substr(1)
    document.getElementById(blockID).scrollIntoView({
      behavior: 'smooth',
      block: 'start'
    })
  })
}
//**************************************************************************


/* конвертируем sessionStorage в массив */
let arCart;
let arClientInfo = null;

function sessionStorageToArray() {
  arCart = [];

  for (let i=0; i<sessionStorage.length; i++) {
    if (sessionStorage.key(i) === "fastOrder") {
      arClientInfo = (sessionStorage.getItem(sessionStorage.key(i))).split("|");
    } else if (sessionStorage.key(i) !== "productType")  {
      arCart.push((sessionStorage.key(i) + "|" + sessionStorage.getItem(sessionStorage.key(i))).split("|"));
    }
  }

  sessionStorage.removeItem('fastOrder');
}
/***********************************/


/* создание строки товара в корзине */
function createProductInTable(index) {
  let idProd =  arCart[index][0];

  let prodSrcImg = arCart[index][5];
  if ( prodSrcImg === undefined) {
    prodSrcImg = "";
  }

  let prodTitle = arCart[index][1];
  if ( prodTitle === undefined) {
    prodTitle = "";
  }

  let prodSize = arCart[index][2];
  if ( prodSize === undefined) {
    prodSize = "";
  }

  let prodText = arCart[index][7];
  if ( prodText === undefined || prodText === "") {
    prodText = "";
  } else {
    prodText = "Текст: " + prodText;
  }

  let prodCount = arCart[index][4];
  if ( prodCount === undefined) {
    prodCount = "";
  }

  let prodPrice = arCart[index][3];
  if ( prodPrice === undefined) {
    prodPrice = "";
  }

  let prodUnit = arCart[index][6];
  if ( prodUnit === undefined) {
    prodUnit = "";
  }

  return (
    "<tr data-productId='" + idProd + "' >" +
      "<td> " +
        "<div class='listProduct__itemHead'>" +
          "<img class='listProduct__itemImg' src='" + prodSrcImg + "' alt='image'>" +
          "<div class='listProduct__itemContainer'>" +
            "<div class='listProduct__itemTitle blockText'>" + prodTitle + "</div>" +
            "<div class='listProduct__itemSize blockText'>" + prodSize + "</div>" +
            "<div class='listProduct__itemText blockText'>" + prodText + "</div>" +
          "</div>" +
        "</div>" +
      "</td>" +

      "<td>" +
        "<div class='listProduct__itemCountWrapper'>" +
          "<div class='listProduct__itemCount productCount'>" +
            "<button class='productCount__countMin productCount__countBtn' data-productId='" + idProd + "'></button>" +
            "<div class='productCount__countNumber' data-productId='" + idProd + "'>" + prodCount + "</div>" +
            "<button class='productCount__countMax productCount__countBtn' data-productId='" + idProd + "'></button>" +
          "</div>" +
        "</div>" +
      "</td>" +

      "<td>" +
        "<span class='listProduct__itemPrice listProduct__itemBodyText' data-productId='" + idProd + "'>" + prodPrice + "</span>" +
        "<span class='listProduct__itemPriceUnit listProduct__itemBodyText listProduct__itemUnit'>"+ prodUnit +"</span>" +
      "</td>" +

      "<td>" +
        "<span class='listProduct__itemTotal listProduct__itemBodyText' data-productId='" + idProd + "'>" + (prodPrice * prodCount).toString() + "</span>" +
        "<span class='listProduct__itemPriceUnit listProduct__itemBodyText listProduct__itemUnit'>"+ prodUnit +"</span>" +
      "</td>" +

      "<td>" +
        "<button class='listProduct__itemDelBtn listProduct__delBtn' data-productId='" + idProd + "'></button>" +
      "</td>" +
    "</tr>"
  )

}
/**************************************/


/* активация примечание при выборе способа доставки */

let deliveryMethodNotes = document.querySelectorAll('.order__deliveryMethodNote');
let orderDelivery = document.querySelector('.order__clientInfoPrice .order__price');

function selectDeliveryNote(inputId) {
  let deliveryMethodNote = document.querySelector('[data-input=' + inputId +']');

  if (deliveryMethodNote !== null) {
    deliveryMethodNote.classList.add("active");
  }
}

for (let i=0; i < deliveryMethodInputs.length; i++) {

  deliveryMethodInputs[i].addEventListener('click', function(e) {

    for (let i=0; i < deliveryMethodNotes.length; i++) {
      deliveryMethodNotes[i].classList.remove('active');
    }

    selectDeliveryNote(e.target.id);

    if (e.target.id==="idDelivery") {
      orderDelivery.classList.remove("blockHide");
    } else {
      orderDelivery.classList.add("blockHide");
    }
  })

}

/*******************************************************/


/* удаление товара из корзины */

function delBtnProductList() {
  let productDelBtns = document.querySelectorAll('.listProduct__itemDelBtn');

  for (let i = 0; i < productDelBtns.length; i++) {

    productDelBtns[i].addEventListener('click', function (e) {

      let productId = e.target.getAttribute('data-productId');
      let totalDelProduct = Number(document.querySelector('.listProduct__itemTotal[data-productId="' + productId + '"]').textContent);
      let totalAllProducts = Number(totalProductList.textContent);

      totalProductList.textContent = (totalAllProducts - totalDelProduct).toString();

      changeTotalProductListWithPromo();

      sessionStorage.removeItem(productId);

      deleteElement(document.querySelector('tr[data-productId="' + productId + '"]'))

        var products_cart = getObjToLocalStorage('products_cart');
        delete(products_cart[productId]);
        console.log(products_cart);
        checkProduct_cart(products_cart);
        setObjToLocalStorage('products_cart', products_cart);

    });

  }
}
/******************************************************/


/* проверка промокода */

let promoSendBtn = document.querySelector('.listProduct__promoBtn');
let promoCodeField = document.querySelector('.listProduct__promoCode');
let promoPriceField = document.querySelector('.order__promoPrice');

function insertPromoInProductList(arData) {
    let promotext = {
        'ru': 'Сумма скидки по промокоду',
        'en': 'Promo code discount amount',
        'he': 'promo code discount amount'
    };


  let promoRowTotal = document.querySelector('tfoot tr:last-child');

  let promoId = arData.id.toString();
  let promoPrice = arData.price.toString();
  let promoUnit = arData.unit.toString();

  let promoRowInHtml =
    "<tr data-promoId="+promoId+">" +
      "<td>" +
      "</td>" +

      "<td colspan='2'>" +
        "<span class='listProduct__itemBodyText'>" + promotext[lang] + ":</span>" +
      "</td>" +

      "<td>" +
        "<span class='listProduct__itemPromoPrice listProduct__itemFooterText'>" + promoPrice + "</span>" +
        "<span class='listProduct__itemPromoUnit listProduct__itemFooterText'>" + promoUnit + "</span>" +
      "</td>" +

      "<td>" +
        "<button class='listProduct__promoDelBtn listProduct__delBtn' data-promoId="+promoId+"></button>" +
      "</td>" +
    "</tr>";

  /* удаляем старый промо перед вставкой нового */
  deleteElement(document.querySelector('tr[data-promoId]'));
  deleteElement(document.querySelector('.order__promoItem[data-promoId]'));
  deleteElement(document.querySelector('.order__promoPrices .order__price[data-promoId]'));
  /*******************************************/

  promoRowTotal.insertAdjacentHTML("beforebegin", promoRowInHtml);

  changeTotalProductListWithPromo();

  promoRowTotal.classList.add('showBlock');

  let promoDelBtn = document.querySelector('.listProduct__promoDelBtn[data-promoId="' + promoId + '"]');

  promoDelBtn.addEventListener('click', function (e) {

    deleteElement(document.querySelector('tr[data-promoId="' + promoId + '"]'));
    deleteElement(document.querySelector('.order__promoItem[data-promoId="' + promoId + '"]'));
    deleteElement(document.querySelector('.order__promoPrices .order__price[data-promoId="' + promoId + '"]'));

    changeTotalProductListWithPromo();

  });

}


function insertPromoInOrder(arData) {

  let promoId = arData.id.toString();
  let promoPrice = arData.price.toString();
  let promoUnit = arData.unit.toString();
  let promoName = arData.name.toString();

  let promoItems = document.querySelector('.order__promoItems');
  let promoItPrices = document.querySelector('.order__promoPrices');

  let promoItemHtml =
    "<div class='order__promoItem order__blockTitle' data-promoId=" + promoId + ">" +
      "<p>Промокод <span>" + promoName + "</span> активирован</p>" +
    "</div>";

  let promoPriceHtml =
    "<div class='order__promoPriceItem order__price' data-promoId=" + promoId + ">" +
      "<span class='order__promoPrice'>" + promoPrice + "</span>" +
      "<span class='order__promoUnit'>"+ promoUnit + "</span>" +
    "</div>";

  promoItems.insertAdjacentHTML("beforeend", promoItemHtml);
  promoItPrices.insertAdjacentHTML("beforeend", promoPriceHtml);

}

if (promoSendBtn !== null) {
  promoSendBtn.addEventListener('click', function (e) {
    e.preventDefault();

    let promoCodeFail = document.querySelector('.listProduct__promoFail');
    promoCodeFail.classList.remove('showBlock');

    let request = new XMLHttpRequest();

    request.open('GET', get_promo_code_url + '?promoCode=' + promoCodeField.value);

    request.send();

    request.addEventListener('readystatechange', function () {
      if (this.readyState === 4 && this.status === 200) {
        let data = this.responseText;

        if (data === "") {
          promoCodeFail.classList.add('showBlock');
        } else {

          let objData = JSON.parse(data);

          // let promoRow = document.querySelector('tfoot tr[data-promoId="' + objData.id.toString() + '"]');

          // if (promoRow === null) {
            insertPromoInProductList(objData);
            insertPromoInOrder(objData);
          // }

        }

      }

    });
  })
}

/****************************************************/


/* показыаем блок "самовывоз" при выборе соответствующей радиокнопки */

let selfRadioInputs = document.getElementsByName('delivery');
let selfBlocks = document.querySelectorAll('tr[data-block = "self"]');
let selfPercent = document.querySelector('.order__selfPercent');
let selfPrice = document.querySelector('.order__selfPrice');

let fieldCity = document.getElementsByName('city');
let fieldStreet = document.getElementsByName('street');
let fieldHouse = document.getElementsByName('house');
let fieldFlat= document.getElementsByName('flat');
let fieldFloor= document.getElementsByName('floor');
let fieldDate= document.getElementsByName('date');
let fieldTime= document.getElementsByName('time');
let fieldOtherClient= document.getElementsByName('phoneOtherPerson');
let fieldOtherPerson= document.querySelector('.order__clientInfoField--otherPerson');
let fieldNote = document.querySelector('.order__clientInfoNote');
let fieldFlatFloor = document.querySelector('.order__clientInfoFields--flat');

function visibleClientInfoFields(visible) {
  if (!visible) {
    fieldCity[0].classList.remove('showBlock');
    fieldCity[0].parentElement.classList.remove('showBlock');
    fieldCity[0].classList.remove('validateText');

    fieldStreet[0].classList.remove('showBlock');
    fieldStreet[0].parentElement.classList.remove('showBlock');
    fieldStreet[0].classList.remove('validateText');

    fieldHouse[0].classList.remove('showBlock');
    fieldHouse[0].parentElement.classList.remove('showBlock');
    fieldHouse[0].classList.remove('validateText');

    fieldFlat[0].classList.remove('showBlock');
    fieldFlat[0].parentElement.classList.remove('showBlock');

    fieldFloor[0].classList.remove('showBlock');
    fieldFloor[0].parentElement.classList.remove('showBlock');

    fieldOtherClient[0].classList.remove('validateText');

    fieldOtherPerson.classList.remove('showBlock');
    fieldOtherPerson.classList.add('blockHide');

    fieldNote.classList.remove('showBlock');

    fieldFlatFloor.classList.remove('showBlock');
  } else {
    fieldCity[0].classList.add('showBlock');
    fieldCity[0].parentElement.classList.add('showBlock');
    fieldCity[0].classList.add('validateText');

    fieldStreet[0].classList.add('showBlock');
    fieldStreet[0].parentElement.classList.add('showBlock');
    fieldStreet[0].classList.add('validateText');

    fieldHouse[0].classList.add('showBlock');
    fieldHouse[0].parentElement.classList.add('showBlock');
    fieldHouse[0].classList.add('validateText');

    fieldFlat[0].classList.add('showBlock');
    fieldFlat[0].parentElement.classList.add('showBlock');

    fieldFloor[0].classList.add('showBlock');
    fieldFloor[0].parentElement.classList.add('showBlock');

    fieldOtherClient[0].parentElement.classList.add('validateText');

    fieldOtherPerson.classList.add('showBlock');
    fieldOtherPerson.classList.remove('blockHide');

    fieldNote.classList.add('showBlock');

    fieldFlatFloor.classList.add('showBlock');
  }
}

function changePlaceholder(valDateField, valTimeField) {
  fieldDate[0].placeholder = valDateField;
  fieldTime[0].placeholder = valTimeField;
}

function visibleBlockSelf(visible) {

  if (visible) {
    for (let j=0; j < selfBlocks.length; j++) {
      selfBlocks[j].classList.remove('blockHide');
    }
  } else {
    for (let j=0; j < selfBlocks.length; j++) {
      selfBlocks[j].classList.add('blockHide');
    }
  }

}

function getPriceFromTotal(discountPercent, totalOrder) {
  return (discountPercent * totalOrder / 100).toFixed(1);
}

for (let i=0; i < selfRadioInputs.length; i++) {

  selfRadioInputs[i].addEventListener('change', function (e) {

    visibleBlockSelf(false);

    if (e.target.hasAttribute('data-block')) {
      visibleBlockSelf(true);
      visibleClientInfoFields(false);
      changePlaceholder("Дата самовывоза", "Время самовывоза");

      selfPrice.textContent = getPriceFromTotal(Number(selfPercent.textContent), Number(totalProductList.textContent));
    } else {
      visibleClientInfoFields(true);
      changePlaceholder("Дата доставки", "Время доставки");

      selfPrice.textContent = '0';
    }

    setTotalOrder();

  })
}

/******************************************************/


/* кнопка выбора дргуого клиента */
let otherPersonCheck = document.getElementById('otherPerson');
let otherPersonWrapperInput = document.querySelector('.order__clientInfoField--otherPerson .order__clientInfoInputWrapper');

otherPersonCheck.addEventListener('change', function(e) {
  if (e.target.checked) {
    otherPersonWrapperInput.classList.add('showBlock');
    fieldOtherClient[0].classList.add('validateText');
  } else {
    otherPersonWrapperInput.classList.remove('showBlock');
    fieldOtherClient[0].classList.remove('validateText');
  }
})
/***********************************/


/* расчет чаевых */

let premiumRadioBtns = document.getElementsByName('premium');
let premiumPrice = document.querySelector('.order__premiumPrice');

function getPremiumPrice() {

  for (let i=0; i < premiumRadioBtns.length; i++) {
    if (premiumRadioBtns[i].checked) {
      premiumPrice.textContent = getPriceFromTotal(Number(premiumRadioBtns[i].value), Number(totalProductListWithPromo.textContent));
    }
  }

}

for (let i=0; i < premiumRadioBtns.length; i++) {

  premiumRadioBtns[i].addEventListener('change', function(e) {

    premiumPrice.textContent = getPriceFromTotal(Number(e.target.value), Number(totalProductListWithPromo.textContent));

    setTotalOrder();
  });

}

/*****************************************************/


/* расчет итоговой суммы заказа */

let totalOrderField;
let tmpTotalOrder = 0;

function setTotalOrder() {
  totalOrderField = document.querySelector('.order__totalPrice');
  let carPriceField = document.querySelector('.order__clientInfoPrice .order__price:not(.blockHide) .order__clientInfoCarPrice');

  let cartTotal = 0;
  if (totalProductListWithPromo !== null) {
    cartTotal = Number(totalProductListWithPromo.textContent);
  }

  let premPr = 0;
  if (premiumPrice !== null) {
    premPr = Number(premiumPrice.textContent);
  }

  let selfPr = 0;
  if (selfPrice !== null) {
    selfPr = Number(selfPrice.textContent);
  }

  let promoPr = 0;
  if (promoPriceField !== null) {
    promoPr = Number(promoPriceField.textContent);
  }

  let carPr = 0;
  if (carPriceField !== null) {
    carPr = Number(carPriceField.textContent);
  }

  tmpTotalOrder = (cartTotal + premPr - selfPr + promoPr + carPr);

  if (tmpTotalOrder === "") {
    tmpTotalOrder = 0;
  }

  totalOrderField.textContent = tmpTotalOrder.toFixed(1);
}

/**************************************************/

/* определяем стоимость доставки в зависимости от города */
function searchAreasAndPrice(value) {
  for(let i=0; i<arAreasAndPrices.length; i++) {
    if (arAreasAndPrices[i][0] === value.toLowerCase()) {
      priceAreasField.textContent = arAreasAndPrices[i][2];
      break;
    } else {
      priceAreasField.textContent = "0";
    }
  }
}

fieldCity[0].addEventListener('input', function(e) {
  searchAreasAndPrice(e.target.value);
  setTotalOrder();
})

/***********************************************************/


/* отправка заказа */

let orderValidMessage = document.querySelector('.warningMessage--valid');
let orderMinSumMessage = document.querySelector('.warningMessage--minSum');

function haveError() {
  orderMinSumMessage.classList.remove('showBlock');
  orderValidMessage.classList.add("showBlock");
}

/**********************************/

function sendForm(e) {
    $(function() {
        $('.preloader').removeClass('blockHide');
    });
    orderValidMessage.classList.remove("showBlock");

    /* здесь проверка минимальной суммы заказа */
    if (Number(totalProductListWithPromo.textContent) < 50) {
        orderMinSumMessage.classList.add('showBlock');
        e.preventDefault();
        return false;
    } else {
        orderMinSumMessage.classList.remove('showBlock');
    }
    /*******************************************/

    let methodPay = "";
    for (let i=0; i<methodPayBtns.length; i++) {
        if (methodPayBtns[i].checked) {
            methodPay = methodPayBtns[i].id;
        }
    }

    let totalOrderUnit = document.querySelector('.order__totalUnit');

}



$(function(){


    //маска даты
    let dateMaskOption = {
        mask: '00-00-0000',
        lazy: false
    }

    $('input.dateMask').on('focus', function () {
        for (let i = 0; i <= fieldsDates.length-1; i++) {
            new IMask(fieldsDates[i], dateMaskOption);
        }
    });


    var err_el = $('.errorSend');
    if (err_el) {
        console.log($(err_el).attr('name'));
        console.log(err_el);
        $('html, body').animate({
            scrollTop: $('.order__table').offset().top
        }, 1000);
    }



    $('.popupInfo .popupCloseBtn').click(function () {
        $('.popupInfo').toggleClass('showBlock');
    });

    $('.popupCloseBtn').click(function () {
        $('.popupMessage').hide();
    });


    var url_string = window.location.href;
    var url = new URL(url_string);
    var test_mode = url.searchParams.get("test_mode");
    if (test_mode == 1) {
        console.log('test mode on');
    }

    $('form').submit(function (e) {

        var url_string = window.location.href;
        var url = new URL(url_string);
        var test_mode = url.searchParams.get("test_mode");

        if (test_mode == 1) {
            console.log('no sent form');
            e.preventDefault();
        }

        // e.preventDefault();
        var products_cart = getObjToLocalStorage('products_cart');
        for (k in products_cart) {
            var item = $('.listProduct__table tr[data-productid="' + k + '"]').text();
            if (!item) {
                delete(products_cart[k]);

            }
        }

        $('.listProduct__table tbody tr').each(function () {
            var key = $(this).attr('data-productid');
            var count = $(this).find('.productCount__countNumber').text();
            products_cart[key]['count'] = count;
        });



        var order_data = {};
        order_data.products = products_cart;
        order_data.promo_code = $('input.listProduct__promoCode').val();
        order_data.delivery_price = $('.order__clientInfoCarPrice').text();

        var delivery_method = $("input[name='delivery']:checked").val();
        if (delivery_method == 'pickup') {
            order_data.delivery_discount = $('.order__deliveryMethodNote.active .order__selfPercent').text();
        }
        console.log(order_data);

        let str = JSON.stringify(order_data);
        $('input.order_data').val(str);
        console.log($('input.order_data').val());

    })


    var products_cart = getObjToLocalStorage('products_cart');
    for (k in products_cart) {
        var cart_item = products_cart[k];
        var count_item = cart_item.stock_count;
        $(`.listProduct__table tbody tr[data-productid="${k}"]`).attr('data-stock_count', count_item);
    }


    $('input.city_input').on('input', function () {
        var val = $(this).val();;
        $('.city_out .select_сity').html('');

        $('input.city_id').val('');

        if (val.length > 0) {
            for (k in cityes.citys_all) {
                var city_name_ru = cityes.citys_all[k]['ru'];
                var city_name_en = cityes.citys_all[k]['en'];
                var city_name_he = cityes.citys_all[k]['he'];
                var search = new RegExp(`^${val}`, 'i');

                if (city_name_ru.match(search)) {
                    var city_name = city_name_ru;
                } else if (city_name_he.match(search)) {
                    var city_name = city_name_he;
                } else if (city_name_en && city_name_en.match(search)) {
                    var city_name = city_name_en;
                } else {
                    var city_name = false;
                }

                if (city_name && city_name != 'all') {

                    $('.city_out .select_сity').append(`<span class="add_city"
                        data_key="${k}"
                        data_name="${city_name}">${city_name}</span> <br>`);

                }
            }
            $('.city_out').show();
        }


        $('.city_out .add_city').click(function () {
            var name = $(this).attr('data_name');
            var key = $(this).attr('data_key');
            $('.city_out .select_сity').html('');
            $('input.city_input').val(name);
            $('input.city_id').val(key);
            searchAreasAndPrice(name);
            setTotalOrder();
            checkDeliveryCity(name);

            $('.city_out').hide();

            var city_min_summ = $(`.order__listAreasPrices li[data_name="${name}"]`).attr('data-order') / 1;
            var order_summ = $('.listProduct__itemAllTotal').text() / 1;

            if (order_summ < city_min_summ) {
                $('.warningMessage.warningMessage--minSum').addClass('showBlock');
                $('.order__sendForm').hide();
            } else {
                $('.warningMessage.warningMessage--minSum').removeClass('showBlock');
            }
        });
    });

    $('form input').on('focus', function () {
        $('.order__sendForm').show();
    });

    // проверка города
    $('input.city_input').on('change', function () {
        var value = $(this).val();
        checkDeliveryCity(value);
    });

    function checkDeliveryCity(value) {
        var city = $(`.order__listAreasPrices li[data_name="${value}"]`).text();
        if (value != city) {
            $('.warningMessage.warningMessage--noDeliveryCity').addClass('showBlock');
            $('.order__sendForm').hide();
        } else {
            $('.warningMessage.warningMessage--noDeliveryCity').removeClass('showBlock');
            $('.order__sendForm').show();
        }
    }

    if (!!ga) {
        ga(function(tracker) {
            var clientId = tracker.get('clientId');
            $("input[name='gClientId']").val(clientId);
        });
    }


});

