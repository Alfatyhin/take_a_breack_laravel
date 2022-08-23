
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


    /* настройки слайдера в описание товара */

    $('.description__slider').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 3000,
        variableWidth: true,
        swipeToSlide: true,
        responsive: [
            {
                breakpoint: 1025,
                settings: {
                    variableWidth: false,
                }
            },
            {
                breakpoint: 481,
                settings: {
                    slidesToShow: 3,
                    variableWidth: false,
                }
            },
            {
                breakpoint: 376,
                settings: {
                    slidesToShow: 2,
                    variableWidth: false,
                }
            },
        ]
    });

    /********************************************/


    /* вытаскиваем название размера из строки */
    function getSizeName(text, sizeId) {
        sizeIndex = typeProduct + sizeId.toString();
        sizeProduct = text;
    }

    /******************************************/


    /* при загрузке страницы */

    let idProduct = "";
    let typeProduct = "";
    let sizeProduct = "";
    let sizeIndex = "n";

    let sizeTextOnProduct = 0;

    function loadPage() {

        /* определяем id и тип товара */
        let urlParams = document.location.search;
        let urlSearchParams = new URLSearchParams(urlParams);

        idProduct = product.id;
        typeProduct = product.category_id + '-';
        /**************************/

        /* отмечаем размер в списке по умолчанию */
        let sizeInputs = document.querySelectorAll('.description__sizeListItem input');
        if (sizeInputs.length !== 0) {
            sizeInputs[0].checked = true;

            getSizeName(sizeInputs[0].nextElementSibling.querySelector('.description__sizeListItemTitle').textContent,
                sizeInputs[0].nextElementSibling.querySelector('.description__sizeListItemTitle').getAttribute('data-id'));

            if (clientTextField !== null) {
                if ((sizeInputs.length !== 0) && (sizeInputs[0].hasAttribute('data-size'))) {
                    setLengthTextProduct(sizeInputs[0]);
                } else {
                    setLengthTextProduct(clientTextField);
                }
            }

        } else {
            sizeProduct = titleProductSize.textContent;
        }
        /******************************************/

        /* проверяем товар в корзине */
        let countPr = getCountProductFromCart();
        if (countPr !== null) {
            setCountProduct(countPr);
            changeCartBtns(true);
        }
        /******************************/

        /* кнопка "минус" в счетчике корзины неактивна, если количество товара равно минимуму */

        totalCountFields = document.querySelectorAll('.productCount__countNumber');
        btnCountMin = document.querySelectorAll('.productCount__countMin');
        btnCountMax = document.querySelectorAll('.productCount__countMax');

        btnChangeCount(); //вешаем клик на кнопку изменения количества товара

        if (totalCountFields !== null) {
            for (let i = 0; i < totalCountFields.length; i++) {
                if (Number(totalCountFields[i].textContent) === minCount) {
                    btnCountMin[i].classList.add('productCount__countMin--disabled');
                }
            }

            setProductIdForCountElements(sizeIndex + "-" + idProduct)
        }

        /*******************************************/
    }

    /**********************************************/

    /* устанавливаем атрибут data-productid у элементов счетчика*/
    function setProductIdForCountElements(productId) {
        totalCountFields[0].setAttribute('data-productid', productId);
        btnCountMin[0].setAttribute('data-productid', productId);
        btnCountMax[0].setAttribute('data-productid', productId);
    }
    /*******************************************/


    /* если товар есть в корзине, вытаскиваем выбранное количество */
    function getCountProductFromCart() {
        let sessionItem = sessionStorage.getItem(sizeIndex + "-" + idProduct);
        let countPr;

        if (sessionItem === null) {
            countPr = null;
        } else {
            let arCart = sessionItem.split("|")
            countPr = arCart[3];
        }

        return countPr;
    }

    /*************************************************************/


    /* вытаскиваем надпись для товара */
    function getClientTextFromCart() {
        let sessionItem = sessionStorage.getItem(sizeIndex + "-" + idProduct);
        let clientText = "";

        if (sessionItem !== null) {
            let arCart = sessionItem.split("|")
            clientText = arCart[6];
        }

        return clientText;
    }

    /*************************************************************/


    /* устанавливаем количество выбранного товара */
    function setCountProduct(countPr) {
        document.querySelector('.productCount__countNumber').textContent = countPr.toString();

        let btnMin = document.querySelector('.productCount__countMin[data-productId="' + idProduct + '"]');
        if (btnMin !== null) {
            if (Number(countPr) === 1) {
                btnMin.classList.add('productCount__countMin--disabled');
            } else {
                btnMin.classList.remove('productCount__countMin--disabled');
            }
        }
    }

    /*******************************************/


    /* проверка длины введного текста для надписи в открытке, на торте */
    let clientTextField = document.querySelector('.description__clientText');

    if (clientTextField !== null) {
        clientTextField.addEventListener('keypress', function (e) {
            if (sizeTextOnProduct > 0) {
                if (e.target.value.length > sizeTextOnProduct) {
                    e.preventDefault();
                }
            }
        })

        clientTextField.addEventListener('paste', function (e) {
            e.preventDefault();
        })
    }

    /*********************************************************************/


    /* список размеров товара */

    let titleProductSize = document.querySelector('.description__sizeTitle');
    let listProductSize = document.querySelector('.description__sizeList');
    let listProductSizeValue = document.querySelectorAll('.description__sizeList .description__sizeListItem input[type="radio"]');
    let productCurPrice = document.querySelector('.description__priceItem--current .description__priceItemNumber');
    let productCurPriceUnit = document.querySelector('.description__priceItem--current .description__priceItemUnit');
    let productPriceText = document.querySelector('.description__priceItem--current .description__priceItemText');

    function setLengthTextProduct(field) {
        let size = field.getAttribute('data-size');

        if (size !== null) {
            clientTextField.placeholder = "Напишите текст до " + size.toString() + " символов";
            sizeTextOnProduct = Number(size);
        }

        clientTextField.value = getClientTextFromCart();
    }

    titleProductSize.addEventListener('click', function (e) {

        if (e.target.classList.contains('description__sizeTitle--many')) {
            if (e.target.classList.contains('showBlock')) {
                listProductSize.classList.remove('showBlock');
                e.target.classList.remove('showBlock');
            } else {
                listProductSize.classList.add('showBlock');
                e.target.classList.add('showBlock');
            }
        }

    })

    for (let i = 0; i < listProductSizeValue.length; i++) {
        listProductSizeValue[i].addEventListener('change', function (e) {
            if (e.target.value !== "") {
                productCurPrice.textContent = e.target.value.toString();
            }

            if (productPriceText !== null) {
                productPriceText.textContent = "";
            }

            let sizeT = e.target.nextElementSibling.querySelector('.description__sizeListItemTitle');
            getSizeName(sizeT.textContent, sizeT.getAttribute('data-id'));

            setProductIdForCountElements(sizeIndex + "-" + idProduct);

            let countPr = getCountProductFromCart();
            if (countPr !== null) {
                setCountProduct(countPr);
                changeCartBtns(true);
            } else {
                setCountProduct(1);
                changeCartBtns(false);
            }

            if (clientTextField !== null) {
                setLengthTextProduct(e.target);
            }

        })
    }

    /**********************************************/

    function changeCartBtns(visible) {
        if (visible) {
            btnAddToCartFirstTitle.classList.add('blockHide');
            btnAddToCartSecondTitle.classList.remove('blockHide');
            btnGoToCart.classList.add('showBlock');
        } else {
            btnAddToCartFirstTitle.classList.remove('blockHide');
            btnAddToCartSecondTitle.classList.add('blockHide');
            btnGoToCart.classList.remove('showBlock');
        }
    }


    /* изменение кнопок при добавлении товара в корзину */

    let btnAddToCart = document.querySelector('.description__btnAddToCart');
    let btnAddToCartFirstTitle = document.querySelector('.description__btnAddToCartFirstTitle');
    let btnAddToCartSecondTitle = document.querySelector('.description__btnAddToCartSecondTitle');
    let btnGoToCart = document.querySelector('.description__btnGoToCart');

    btnAddToCart.addEventListener('click', function (e) {

        let tmpKey = sizeIndex + "-" + idProduct;

        /* если товар есть в корзине, переходим в каталог на главной */
        if (sessionStorage.getItem(tmpKey) !== null) {
            sessionStorage.setItem('productType', typeProduct);
            window.location.href = general_url + '/#anchor-select';
            return false;
        }
        /*********************************************/

        let clientText = "";
        if (clientTextField !== null) {
            clientText = clientTextField.value
        }

        changeCartBtns(true);

        //заносим название, цену и кол-во выбранного товара в хранилище, для передачи в корзину
        sessionStorage.setItem(sizeIndex + "-" + idProduct,
            document.querySelector('.description__title').textContent + "|" +
            sizeProduct + "|" +
            productCurPrice.textContent + "|" +
            document.querySelector('.productCount__countNumber').textContent + "|" +
            productGeneralImg.getAttribute("src") + "|" +
            productCurPriceUnit.textContent + "|" +
            clientText);


        /////////////////////////////////////////////
        var products_cart = getObjToLocalStorage('products_cart');
        if (!products_cart) {
            products_cart = {};
        }

        $(function() {
            var product_cart_item = {};
            product_cart_item.id = product.id;
            var cart_id = sizeIndex + "-" + idProduct;

            var size = $('.description__sizeListItem input:checked + label .description__sizeListItemTitle');
            if (size.length != 0) {
                var stock_count = $(size).attr('data-stock_count');
                if (stock_count) {
                    product_cart_item.stock_count = stock_count;
                }
                var variant_id = $(size).attr('data-variant_id');
                if (variant_id) {
                    product_cart_item.variant = variant_id;
                }
                var option_key = $(size).attr('data-option_key');
                if (option_key) {
                    var option_value = $(size).attr('data-option_value');
                    product_cart_item.options = [];
                    var option = {
                        'key': option_key,
                        'value': option_value
                    };
                    product_cart_item.options.push(option);
                }

            } else {
                console.log('no size');
                if (product.unlimited == 0) {
                    product_cart_item.stock_count = product.count;
                } else {
                    product_cart_item.stock_count = 0;
                }
            }
            console.log(product_cart_item);

            console.log(cart_id);
            products_cart[cart_id] = product_cart_item;

            checkProduct_cart(products_cart);
            setObjToLocalStorage('products_cart', products_cart);
        });
        //////////////////////////////////////////////////////////////////////

    })

    /***************************************************/


    /* переключение табов с характеристиками товара */

    let tabNameComponents = document.querySelector('.description__tabName--components');
    let tabComponents = document.querySelector('.description__tabComponents');
    let tabNameCalories = document.querySelector('.description__tabName--calories');
    let tabCalories = document.querySelector('.description__tabColories');
    let tabNameStoring = document.querySelector('.description__tabName--storing');
    let tabStoring = document.querySelector('.description__tabStoring');
    let tabNameUnderline = document.querySelector('.description__tabNameUnderline');

    if (tabNameUnderline !== null) {
        tabNameUnderline.style.width = tabNameComponents.offsetWidth + 'px';
    }

    function hideAllTabs() {
        tabComponents.classList.remove('showBlock');
        tabNameComponents.classList.remove('showBlock');
        tabCalories.classList.remove('showBlock');
        tabNameCalories.classList.remove('showBlock');
        tabStoring.classList.remove('showBlock');
        tabNameStoring.classList.remove('showBlock');
    }

    if (tabNameComponents !== null) {
        tabNameComponents.addEventListener('click', function () {
            hideAllTabs();
            tabComponents.classList.add('showBlock');
            tabNameComponents.classList.add('showBlock');

            tabNameUnderline.style.left = tabNameComponents.offsetLeft + 'px';
            tabNameUnderline.style.width = tabNameComponents.offsetWidth + 'px';
        });
    }

    if (tabNameCalories !== null) {
        tabNameCalories.addEventListener('click', function () {
            hideAllTabs();
            tabCalories.classList.add('showBlock');
            tabNameCalories.classList.add('showBlock');

            tabNameUnderline.style.left = tabNameCalories.offsetLeft + 'px';
            tabNameUnderline.style.width = tabNameCalories.offsetWidth + 'px';
        });
    }

    if (tabNameStoring !== null) {
        tabNameStoring.addEventListener('click', function () {
            hideAllTabs();
            tabStoring.classList.add('showBlock');
            tabNameStoring.classList.add('showBlock');

            tabNameUnderline.style.left = tabNameStoring.offsetLeft + 'px';
            tabNameUnderline.style.width = tabNameStoring.offsetWidth + 'px';
        });
    }


    /******************************************************/


    /* переключение фотографий */

    let productImgList = document.querySelectorAll('.description__listImages a');
    let productGeneralImg = document.querySelector('.description__generalImg img');

    for (let i = 0; i < productImgList.length; i++) {
        productImgList[i].addEventListener('click', function (e) {
            e.preventDefault();

            productGeneralImg.setAttribute("src", e.target.getAttribute("src"));
        });
    }

    /*******************************************************/

