

document.addEventListener('DOMContentLoaded',()=>{

    let intervalSelect
    if($(".main__wrap .product .product-info__size.product_option.required").length){
        try {
            let elem = $(".product-size.open-size-table")[0];
            intervalSelect = setInterval(() => {

                elem.style.cssText= `
                border: 2px solid red;
                background: lightpink;
            `
                setTimeout(() => {
                    elem.style.cssText= `
                    border: 2px solid #000;
                    background: none;
                `
                }, 300);
                setTimeout(() => {
                    elem.style.cssText= `
                    border: 2px solid red;
                    background: lightpink;
                `
                }, 500);
                setTimeout(() => {
                    elem.style.cssText= `
                    border: 2px solid #000;
                    background: none;
                `
                }, 700);
                setTimeout(() => {
                    elem.style.cssText= `
                    border: 2px solid red;
                    background: lightpink;
                `
                }, 900);
                setTimeout(() => {
                    elem.style.cssText= `
                    border: 2px solid #000;
                    background: none;
                `
                }, 1100);
                
            }, 3000);
        } catch (error) {
                
        }
    }


    // client_data = JSON.parse(localStorage.getItem("client_data") || "[]");   
    // client_data.order_id = ""
    // localStorage.setItem("client_data", JSON.stringify(client_data));

    (function cartInit(){        
        debugger
        let ordData = $("input.order_data");
        if( ordData.length != 0 && ordData[0].value != "" ){
            
            let cart =  JSON.parse(ordData[0].value).products
            let isPromoCodeActive =  JSON.parse(ordData[0].value).promo
            localStorage.setItem("cart", JSON.stringify(cart));
            localStorage.setItem("promo", JSON.stringify(isPromoCodeActive));
        } 
        
        //let cart = JSON.parse(localStorage.getItem("cart") || "[]") 
        //localStorage.setItem("cart", "undefined");

        let cart = JSON.parse( localStorage.getItem("cart") == "undefined" ? "[]" : (localStorage.getItem("cart") || "[]"))
        if($("#cart").length){
            if(!cart.length) $(".main-btn.go-pay")[0].style.opacity = "0.4"
            else if(cart.length ) $(".main-btn.go-pay")[0].style.opacity = "1.0"
        } 
       
        // $(".mark-link")[1].style.display = "none"      // скрываем иконку пользователя/личного кабинета        
        // $(".mark-link")[2].style.display = "none"      // скрываем иконку пользователя/личного кабинета 

        if(!cart.length)  return 
        if($("#cart").length){
            cartInitProducts(cart);
        } 
        if($("#cart").length) summCalculation(cart);
        // $(".header__login a:last-child")[1].style.display = "none"

        
        if($(".pay.step_3").length != 0) {            
            if(cart[0] && cart[0].delivery_params && cart[0].delivery_params.cityId) summDeliveryStep3(cart[0].delivery_params.cityId)
            else(summDeliveryStep3(-1))
        } 
       
       
       
    })();


    if($(".main__wrap .product").length){
        updateProductPage()        

       }
    // $(".popup")[0].innerHTML = ` 
    //                             <div class="popup__content">
    //                                 <div class="text_add">Малиновый торт с нежным бисквитом добавлен в корзину!</div>
    //                                 <div class="count_in_cart_text">В корзине уже есть 2шт. данного товара</div>

    //                                 <div class="text_in_cart">Для продолжения оформления заказа перейдите пожалуйста в корзину</div>

    //                                 <div class="buttons_popup">
    //                                     <button class="go_prod"> ВЕРНУТЬСЯ К ТОВАРАМ</button>
    //                                     <button class="go_cart"><a href="https://takeabreak.website/en/cart" >ПЕРЕЙТИ В КОРЗИНУ</a></button> 
    //                                 </div>
    //                             </div>
    //                         `
   
    

    //#region Переключалка языка

    $(".lang_select.mark_lang li.active").on('click change', function(e) {
        
        $(".lang_select.mark_lang li").each(function() { 
           if( this.classList.contains("hide")) this.classList.remove("hide")
           if( this.classList.contains("active")) this.classList.remove("active")
           this.classList.add("open")
        });
        return false
    });
    $(".lang_select.mark_lang li.open").on('click change', function(e) {
        
        this.classList.add("active")
        $(".lang_select.mark_lang li").each(function() {            
            if( !this.classList.contains("active")) this.classList.remove("hide")
            if( this.classList.contains("open")) this.classList.remove("open")
         });
         return false
    });
    //#endregion
    //#region   Открытие меню
                    
    const menuBtn = document.querySelector('.menu-btn');
    const menuList = document.querySelector('.category');
    const closeMenuBtn = document.querySelector('.close-menu-btn');
    const modal = document.querySelector('.modal');
    if (menuBtn) {
        menuBtn.addEventListener('click', ()=>{
            OpenMenu();
        })
    }

    if(closeMenuBtn) {
        closeMenuBtn.addEventListener('click', CloseMenu);
    }
    function CloseMenu() {
        menuList.classList.remove('active');
        document.body.style.overflow = 'auto';
        modal.classList.remove('active');
    }

    function OpenMenu() {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        menuList.classList.add('active')
        document.addEventListener('click',(e)=>{
            let target = e.target;
            
            if (!target.closest('.header') && !target.closest('.category')){
                CloseMenu();
            }
        })
    }
    //#endregion
 
    //#region слайдер продукта 

    let productpage = document.querySelector('.product-preview');
    if (productpage) {
        const productSliderPreview = new Swiper('.product-preview', {
            slidesPerView: 4,
            spaceBetween: 10,
            watchSlidesProgress: true,
        })
        const productSlider = new Swiper('.product-slider', {
            spaceBetween: 10,
            slidesPerView: 1,
            thumbs: {
                swiper: productSliderPreview
            },
           
        });
    }
    //#endregion
    
    //#region Выбор из списка

    $('.product-info__size.product_option').on('click change', function(e) {

        if(intervalSelect) clearInterval(intervalSelect)  
         
        let elem = $(this).find('.product-size__table')[0]
        if(elem.classList.contains('active')) {
            $(this).find('.open-size-table')[0].classList.remove("active")
            $(this).find('.product-size__table')[0].classList.remove("active");
        } else {
            $(".open-size-table").each(function() { 
                if(this.classList.contains('active')) this.classList.remove("active")
            });
            $(".product-size__table").each(function() { 
                if(this.classList.contains('active')) this.classList.remove("active")
            });
            $(this).find('.open-size-table')[0].classList.add("active")
            $(this).find('.product-size__table')[0].classList.add("active");
        }
    });

    $(".product-size-var.option_value").on('click change',async function(e) {
        
        selectedItemPrice = +product.price

        $(".main-btn.go-to-cart").each(function() { this.disabled = false });


        let elem = this.closest(".product-size__table").querySelectorAll(".product-size-var.option_value")
        for (let i = 0; i < elem.length; i++) {
            if(elem[i].classList.contains("active")) elem[i].classList.remove("active")
        }
        this.closest(".product_option").firstElementChild.innerHTML =`<span>${ this.dataset.option_text}</span>` 
        if(!this.classList.contains('active')) this.classList.add("active")
        if(this.closest(".product_option") && this.closest(".product_option").classList.contains('required')) this.closest(".product_option").classList.remove("required")
        selectedItemWeightParams = $(this).find('.weight-params')[0] && $(this).find('.weight-params')[0].innerHTML ? $(this).find('.weight-params')[0].innerHTML : ""       
        $(".main-btn.go-to-cart").each(function() { this.disabled = false });
        // $(".product-info-checkbox").each(function() { this.disabled = false });        
        $('.product-info__add input[type="radio"]').each(function() { this.disabled = false });
        $('.product-info__add input[type="checkbox"]').each(function() { this.disabled = false });
        $(".product-info-decrement").each(function() { this.disabled = false });
        $(".product-info-count-input").each(function() { this.disabled = false });
        $(".product-info-increment").each(function() { this.disabled = false });
        $("input[name='box']").each(function() { this.disabled = false });
        $(".main-btn.go-to-cart").each(function() { this.style.opacity = "1.0" });  
        $('#count-product')[0].value = 1 /*** ПРОВЕРИТЬ АКТУАЛЬНОСТЬ ТАКИХ ДЕЙСТВИЙ */
        clearSelected("select");
        await currentSumm($('#count-product').val())    

        
    });

    //#endregion


    // #region Инициализация бейджика количества корзины в карточке товара

    let cart = JSON.parse( localStorage.getItem("cart") == "undefined" ? "[]" : (localStorage.getItem("cart") || "[]"))  
    if(cart.length != 0) $(".badge").each(function() { this.style.opacity = "1" }); 
    $(".cart-count").each(function() { this.innerText = cart.length })          
    

    //#endregion

    // #region  Добавить в корзину

    $(".main-btn.go-to-cart").on('click', function(event){
        
        let isReguired = isRequiredChecked()
        if(!isReguired) return
        let cart = JSON.parse( localStorage.getItem("cart") == "undefined" ? "[]" : (localStorage.getItem("cart") || "[]")); 
        var options = {};
        var cart_key = product.id.toString();
        var variant = false;
        
        $('.product_options .product_option').each(function () {
            
            var option = {};
            var option_el = $(this).find('.option_value.active');
            // let textButton = $(option_el).find('.trans-btn')[0] && $(option_el).find('.trans-btn')[0].innerHTML
           // if(option_el[0].dataset.option_key == 0 ) return
            if (option_el.length != 0 ) {
                
                for (let i = 0; i < option_el.length; i++) {
                   var option_key = $(option_el).attr('data-option_key')[i];

                    
                    console.log(this)
                    option.name = option_el[0].dataset.option_text    
                    console.log(product)
                    option.price = option_el[0].dataset.pricemodifiertype == "PERCENT" ?  product.price * option_el[0].dataset.pricemodifier / 100 : option_el[0].dataset.pricemodifier 
                    option.type = $(this).attr('data_optiontype');
                    option.key = option_key;
                    option.value = $(option_el).attr('data-option_value');
                    option.text = $(option_el).find('.option_text').text();                
                    option.input_text = $(option_el).find('.option_input_text').val() 
                    var variant_number =   $(option_el).attr('data-variant_number');
                    
                    cart_key = `${cart_key}-${option_key}-${option.value}`;
                    if (!!variant_number) {
                        variant = variant_number;
                    }
                    options[option_key] = option;   //  если три позиции под одинаковым ключем, добавляется в корзину только одна опция
                    
                }                
               
            }
        });
        
        let isContains = false
        for (let i = 0; i < cart.length; i++) {
            if(cart_key == cart[i].key ) {
                isContains = true
                $(".count_in_cart")[0].innerHTML =  cart[i].count
                cart[i].count = (+cart[i].count + +$('#count-product').val())
                cart[i].itemSumm = +cart[i].itemSumm + +$('.current-price')[0].innerHTML
                break
            }            
        }
        let addedPosition
        console.log(document.location.href)
        if(!isContains){
            addedPosition = { 
                urlProduct: document.location.href,
                id:  product.id,     /* id +  data-option_key[0] +  data-option_value ???*/
                key: cart_key,
                count: $('#count-product').val(), 
                options: options,
                variant: variant,

                //  поля для визуального отображени
                name: $(".product-info__title h1")[0].innerText,
                weightParams: selectedItemWeightParams,
                itemSumm: $('.current-price')[0].innerHTML,                                                              
                imagePath: imageSrc, 
            }     
            $(".count_in_cart")[0].innerHTML =  addedPosition.count
            cart.push(addedPosition)            
        }
        localStorage.setItem("cart", JSON.stringify(cart));
        
        // очистка полей
        $(".option_input_text").each(function() { this.value = "" });
        $(".product-info-checkbox").each(function() { this.checked = false });
        $("input").each(function() { this.checked = false });
        $("input[name='box']").each(function() { this.disabled = false });
        $(".open-size-table").each(function() { 
            if(this.classList.contains('active')) this.classList.remove("active")
        });
        $(".product-size__table").each(function() { 
            if(this.classList.contains('active')) this.classList.remove("active")
        });
        $(".option_value.active").each(function() { 
            if(this.classList.contains('active')) this.classList.remove("active")
        });
        if($(".trans-btn").length != 0) $(".trans-btn")[0].innerHTML = "Добавить"

        // $(".product-info__size.product_option").each(function() { this.innerHTML = `<span>${ this.dataset.option_text}</span>` });
        // `<span>${ this.dataset.option_text}</span>`
        // currentSumm( $("#count-product")[0].value)
        $(".cart-count").each(function() { this.innerText = cart.length })
        $(".badge").each(function() { this.style.opacity = "1" })
                $('html, body').animate({ scrollTop: 0 }, 10); 
        clearSelected("add")
        $(".popup").fadeIn(500);
        $("#count-product")[0].value = 1
        currentSumm( $("#count-product")[0].value)
    });

    $(".go_prod").click(function() {
        $(".popup").fadeOut(500);
    });
    $(".popup .popup__content .close span").click(function() {
        $(".popup").fadeOut(500);
    });

    // #endregion

    //#region  Переход из корзины в карточку товара

    // $(".pay-cart__item-info").on('click', function(e){
    //     console.log(this)
    //     window.location.href = this.dataset.urlproduct + `/#${this.dataset.id}`
    // });
    $(".pay-cart__item img").on('click', function(e){
        window.location.href = this.dataset.urlproduct + `/#${this.dataset.key}`
    });

    //#endregion

    //#region Кнопка  добавить к заказу 
    $(".trans-btn_additive").on('click', function(event){

        

        let cart = JSON.parse( localStorage.getItem("cart") == "undefined" ? "[]" : (localStorage.getItem("cart") || "[]"));  
        let addedPosition = { 
                                id:  16/*product.id*/,
                                name: $(".product-info__title h1")[0].innerText,
                                weightParams: selectedItemWeightParams,
                                price: selectedItemPrice,
                                itemSumm: currentSumm( $("#count-product")[0].value),
                                count: $(".product-info-count-input")[0].value,
                                variant: "variant",
                                size: "size",
                                text: $(".body-product-info-input-text")[0].value,
                                imagePath: "assets/images/slider-img.png",    

                            }
        cart.push(addedPosition)        
        $(".cart-count").each(function() { this.innerText = cart.length }) 
        $(".badge").each(function() { this.style.opacity = "1" });
        localStorage.setItem("cart", JSON.stringify(cart));
    });
    //#endregion

    //#region Кнопка  добавить текст к торту

    
    $('input.body-product-info-input-text').keyup( async function(e){
        if( this.value != "" ){           
            this.closest(".body-product-info-add").querySelector(".option_value").classList.add("active")
            $(".current-price")[0].innerHTML =  roundNumber(    await currentSumm( $("#count-product")[0].value) || $(".current-price")[0].innerHTML)                
        } else{
            let el = this.closest(".body-product-info-add").querySelector(".option_value")
            if(el.classList.contains("active")) el.classList.remove("active")
            this.value = ""
            $(".current-price")[0].innerHTML = roundNumber(await currentSumm( $("#count-product")[0].value) || $(".current-price")[0].innerHTML)
        }        
    });




    // $(".trans-btn").on('click',async function(e){
    //     let inputText = e.currentTarget.closest(".body-product-info-add").querySelector(".body-product-info-input-text.option_input_text")
    //     if( e.currentTarget.innerHTML == "Добавить" ){                
    //         if( inputText.value != "" ){
    //             e.currentTarget.innerHTML = "Убрать текст"
    //             this.closest(".body-product-info-add").querySelector(".option_value").classList.add("active")
    //             $(".current-price")[0].innerHTML =  roundNumber(await currentSumm( $("#count-product")[0].value) || $(".current-price")[0].innerHTML)                
    //         }
    //     } else if(e.currentTarget.innerHTML == "Убрать текст"){            
    //         e.currentTarget.innerHTML = "Добавить"
    //         let el = this.closest(".body-product-info-add").querySelector(".option_value")
    //         if(el.classList.contains("active")) el.classList.remove("active")
    //         inputText.value = ""
    //         $(".current-price")[0].innerHTML = roundNumber(await currentSumm( $("#count-product")[0].value) || $(".current-price")[0].innerHTML)              
    //     }
    // });
    //#endregion

    //#region  Выбор радиобаттонов

    $('.product-info__add input[type="radio"]').on('click change', function(e) {
        
        $(`input[type="radio"]`).each(function() { 
            if( this.closest(".option_value").classList.contains("active"))
            this.closest(".option_value").classList.remove("active") 
        });  
        this.closest(".option_value").classList.add("active")
        currentSumm( $("#count-product")[0].value)
    });
    
    //#endregion

    //#region  Выбор чекбоксов свечи

    $('.product-info__add input[name="candles"]').on('click change', function(e) {

        if(this.checked)  this.closest(".option_value").classList.add("active")
        else if(!this.checked) {
            if(this.closest(".option_value").classList.contains("active"))  this.closest(".option_value").classList.remove("active")
        } 
       
        currentSumm( $("#count-product")[0].value)
    });
    
    //#endregion

    
    //#region Checkbox для текста к торту
    $(".product-info-checkbox").change(function(){
       
        $(".product-info-checkbox").each(function() {   
            let elem = this.closest(".product-info__add.product_option").querySelector(".body-product-info-add")      
            if(this.checked == true){                
                elem.classList.add("active")
                elem.style.marginBottom = "30px"
            } 
            else{
                elem.classList.remove("active")
                elem.style.marginBottom = "0px"
            } 
        });        
    });
    //#endregion
    
    
    
    //#region маска вводы номера телефона
    
    var selector = document.getElementsByClassName("phone-mask-input");
    if (!selector.length == 0) {
        var im = new Inputmask("99-99-999 (999)");
        im.mask(selector);
    }
    //#endregion

    //#region логика чаевые 

    let totalSumElem = document.querySelector('.total-num');
    let tipsBtns = document.querySelectorAll('input[name=pay-tips]');
    if (totalSumElem) {
        let totalSumNum = +totalSumElem.innerHTML;
        tipsBtns.forEach(function(item){
            item.addEventListener('click', (e)=> {
                let value = +item.value;
                let percent = Math.round((value * 100) / totalSumNum);
                totalSumElem.innerHTML = totalSumNum + percent;
            })
        })
        tipsBtns[1].click();
    }
    //#endregion
    
    //#region слайдер преимуществ продукта

    if (document.querySelector('.product-info__edge')) {
        const productInfoSlider = new Swiper('.product-info__edge', {
            navigation: {
                nextEl: '.edge-next',
                prevEl: '.edge-prev',
            },
            breakpoints: {
                320: {
                    slidesPerView: 2,
                },
                400: {
                    slidesPerView: 3,
                },
                1024: {
                    slidesPerView: 4,
                }
            }
        })
    }
    //#endregion

    //#region табы продукта

    let productTabBtns = document.querySelectorAll('.product-info__tabs-btn');
    let productTabText = document.querySelectorAll('.product-info__tab-text');
    if (productTabBtns.length != 0) {
        productTabBtns.forEach(function(item, i){
            item.addEventListener('click', (e) => {
                productTabBtns.forEach(function(item){
                    item.classList.remove('active');
                })
                item.classList.add('active');
                productTabText.forEach(function(item){
                    item.classList.remove('active');
                })
                productTabText[i].classList.add('active');
            })
        })
        productTabBtns[0].click();
    }
    //#endregion

    //#region изменение вручную количество продукта в карточке товара

    $("#count-product").keyup(function(){
        if($("#count-product")[0].value > 999){
            $("#count-product")[0].value = $("#count-product")[0].value.toString().substring(0,3)
        }
        $(".current-price")[0].innerHTML =   roundNumber(currentSumm( $("#count-product")[0].value) || $(".current-price")[0].innerHTML)
    });
    $("#count-product").change(function(){  
        if($("#count-product")[0].value < 1) $("#count-product")[0].value = 1 
        
        $(".current-price")[0].innerHTML =   roundNumber(currentSumm( $("#count-product")[0].value) || $(".current-price")[0].innerHTML)
    });
    $("#count-product").keypress(function(e){
        if (e.keyCode === 13) $("#count-product").blur()  
    });
    //#endregion

    //#region изменение вручную количество продукта в корзине
    // let itemCountOld;
    // $(".cart-product-info-count-input").keydown(function(e){
    //     itemCountOld = e.currentTarget.value
    // });
    // $(".cart-product-info-count-input").keyup(function(e){
    //     if($(".cart-product-info-count-input")[0].value > 999){
    //         $(".cart-product-info-count-input")[0].value = $(".cart-product-info-count-input")[0].value.toString().substring(0,3)
    //     }
    //     let cart = JSON.parse( localStorage.getItem("cart") == "undefined" ? "[]" : (localStorage.getItem("cart") || "[]"));
    //     let cartId = e.currentTarget.dataset.id 
    //     for (let i = 0; i < cart.length; i++) {
    //         if(cart[i].id == cartId ) {
    //             cart[i].itemSumm = (+cart[i].itemSumm/cart[i].count*e.currentTarget.value).toString()
    //             cart[i].count = e.currentTarget.value
    //         }
    //     }      
    //     localStorage.removeItem("cart");   
    //     localStorage.setItem("cart", JSON.stringify(cart)); 
    //     cartInitProducts(cart)
    // });
    // $(".cart-product-info-count-input").change(function(e){ 
    //     if($(".cart-product-info-count-input")[0].value < 1) $(".cart-product-info-count-input")[0].value = 1 
    //     let cart = JSON.parse( localStorage.getItem("cart") == "undefined" ? "[]" : (localStorage.getItem("cart") || "[]"));
    //     let cartId = e.currentTarget.dataset.id
    //     for (let i = 0; i < cart.length; i++) {
    //         if(cart[i].id == cartId ) {
    //             cart[i].itemSumm = (+cart[i].itemSumm/cart[i].count*e.currentTarget.value).toString()
    //             cart[i].count = e.currentTarget.value
    //         }
    //     }  
    //     localStorage.removeItem("cart");   
    //     localStorage.setItem("cart", JSON.stringify(cart)); 
    //     cartInitProducts(cart)
    //     // $(".itemSumm")[0].innerHTML =   (+$(".itemSumm")[0].innerHTML/itemCountOld*count).toString()      
    // });
    // $(".cart-product-info-count-input").keypress(function(e){
    //     if (e.keyCode === 13) $(".cart-product-info-count-input").blur()  
    // });
    //#endregion

    //#region открытие текст на странице о категории

    let openTextBtn = document.querySelector('.open-text');

    if (openTextBtn) {
        openTextBtn.addEventListener('click', ()=> {
            let hiddenText = document.querySelector('.category-about__col:last-child');
            openTextBtn.style.display = 'none';
            hiddenText.style.overflow = 'auto';
            hiddenText.style.opacity = '1';
            hiddenText.style.maxHeight = hiddenText.scrollHeight + 'px';
        })
    }
    //#endregion

    //#region страница faq

    let faqBtns = document.querySelectorAll('.faq-btn');

    if (!faqBtns.length == 0) {
        faqBtns.forEach(function(item){
            item.addEventListener('click', (e)=>{
                let nextElem = item.nextElementSibling;
                if (!item.classList.contains('active')) {
                    // faqBtns.forEach(function(item){
                    //     item.classList.remove('active');
                    //     document.querySelectorAll(".faq-text").forEach(function(item){
                    //         item.classList.remove('active');
                    //         item.style.maxHeight = 0;
                    //     })
                    // })
                    nextElem.classList.add('active')
                    item.classList.add('active')
                    nextElem.style.maxHeight = nextElem.scrollHeight + 'px';
                } else {
                    item.classList.remove('active')
                    nextElem.classList.remove('active');
                    nextElem.style.maxHeight = 0;
                }
            })
        })
        faqBtns[0].click();
    }
    //#endregion

    //#region Гармошка корзины товара
   
        setTimeout(() => {
            let totalCartBtn = document.querySelector('.pay-cart__total-sum'),
            cartBox = document.querySelector('.pay-cart__box');            
            if (totalCartBtn) {
                totalCartBtn.addEventListener('click', (e)=>{                                         
                if (totalCartBtn.classList.contains('active')) {
                    totalCartBtn.classList.remove('active');
                    cartBox.style.maxHeight =  0 + 'px';
                } else {
                    totalCartBtn.classList.add('active');
                    cartBox.style.maxHeight = cartBox.scrollHeight + 'px';
                }
                })
            }            
        }, 500);       
    
    //#endregion

    //
    // window.addEventListener('beforeunload', (event) => {
    //     // Отмените событие, как указано в стандарте.
    //     // alert("beforeunload")
    //     event.preventDefault();
    //     // Chrome требует установки возвратного значения.
    //     event.returnValue = '';
    //   });
})

let selectedItemPrice;
let imageSrc;
let selectedItemWeightParams;

function currentSumm(count){

    
    if(product.options == null){
        return  +count * +product.price
    } 
    setTimeout(() => {
        let  additionValue = 0
        let itemOptions =  $(".option_value.active")
        if(itemOptions.length) {
            for (let i = 0; i < itemOptions.length; i++) {
                let elem = itemOptions[i]
                let parentElem = elem.closest(".product_option")
                //let type = parentElem.getAttribute( "data_optiontype" )                
                let data = itemOptions[i].dataset
                // if(type == "SIZE"){                    
                if(data.pricemodifiertype == "PERCENT")  additionValue += +product.price* +data.pricemodifier/100
                else if(data.pricemodifiertype == "VARIANT_PRICE") additionValue += +data.pricemodifier - +product.price
                else if(data.pricemodifiertype == "ABSOLUTE") additionValue += +data.pricemodifier
                // } else if(type == "TEXT"){
                //     if(data.pricemodifiertype == "PERCENT")  additionValue += +product.price* +data.pricemodifier/100
                //     else if(data.pricemodifiertype == "VARIANT_PRICE") additionValue += +data.pricemodifier - +product.price
                //     else if(data.pricemodifiertype == "ABSOLUTE") additionValue += +data.pricemodifier
                    
                // } else if(type == "RADIO"){
                //     if(data.pricemodifiertype == "PERCENT")  additionValue += +product.price* +data.pricemodifier/100
                //     else if(data.pricemodifiertype == "VARIANT_PRICE") additionValue += +data.pricemodifier - +product.price
                //     else if(data.pricemodifiertype == "ABSOLUTE") additionValue += +data.pricemodifier
                // }else if(type == "SELECT"){
                //     if(data.pricemodifiertype == "PERCENT")  additionValue += +product.price* +data.pricemodifier/100
                //     else if(data.pricemodifiertype == "VARIANT_PRICE") additionValue += +data.pricemodifier - +product.price
                //     else if(data.pricemodifiertype == "ABSOLUTE") additionValue += +data.pricemodifier
                // }else if(type == "CHECKBOX"){
                //     if(data.pricemodifiertype == "PERCENT")  additionValue += +product.price* +data.pricemodifier/100
                //     else if(data.pricemodifiertype == "VARIANT_PRICE") additionValue += +data.pricemodifier - +product.price
                //     else if(data.pricemodifiertype == "ABSOLUTE") additionValue += +data.pricemodifier
                // }
            }
        }        
        let currentSumm = (+product.price + additionValue)*count
        $(".current-price")[0].innerHTML = roundNumber(currentSumm) || $(".current-price")[0].innerHTML
        return roundNumber(currentSumm)
    }, 50);
}
function currentCountCart( cart ){
    if(cart.length == 0) return 0
    let count = 0
    for (let i = 0; i < cart.length; i++) {
        count = +cart[i].count        
    }
    return count

}
async function increment(e){
    

    if($(".main__wrap .product").length) {    //для товара без опций
        let count = +e.previousElementSibling.value + 1
        $(".current-price")[0].innerHTML = roundNumber($(".current-price")[0].innerHTML/+e.previousElementSibling.value*count)
        e.previousElementSibling.value = count
        
        if($(".pay.step_2").length != 0)  summDelivery((cart[0].delivery_params && cart[0].delivery_params.cityId) || -1);
        if($(".pay.step_3").length != 0)  summDeliveryStep3((cart[0].delivery_params && cart[0].delivery_params.cityId) || -1);
    } 
    
    
    
    else {
        
        if(e.closest('.product-info__count')){    // для товара в корзине
            let count = +e.previousElementSibling.value + 1
            e.previousElementSibling.value = count
            let cart = JSON.parse( localStorage.getItem("cart") == "undefined" ? "[]" : (localStorage.getItem("cart") || "[]"));
            
            let cartKey = e.dataset.key
            for (let i = 0; i < cart.length; i++) {
                if(cart[i].key == cartKey ) {
                    let itemSumm =  (+cart[i].itemSumm/cart[i].count*count)
                    cart[i].itemSumm = roundNumber(itemSumm)
                    cart[i].count = count
                }
            }               
            localStorage.setItem("cart", JSON.stringify(cart)); 
            cartInitProducts(cart)
            summCalculation(cart)
            
            if($(".pay.step_2").length != 0)  summDelivery((cart[0].delivery_params && cart[0].delivery_params.cityId) || -1);
            if($(".pay.step_3").length != 0)  summDeliveryStep3((cart[0].delivery_params && cart[0].delivery_params.cityId) || -1);

        } else if(e.closest('.product-info__count_additive')){   // для добавочного товара в корзине
            let oldCount = +e.previousElementSibling.value
            let count = +e.previousElementSibling.value + 1
            $("#itemSumm_additive")[0].innerHTML =  $("#itemSumm_additive")[0].innerHTML/oldCount*count
            e.previousElementSibling.value = count
        }
    }
    
}
async function decrement(e){

    if($(".main__wrap .product").length) {  //для товара без опций
        let count = +e.nextElementSibling.value - 1
        count = count < 1 ? 1 : count
        let summ = await currentSumm(count)
        $(".current-price")[0].innerHTML = roundNumber($(".current-price")[0].innerHTML/+e.nextElementSibling.value*count)
        e.nextElementSibling.value = count
        
        if($(".pay.step_2").length != 0)  summDelivery((cart[0].delivery_params && cart[0].delivery_params.cityId) || -1);
        if($(".pay.step_3").length != 0)  summDeliveryStep3((cart[0].delivery_params && cart[0].delivery_params.cityId) || -1);
    } 

    
    else {   // для товара в корзине
        if(e.closest('.product-info__count')){
            let count = +e.nextElementSibling.value - 1
            count = count < 1 ? 1 : count
            e.nextElementSibling.value = count
            let cart = JSON.parse( localStorage.getItem("cart") == "undefined" ? "[]" : (localStorage.getItem("cart") || "[]"));
            let cartKey = e.dataset.key
            for (let i = 0; i < cart.length; i++) {
                if(cart[i].key == cartKey ) {
                    let itemSumm = (+cart[i].itemSumm/cart[i].count*count)
                    cart[i].itemSumm = roundNumber(itemSumm)
                    cart[i].count = count
                }
            }       
              
            localStorage.setItem("cart", JSON.stringify(cart)); 
            cartInitProducts(cart)
            summCalculation(cart)
            
            if($(".pay.step_2").length != 0)  summDelivery((cart[0].delivery_params && cart[0].delivery_params.cityId) || -1);
            if($(".pay.step_3").length != 0)  summDeliveryStep3((cart[0].delivery_params && cart[0].delivery_params.cityId) || -1);

        } else if(e.closest('.product-info__count_additive')){  // для добавочного товара в корзине
            let oldCount = +e.nextElementSibling.value
            let count = +e.nextElementSibling.value - 1
            count = count < 1 ? 1 : count
            $("#itemSumm_additive")[0].innerHTML =  $("#itemSumm_additive")[0].innerHTML/oldCount*count
            e.nextElementSibling.value = count
        }
    }
}
function roundNumber(num){
    let newNum = (+num).toFixed(2)
    if(newNum.toString().slice(-3) == ".00") newNum = newNum.slice(0, -3)
    return newNum
}
function cartInitProducts(cart){

    var el = $('.pay-cart__title');
    let title = el
    if(el) el.remove();
    el = $(".pay-cart__items")    
    for (let i = 0; i < el.length; i++) {
        el[i].remove();            
    }
    let rootElement = $(".pay-cart__box");
    let isPromoCodeActive = JSON.parse(localStorage.getItem("promo") || "[]");
    
    if(!cart.length) $(".main-btn.go-pay")[0].style.opacity = "0.4"
    else if(cart.length ) $(".main-btn.go-pay")[0].style.opacity = "1.0"

    if(isPromoCodeActive.result != "sugess") $(".discount")[0].closest("p").style.display = "none"
    else if(isPromoCodeActive.result == "sugess")  $(".discount")[0].closest("p").style.display = "flex"
    for (let i = cart.length-1; i > -1 ; i--) {

        let itemSumm = roundNumber(cart[i].itemSumm) 
        let payCartItems = `
                                <div class="pay-cart__item">
                                    <img src="${cart[i].imagePath}" data-key="${cart[i].key}" data-urlProduct="${cart[i].urlProduct}" alt="">
                                    <div class="pay-cart__item-info" data-id="${cart[i].id}" data-urlProduct="${cart[i].urlProduct}">
                                        
                                        <p >
                                            ${cart[i].name}
                                        </p>
                                        <span>
                                            ${cart[i].weightParams ? cart[i].weightParams : ""}                                           
                                        </span>
                                        <div class="product-info__count" data-id="${cart[i].id}">
                                            <button class="product-info-decrement" onclick="decrement(this)" data-key="${cart[i].key}">-</button>
                                            <input class="cart-product-info-count-input" value="${cart[i].count}" data-id="${cart[i].id}" type="number" name="product-count">
                                            <button class="product-info-increment" onclick="increment(this)" data-key="${cart[i].key}">+</button>
                                        </div>
                                        
                                    </div>
                                    <div class="pay-cart__item-price">
                                        <span>
                                            <span class="itemSumm">${itemSumm}</span> ₪
                                        </span>
                                    </div>                                    
                                </div>
                                <button class="delete-item" data-key="${cart[i].key}">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M13.4099 12L17.7099 7.71C17.8982 7.5217 18.004 7.2663 18.004 7C18.004 6.7337 17.8982 6.47831 17.7099 6.29C17.5216 6.1017 17.2662 5.99591 16.9999 5.99591C16.7336 5.99591 16.4782 6.1017 16.2899 6.29L11.9999 10.59L7.70994 6.29C7.52164 6.1017 7.26624 5.99591 6.99994 5.99591C6.73364 5.99591 6.47824 6.1017 6.28994 6.29C6.10164 6.47831 5.99585 6.7337 5.99585 7C5.99585 7.2663 6.10164 7.5217 6.28994 7.71L10.5899 12L6.28994 16.29C6.19621 16.383 6.12182 16.4936 6.07105 16.6154C6.02028 16.7373 5.99414 16.868 5.99414 17C5.99414 17.132 6.02028 17.2627 6.07105 17.3846C6.12182 17.5064 6.19621 17.617 6.28994 17.71C6.3829 17.8037 6.4935 17.8781 6.61536 17.9289C6.73722 17.9797 6.86793 18.0058 6.99994 18.0058C7.13195 18.0058 7.26266 17.9797 7.38452 17.9289C7.50638 17.8781 7.61698 17.8037 7.70994 17.71L11.9999 13.41L16.2899 17.71C16.3829 17.8037 16.4935 17.8781 16.6154 17.9289C16.7372 17.9797 16.8679 18.0058 16.9999 18.0058C17.132 18.0058 17.2627 17.9797 17.3845 17.9289C17.5064 17.8781 17.617 17.8037 17.7099 17.71C17.8037 17.617 17.8781 17.5064 17.9288 17.3846C17.9796 17.2627 18.0057 17.132 18.0057 17C18.0057 16.868 17.9796 16.7373 17.9288 16.6154C17.8781 16.4936 17.8037 16.383 17.7099 16.29L13.4099 12Z" fill="#222222"></path>
                                    </svg>                                            
                                </button>
                            `
                  
        for (const item  in cart[i].options) {
            
            if(cart[i].options[item].key == 0 ) continue            
            payCartItems += 
            `<div class="pay-cart__items_option">

                <div class="option_row1" > 
                    <div class="name_option"> ${cart[i].options[item].name}</div>
                    <button class="delete-options" data-key="${cart[i].key}" data-options_key="${item}">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.4099 12L17.7099 7.71C17.8982 7.5217 18.004 7.2663 18.004 7C18.004 6.7337 17.8982 6.47831 17.7099 6.29C17.5216 6.1017 17.2662 5.99591 16.9999 5.99591C16.7336 5.99591 16.4782 6.1017 16.2899 6.29L11.9999 10.59L7.70994 6.29C7.52164 6.1017 7.26624 5.99591 6.99994 5.99591C6.73364 5.99591 6.47824 6.1017 6.28994 6.29C6.10164 6.47831 5.99585 6.7337 5.99585 7C5.99585 7.2663 6.10164 7.5217 6.28994 7.71L10.5899 12L6.28994 16.29C6.19621 16.383 6.12182 16.4936 6.07105 16.6154C6.02028 16.7373 5.99414 16.868 5.99414 17C5.99414 17.132 6.02028 17.2627 6.07105 17.3846C6.12182 17.5064 6.19621 17.617 6.28994 17.71C6.3829 17.8037 6.4935 17.8781 6.61536 17.9289C6.73722 17.9797 6.86793 18.0058 6.99994 18.0058C7.13195 18.0058 7.26266 17.9797 7.38452 17.9289C7.50638 17.8781 7.61698 17.8037 7.70994 17.71L11.9999 13.41L16.2899 17.71C16.3829 17.8037 16.4935 17.8781 16.6154 17.9289C16.7372 17.9797 16.8679 18.0058 16.9999 18.0058C17.132 18.0058 17.2627 17.9797 17.3845 17.9289C17.5064 17.8781 17.617 17.8037 17.7099 17.71C17.8037 17.617 17.8781 17.5064 17.9288 17.3846C17.9796 17.2627 18.0057 17.132 18.0057 17C18.0057 16.868 17.9796 16.7373 17.9288 16.6154C17.8781 16.4936 17.8037 16.383 17.7099 16.29L13.4099 12Z" fill="#222222"></path>
                        </svg>                                            
                    </button>
                </div>
                <div class="option_row2">
                    <div class="option_value">${cart[i].options[item].text || cart[i].options[item].input_text }</div>
                    <div class="option_price"><span class="option_price_value">${cart[i].options[item].price*cart[i].count}</span> ₪</div>
                </div>
                
            </div>`
        }
        let insertedElement = document.createElement('div');
        insertedElement.classList.add("pay-cart__items");
        insertedElement.innerHTML = payCartItems;            
        rootElement.prepend(insertedElement);          
        if($(".option_row2").length !=0) $(".option_row2").last()[0].style.border = "none"
        //#region  Удаление  опций

        $(".delete-options").on('click',async function(e){
            console.log(this)
            let key = this.dataset.key
            let optionKey = this.dataset.options_key
            let cart = JSON.parse( localStorage.getItem("cart") == "undefined" ? "[]" : (localStorage.getItem("cart") || "[]"));
            for (let i = 0; i < cart.length; i++) {
                let newOptions ={}
                if(key == cart[i].key) {                    
                    let arrKeys = Object.keys(cart[i].options)
                    cart[i].key = cart[i].id
                    for (let j = 0; j < arrKeys.length; j++) {
                        if(arrKeys[j] != optionKey){
                            newOptions[arrKeys[j]] = cart[i].options[arrKeys[j]]
                            cart[i].key += `-${cart[i].options[arrKeys[j]].key}-${cart[i].options[arrKeys[j]].value}`
                        } else{
                            cart[i].itemSumm -= cart[i].options[arrKeys[j]].price*cart[i].count
                        }
                    }                    
                    cart[i].options = newOptions
                }
            }
            localStorage.setItem("cart", JSON.stringify(cart));
            cartInitProducts(cart)
            summCalculation(cart)
        })

        //#endregion

    }
    rootElement.prepend(title);
    //#region  Удаление из корзины
    $(".delete-item").on('click', function(e){    
            
        if($("#cart").length) {
            let cart = JSON.parse( localStorage.getItem("cart") == "undefined" ? "[]" : (localStorage.getItem("cart") || "[]"));
            let cartKey = e.currentTarget.dataset.key 
            const newCart = cart.filter( n => n.key.toString() != cartKey )
            localStorage.setItem("cart", JSON.stringify(newCart)); 
            cartInitProducts(newCart)
            summCalculation(newCart)
            $(".cart-count").each(function() { this.innerText = cart.length })
            if(newCart.length == 0)  $(".badge").each(function() { this.style.opacity = "0" });
        }       
    });
    //#endregion
    let promoCart = JSON.parse(localStorage.getItem("promo") || "[]");
    if(promoCart && promoCart.id){
        $(".pay-cart__promo input")[0].value = promoCart.id
        if( $(".sugess_promo")[0].classList.contains("hide")) $(".sugess_promo")[0].classList.remove("hide")
        $(".error_promo")[0].classList.add("hide")
    } 

    let client_data = JSON.parse(localStorage.getItem("client_data") || "[]"); 
    //if(client_data.order_id != ""){
        // $("input[name='order_id']")[0].value = client_data.order_id || ""
        // $("span.order_number_data")[0].innerHTML = client_data.order_id || ""
        if($("input[name='order_id']")[0].value != "") $("span.order_number_data")[0].innerHTML = $("input[name='order_id']")[0].value
        else if ( client_data.order_id != "" ) $("span.order_number_data")[0].innerHTML = client_data.order_id || ""
        $(".order_number_data")[0].style.display = "block"
        $(".pay-cart__title")[0].style.cssText= `display: flex;`
    //}
}
function summCalculation(cart){

    let totalAmount = 0
    let summDiscount = 0
    let summForPayment = 0   
    if(!cart.length) {
        $("#total-ammount")[0].innerHTML = "0"                                                                 
        $("#summ-for-payment")[0].innerHTML = "0"
        $(".discount")[0].innerHTML = roundNumber(summDiscount)
        return
    }     
    let promoCart = JSON.parse(localStorage.getItem("promo") || "[]");
    for (let i = 0; i < cart.length; i++) {
        totalAmount += +cart[i].itemSumm                    
    }
    if(promoCart.unit && promoCart.unit == "%") summDiscount = totalAmount- totalAmount* (100 - promoCart.price)/100    
    if(promoCart.unit && promoCart.unit == "₪") summDiscount = promoCart.price    
    $("#total-ammount")[0].innerHTML = roundNumber(totalAmount)
    $(".discount")[0].innerHTML = roundNumber(summDiscount)
    summForPayment = totalAmount - summDiscount                      
    $("#summ-for-payment")[0].innerHTML = roundNumber(summForPayment)
}
function updateProductPage(){
    
    let url = window.location.href
    let index = url.indexOf("#")
    let key = url.indexOf("#") != -1 ?  url.substring(index+1) : 0
    if(key) reloadPage(key)
    else{
        
        if(!$(".main__wrap .product").length) return  
        imageSrc = $(".product__imgs img")[0].src
        
        if(product.options == null){   //для товара без опций
            $(".current-price")[0].innerHTML =  roundNumber(currentSumm(+$("#count-product")[0].value) || $(".current-price")[0].innerHTML)
            $(".main-btn.go-to-cart").each(function() { this.disabled = false });
            $(".product-info-decrement").each(function() { this.disabled = false });
            $(".product-info-count-input").each(function() { this.disabled = false });
            $(".product-info-increment").each(function() { this.disabled = false });
            $(".main-btn.go-to-cart").each(function() { this.style.opacity = "1.0" });
        } else if ($("input[name='box']").length){   // при загрузке на старте деакивируем радиобоксы и кнопку заказа        
            if(window.location.href.indexOf("#") == -1){
                $("input[name='box']").each(function() { this.disabled = true });
                $(".main-btn.go-to-cart").each(function() { this.disabled = true });
            }
        }
    }

}
async function reloadPage(key){   // при переходе из корзины
    
    let cart = JSON.parse( localStorage.getItem("cart") == "undefined" ? "[]" : (localStorage.getItem("cart") || "[]"));
    selectedItemPrice = +product.price
    // this.closest(".product_option").firstElementChild.innerHTML = this.dataset.option_text       
    // selectedItemWeightParams = $(this).find('.weight-params')[0] && $(this).find('.weight-params')[0].innerHTML ? $(this).find('.weight-params')[0].innerHTML : ""       
    $(".main-btn.go-to-cart").each(function() { this.disabled = false });
    $(".product-info-checkbox").each(function() { this.disabled = false });
    $('.product-info__add input[type="radio"]').each(function() { this.disabled = false });
    $(".product-info-decrement").each(function() { this.disabled = false });
    $(".product-info-count-input").each(function() { this.disabled = false });
    $(".product-info-increment").each(function() { this.disabled = false });
    $("input[name='box']").each(function() { this.disabled = false });
    $(".main-btn.go-to-cart").each(function() { this.style.opacity = "1.0" }); 
    let options= $(".product_option")
    let item = cart.filter( n => n.key === key )[0]
    if(options.length !=0){
        for (let i = 0; i < options.length; i++) {
            if(item.options[i] != undefined){
                if(item.options[i].type == "SIZE"){ 
                    let el = options[i].querySelectorAll(".product-size-var.option_value")                
                    el[+item.options[i].value].classList.add("active")
                    let el2 = el[+item.options[i].value].closest(".product-info__size.product_option")
                    if(el2.classList.contains("required")) el2.classList.remove("required")
                    el[+item.options[i].value].closest(".product_option").firstElementChild.innerHTML =`<span>${ el[+item.options[i].value].dataset.option_text}</span>` 
                } else if(item.options[i].type == "TEXT"){
                    let el = options[i]
                    let el2 = el.querySelector(".product-info-checkbox")
                    el2.checked = true
                    let el3 = el2.closest(".product-info__add.product_option").querySelector(".body-product-info-add")
                    el3.classList.add("active")
                    el3.style.marginBottom = "30px"
                    el3.querySelector(".option_value").classList.add("active") 
                    el3.querySelector(".trans-btn").innerHTML = "Убрать текст"
                    el3.querySelector(".body-product-info-input-text.option_input_text").value = item.options[i].input_text
                } else if(item.options[i].type == "RADIO"){
                    let el = options[i]
                    let el2 = el.querySelectorAll(".option_value")
                    let el3 = el2[+item.options[i].value]
                    el3.classList.add("active")
                    el3.querySelector("input[name='box']").checked = true
                }else if(item.options[i].type == "SELECT"){
                    let el = options[i].querySelectorAll(".product-size-var.option_value")                
                    el[+item.options[i].value].classList.add("active")
                    let el2 = el[+item.options[i].value].closest(".product-info__size.product_option")
                    if(el2.classList.contains("required")) el2.classList.remove("required")
                    el[+item.options[i].value].closest(".product_option").firstElementChild.innerHTML =`<span>${ el[+item.options[i].value].dataset.option_text}</span>`              
                }
            }         
        }
    } else{        
        $(".current-price")[0].innerHTML = roundNumber(item.itemSumm)
    }
    $('#count-product')[0].value = item.count

    await currentSumm($('#count-product').val())    
}
function isRequiredChecked(){

    let req = $(".product_option.required")
    if(!req.length) return true
    for (let i = 0; i < 7; i++) {
        setTimeout(() => {
            if (i%2) $(".product_option.required").each(function() {
                    $(this).find('.open-size-table')[0].style.cssText= `
                        border: 2px solid red;
                    `
                }); 
            else $(".product_option.required").each(function() { 
                $(this).find('.open-size-table')[0].style.cssText= `
                        border: 2px solid #000;
                    `
            });            
            if(i == 6 ) return false
        }, i*250);
    }
}
//#region  Country
if($("#cart").length)
{
    let maskNum = [    
        {pattern:"[0-9]{3}\s[0-9]{3}\s[0-9]{2}\s[0-9]{2}", mask: "(999) 999 99 99"},
        // {pattern:"\(?[0-9]{3}\) [0-9]{3} [0-9]{2} [0-9]{2}/gm", mask: "(999) 999 99 99"},
        // {pattern:"^\(\d{3}\) \d{3}-\d{2}-\d{2}", mask: "(999) 999 99 99"},        
        // {pattern:"\(?[0-9]{3}[\-\)][0-9]{3}-[0-9]{2}-[0-9]{2}/gm", mask: "(999) 999 99 99"},
        // {pattern:"\([0-9]{3}\)[0-9]{3}-[0-9]{2}-[0-9]{2}", mask: "(999) 999 99 99"},

    ]

    let country = { data:[

    {name: 'Afghanistan (‫افغانستان‬‎)', iso2: 'af', dialCode: '93', priority: 0, areaCodes: null, maskNum:0},
    {name: 'Albania (Shqipëri)', iso2: 'al', dialCode: '355', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Algeria (‫الجزائر‬‎)', iso2: 'dz', dialCode: '213', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'American Samoa', iso2: 'as', dialCode: '1', priority: 5, areaCodes: Array(1), maskNum:0 },
    {name: 'Andorra', iso2: 'ad', dialCode: '376', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Angola', iso2: 'ao', dialCode: '244', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Anguilla', iso2: 'ai', dialCode: '1', priority: 6, areaCodes: Array(1), maskNum:0 },
    {name: 'Antigua and Barbuda', iso2: 'ag', dialCode: '1', priority: 7, areaCodes: Array(1), maskNum:0 },
    {name: 'Argentina', iso2: 'ar', dialCode: '54', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Armenia (Հայաստան)', iso2: 'am', dialCode: '374', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Aruba', iso2: 'aw', dialCode: '297', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Ascension Island', iso2: 'ac', dialCode: '247', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Australia', iso2: 'au', dialCode: '61', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Austria (Österreich)', iso2: 'at', dialCode: '43', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Azerbaijan (Azərbaycan)', iso2: 'az', dialCode: '994', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Bahamas', iso2: 'bs', dialCode: '1', priority: 8, areaCodes: Array(1), maskNum:0 },
    {name: 'Bahrain (‫البحرين‬‎)', iso2: 'bh', dialCode: '973', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Bangladesh (বাংলাদেশ)', iso2: 'bd', dialCode: '880', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Barbados', iso2: 'bb', dialCode: '1', priority: 9, areaCodes: Array(1), maskNum:0 },
    {name: 'Belarus (Беларусь)', iso2: 'by', dialCode: '375', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Belgium (België)', iso2: 'be', dialCode: '32', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Belize', iso2: 'bz', dialCode: '501', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Benin (Bénin)', iso2: 'bj', dialCode: '229', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Bermuda', iso2: 'bm', dialCode: '1', priority: 10, areaCodes: Array(1), maskNum:0 },
    {name: 'Bhutan (འབྲུག)', iso2: 'bt', dialCode: '975', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Bolivia', iso2: 'bo', dialCode: '591', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Bosnia and Herzegovina (Босна и Херцеговина)', iso2: 'ba', dialCode: '387', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Botswana', iso2: 'bw', dialCode: '267', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Brazil (Brasil)', iso2: 'br', dialCode: '55', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'British Indian Ocean Territory', iso2: 'io', dialCode: '246', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'British Virgin Islands', iso2: 'vg', dialCode: '1', priority: 11, areaCodes: Array(1), maskNum:0 },
    {name: 'Brunei', iso2: 'bn', dialCode: '673', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Bulgaria (България)', iso2: 'bg', dialCode: '359', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Burkina Faso', iso2: 'bf', dialCode: '226', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Burundi (Uburundi)', iso2: 'bi', dialCode: '257', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Cambodia (កម្ពុជា)', iso2: 'kh', dialCode: '855', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Cameroon (Cameroun)', iso2: 'cm', dialCode: '237', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Canada', iso2: 'ca', dialCode: '1', priority: 1, areaCodes: Array(42), maskNum:0 },
    {name: 'Cape Verde (Kabu Verdi)', iso2: 'cv', dialCode: '238', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Caribbean Netherlands', iso2: 'bq', dialCode: '599', priority: 1, areaCodes: Array(3), maskNum:0 },
    {name: 'Cayman Islands', iso2: 'ky', dialCode: '1', priority: 12, areaCodes: Array(1), maskNum:0 },
    {name: 'Central African Republic (République centrafricaine)', iso2: 'cf', dialCode: '236', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Chad (Tchad)', iso2: 'td', dialCode: '235', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Chile', iso2: 'cl', dialCode: '56', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'China (中国)', iso2: 'cn', dialCode: '86', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Christmas Island', iso2: 'cx', dialCode: '61', priority: 2, areaCodes: Array(1), maskNum:0 },
    {name: 'Cocos (Keeling) Islands', iso2: 'cc', dialCode: '61', priority: 1, areaCodes: Array(1), maskNum:0 },
    {name: 'Colombia', iso2: 'co', dialCode: '57', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Comoros (‫جزر القمر‬‎)', iso2: 'km', dialCode: '269', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Congo (DRC) (Jamhuri ya Kidemokrasia ya Kongo)', iso2: 'cd', dialCode: '243', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Congo (Republic) (Congo-Brazzaville)', iso2: 'cg', dialCode: '242', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Cook Islands', iso2: 'ck', dialCode: '682', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Costa Rica', iso2: 'cr', dialCode: '506', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Côte d’Ivoire', iso2: 'ci', dialCode: '225', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Croatia (Hrvatska)', iso2: 'hr', dialCode: '385', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Cuba', iso2: 'cu', dialCode: '53', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Curaçao', iso2: 'cw', dialCode: '599', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Cyprus (Κύπρος)', iso2: 'cy', dialCode: '357', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Czech Republic (Česká republika)', iso2: 'cz', dialCode: '420', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Denmark (Danmark)', iso2: 'dk', dialCode: '45', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Djibouti', iso2: 'dj', dialCode: '253', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Dominica', iso2: 'dm', dialCode: '1', priority: 13, areaCodes: Array(1), maskNum:0 },
    {name: 'Dominican Republic (República Dominicana)', iso2: 'do', dialCode: '1', priority: 2, areaCodes: Array(3), maskNum:0 },
    {name: 'Ecuador', iso2: 'ec', dialCode: '593', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Egypt (‫مصر‬‎)', iso2: 'eg', dialCode: '20', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'El Salvador', iso2: 'sv', dialCode: '503', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Equatorial Guinea (Guinea Ecuatorial)', iso2: 'gq', dialCode: '240', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Eritrea', iso2: 'er', dialCode: '291', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Estonia (Eesti)', iso2: 'ee', dialCode: '372', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Eswatini', iso2: 'sz', dialCode: '268', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Ethiopia', iso2: 'et', dialCode: '251', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Falkland Islands (Islas Malvinas)', iso2: 'fk', dialCode: '500', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Faroe Islands (Føroyar)', iso2: 'fo', dialCode: '298', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Fiji', iso2: 'fj', dialCode: '679', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Finland (Suomi)', iso2: 'fi', dialCode: '358', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'France', iso2: 'fr', dialCode: '33', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'French Guiana (Guyane française)', iso2: 'gf', dialCode: '594', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'French Polynesia (Polynésie française)', iso2: 'pf', dialCode: '689', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Gabon', iso2: 'ga', dialCode: '241', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Gambia', iso2: 'gm', dialCode: '220', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Georgia (საქართველო)', iso2: 'ge', dialCode: '995', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Germany (Deutschland)', iso2: 'de', dialCode: '49', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Ghana (Gaana)', iso2: 'gh', dialCode: '233', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Gibraltar', iso2: 'gi', dialCode: '350', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Greece (Ελλάδα)', iso2: 'gr', dialCode: '30', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Greenland (Kalaallit Nunaat)', iso2: 'gl', dialCode: '299', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Grenada', iso2: 'gd', dialCode: '1', priority: 14, areaCodes: Array(1), maskNum:0 },
    {name: 'Guadeloupe', iso2: 'gp', dialCode: '590', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Guam', iso2: 'gu', dialCode: '1', priority: 15, areaCodes: Array(1), maskNum:0 },
    {name: 'Guatemala', iso2: 'gt', dialCode: '502', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Guernsey', iso2: 'gg', dialCode: '44', priority: 1, areaCodes: Array(4), maskNum:0 },
    {name: 'Guinea (Guinée)', iso2: 'gn', dialCode: '224', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Guinea-Bissau (Guiné Bissau)', iso2: 'gw', dialCode: '245', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Guyana', iso2: 'gy', dialCode: '592', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Haiti', iso2: 'ht', dialCode: '509', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Honduras', iso2: 'hn', dialCode: '504', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Hong Kong (香港)', iso2: 'hk', dialCode: '852', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Hungary (Magyarország)', iso2: 'hu', dialCode: '36', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Iceland (Ísland)', iso2: 'is', dialCode: '354', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'India (भारत)', iso2: 'in', dialCode: '91', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Indonesia', iso2: 'id', dialCode: '62', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Iran (‫ایران‬‎)', iso2: 'ir', dialCode: '98', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Iraq (‫العراق‬‎)', iso2: 'iq', dialCode: '964', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Ireland', iso2: 'ie', dialCode: '353', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Isle of Man', iso2: 'im', dialCode: '44', priority: 2, areaCodes: Array(5), maskNum:0 },
    {name: 'Israel (‫ישראל‬‎)', iso2: 'il', dialCode: '972', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Italy (Italia)', iso2: 'it', dialCode: '39', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Jamaica', iso2: 'jm', dialCode: '1', priority: 4, areaCodes: Array(2), maskNum:0 },
    {name: 'Japan (日本)', iso2: 'jp', dialCode: '81', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Jersey', iso2: 'je', dialCode: '44', priority: 3, areaCodes: Array(6), maskNum:0 },
    {name: 'Jordan (‫الأردن‬‎)', iso2: 'jo', dialCode: '962', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Kazakhstan (Казахстан)', iso2: 'kz', dialCode: '7', priority: 1, areaCodes: Array(2), maskNum:0 },
    {name: 'Kenya', iso2: 'ke', dialCode: '254', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Kiribati', iso2: 'ki', dialCode: '686', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Kosovo', iso2: 'xk', dialCode: '383', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Kuwait (‫الكويت‬‎)', iso2: 'kw', dialCode: '965', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Kyrgyzstan (Кыргызстан)', iso2: 'kg', dialCode: '996', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Laos (ລາວ)', iso2: 'la', dialCode: '856', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Latvia (Latvija)', iso2: 'lv', dialCode: '371', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Lebanon (‫لبنان‬‎)', iso2: 'lb', dialCode: '961', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Lesotho', iso2: 'ls', dialCode: '266', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Liberia', iso2: 'lr', dialCode: '231', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Libya (‫ليبيا‬‎)', iso2: 'ly', dialCode: '218', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Liechtenstein', iso2: 'li', dialCode: '423', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Lithuania (Lietuva)', iso2: 'lt', dialCode: '370', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Luxembourg', iso2: 'lu', dialCode: '352', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Macau (澳門)', iso2: 'mo', dialCode: '853', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Macedonia (FYROM) (Македонија)', iso2: 'mk', dialCode: '389', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Madagascar (Madagasikara)', iso2: 'mg', dialCode: '261', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Malawi', iso2: 'mw', dialCode: '265', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Malaysia', iso2: 'my', dialCode: '60', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Maldives', iso2: 'mv', dialCode: '960', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Mali', iso2: 'ml', dialCode: '223', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Malta', iso2: 'mt', dialCode: '356', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Marshall Islands', iso2: 'mh', dialCode: '692', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Martinique', iso2: 'mq', dialCode: '596', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Mauritania (‫موريتانيا‬‎)', iso2: 'mr', dialCode: '222', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Mauritius (Moris)', iso2: 'mu', dialCode: '230', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Mayotte', iso2: 'yt', dialCode: '262', priority: 1, areaCodes: Array(2), maskNum:0 },
    {name: 'Mexico (México)', iso2: 'mx', dialCode: '52', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Micronesia', iso2: 'fm', dialCode: '691', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Moldova (Republica Moldova)', iso2: 'md', dialCode: '373', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Monaco', iso2: 'mc', dialCode: '377', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Mongolia (Монгол)', iso2: 'mn', dialCode: '976', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Montenegro (Crna Gora)', iso2: 'me', dialCode: '382', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Montserrat', iso2: 'ms', dialCode: '1', priority: 16, areaCodes: Array(1), maskNum:0 },
    {name: 'Morocco (‫المغرب‬‎)', iso2: 'ma', dialCode: '212', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Mozambique (Moçambique)', iso2: 'mz', dialCode: '258', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Myanmar (Burma) (မြန်မာ)', iso2: 'mm', dialCode: '95', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Namibia (Namibië)', iso2: 'na', dialCode: '264', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Nauru', iso2: 'nr', dialCode: '674', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Nepal (नेपाल)', iso2: 'np', dialCode: '977', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Netherlands (Nederland)', iso2: 'nl', dialCode: '31', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'New Caledonia (Nouvelle-Calédonie)', iso2: 'nc', dialCode: '687', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'New Zealand', iso2: 'nz', dialCode: '64', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Nicaragua', iso2: 'ni', dialCode: '505', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Niger (Nijar)', iso2: 'ne', dialCode: '227', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Nigeria', iso2: 'ng', dialCode: '234', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Niue', iso2: 'nu', dialCode: '683', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Norfolk Island', iso2: 'nf', dialCode: '672', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'North Korea (조선 민주주의 인민 공화국)', iso2: 'kp', dialCode: '850', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Northern Mariana Islands', iso2: 'mp', dialCode: '1', priority: 17, areaCodes: Array(1), maskNum:0 },
    {name: 'Norway (Norge)', iso2: 'no', dialCode: '47', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Oman (‫عُمان‬‎)', iso2: 'om', dialCode: '968', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Pakistan (‫پاکستان‬‎)', iso2: 'pk', dialCode: '92', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Palau', iso2: 'pw', dialCode: '680', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Palestine (‫فلسطين‬‎)', iso2: 'ps', dialCode: '970', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Panama (Panamá)', iso2: 'pa', dialCode: '507', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Papua New Guinea', iso2: 'pg', dialCode: '675', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Paraguay', iso2: 'py', dialCode: '595', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Peru (Perú)', iso2: 'pe', dialCode: '51', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Philippines', iso2: 'ph', dialCode: '63', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Poland (Polska)', iso2: 'pl', dialCode: '48', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Portugal', iso2: 'pt', dialCode: '351', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Puerto Rico', iso2: 'pr', dialCode: '1', priority: 3, areaCodes: Array(2), maskNum:0 },
    {name: 'Qatar (‫قطر‬‎)', iso2: 'qa', dialCode: '974', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Réunion (La Réunion)', iso2: 're', dialCode: '262', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Romania (România)', iso2: 'ro', dialCode: '40', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Russia (Россия)', iso2: 'ru', dialCode: '7', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Rwanda', iso2: 'rw', dialCode: '250', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Saint Barthélemy', iso2: 'bl', dialCode: '590', priority: 1, areaCodes: null, maskNum:0 },
    {name: 'Saint Helena', iso2: 'sh', dialCode: '290', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Saint Kitts and Nevis', iso2: 'kn', dialCode: '1', priority: 18, areaCodes: Array(1), maskNum:0 },
    {name: 'Saint Lucia', iso2: 'lc', dialCode: '1', priority: 19, areaCodes: Array(1), maskNum:0 },
    {name: 'Saint Martin (Saint-Martin (partie française))', iso2: 'mf', dialCode: '590', priority: 2, areaCodes: null, maskNum:0 },
    {name: 'Saint Pierre and Miquelon (Saint-Pierre-et-Miquelon)', iso2: 'pm', dialCode: '508', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Saint Vincent and the Grenadines', iso2: 'vc', dialCode: '1', priority: 20, areaCodes: Array(1), maskNum:0 },
    {name: 'Samoa', iso2: 'ws', dialCode: '685', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'San Marino', iso2: 'sm', dialCode: '378', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'São Tomé and Príncipe (São Tomé e Príncipe)', iso2: 'st', dialCode: '239', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Saudi Arabia (‫المملكة العربية السعودية‬‎)', iso2: 'sa', dialCode: '966', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Senegal (Sénégal)', iso2: 'sn', dialCode: '221', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Serbia (Србија)', iso2: 'rs', dialCode: '381', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Seychelles', iso2: 'sc', dialCode: '248', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Sierra Leone', iso2: 'sl', dialCode: '232', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Singapore', iso2: 'sg', dialCode: '65', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Sint Maarten', iso2: 'sx', dialCode: '1', priority: 21, areaCodes: Array(1), maskNum:0 },
    {name: 'Slovakia (Slovensko)', iso2: 'sk', dialCode: '421', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Slovenia (Slovenija)', iso2: 'si', dialCode: '386', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Solomon Islands', iso2: 'sb', dialCode: '677', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Somalia (Soomaaliya)', iso2: 'so', dialCode: '252', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'South Africa', iso2: 'za', dialCode: '27', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'South Korea (대한민국)', iso2: 'kr', dialCode: '82', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'South Sudan (‫جنوب السودان‬‎)', iso2: 'ss', dialCode: '211', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Spain (España)', iso2: 'es', dialCode: '34', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Sri Lanka (ශ්‍රී ලංකාව)', iso2: 'lk', dialCode: '94', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Sudan (‫السودان‬‎)', iso2: 'sd', dialCode: '249', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Suriname', iso2: 'sr', dialCode: '597', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Svalbard and Jan Mayen', iso2: 'sj', dialCode: '47', priority: 1, areaCodes: Array(1), maskNum:0 },
    {name: 'Sweden (Sverige)', iso2: 'se', dialCode: '46', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Switzerland (Schweiz)', iso2: 'ch', dialCode: '41', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Syria (‫سوريا‬‎)', iso2: 'sy', dialCode: '963', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Taiwan (台灣)', iso2: 'tw', dialCode: '886', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Tajikistan', iso2: 'tj', dialCode: '992', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Tanzania', iso2: 'tz', dialCode: '255', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Thailand (ไทย)', iso2: 'th', dialCode: '66', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Timor-Leste', iso2: 'tl', dialCode: '670', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Togo', iso2: 'tg', dialCode: '228', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Tokelau', iso2: 'tk', dialCode: '690', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Tonga', iso2: 'to', dialCode: '676', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Trinidad and Tobago', iso2: 'tt', dialCode: '1', priority: 22, areaCodes: Array(1), maskNum:0 },
    {name: 'Tunisia (‫تونس‬‎)', iso2: 'tn', dialCode: '216', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Turkey (Türkiye)', iso2: 'tr', dialCode: '90', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Turkmenistan', iso2: 'tm', dialCode: '993', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Turks and Caicos Islands', iso2: 'tc', dialCode: '1', priority: 23, areaCodes: Array(1), maskNum:0 },
    {name: 'Tuvalu', iso2: 'tv', dialCode: '688', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'U.S. Virgin Islands', iso2: 'vi', dialCode: '1', priority: 24, areaCodes: Array(1), maskNum:0 },
    {name: 'Uganda', iso2: 'ug', dialCode: '256', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Ukraine (Україна)', iso2: 'ua', dialCode: '38', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'United Arab Emirates (‫الإمارات العربية المتحدة‬‎)', iso2: 'ae', dialCode: '971', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'United Kingdom', iso2: 'gb', dialCode: '44', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'United States', iso2: 'us', dialCode: '1', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Uruguay', iso2: 'uy', dialCode: '598', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Uzbekistan (Oʻzbekiston)', iso2: 'uz', dialCode: '998', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Vanuatu', iso2: 'vu', dialCode: '678', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Vatican City (Città del Vaticano)', iso2: 'va', dialCode: '39', priority: 1, areaCodes: Array(1), maskNum:0 },
    {name: 'Venezuela', iso2: 've', dialCode: '58', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Vietnam (Việt Nam)', iso2: 'vn', dialCode: '84', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Wallis and Futuna (Wallis-et-Futuna)', iso2: 'wf', dialCode: '681', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Western Sahara (‫الصحراء الغربية‬‎)', iso2: 'eh', dialCode: '212', priority: 1, areaCodes: Array(2), maskNum:0 },
    {name: 'Yemen (‫اليمن‬‎)', iso2: 'ye', dialCode: '967', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Zambia', iso2: 'zm', dialCode: '260', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Zimbabwe', iso2: 'zw', dialCode: '263', priority: 0, areaCodes: null, maskNum:0 },
    {name: 'Åland Islands', iso2: 'ax', dialCode: '358', priority: 1, areaCodes: Array(1), maskNum:0 } ]}

    // $(".phone-mask")[0].innerHTML = ``
    // $(".phone-mask").append(`<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.js"></script>`)
    // $(".phone-mask").append(`<p>Телефон *</p>`)

    $.getJSON('https://ipinfo.io', function(data){ 
            
            let currentCountry = country.data.filter( n => n.iso2 === data.country.toLowerCase())[0]
            let mask = maskNum[currentCountry.maskNum].mask
            // let pattern = maskNum[currentCountry.maskNum].pattern
            
            let  isPayStep2 = $(".pay.step_2")           
            if(isPayStep2 && isPayStep2.length) {
                data.country = "IL"
                currentCountry.dialCode = "972"
            }
            

            // $(".phone-mask").append(`<input name="user-phone" id="user-phone" type="phone" placeholder="(000) 123 45 67" pattern="${pattern}">`)
            $(".phone-mask").append(`<input name="user-phone" id="user-phone" type="phone" placeholder="(000) 123 45 67" >`)
            

            let insertString = `<div class="iti__selected-flag" data-iso2="${data.country.toLowerCase()}" >
                                    <div class="iti__flag iti__${data.country.toLowerCase()}"></div>
                                    <div class="iti__selected-dial-code">&nbsp;&nbsp;+${currentCountry.dialCode}</div>
                                    <div class="iti__arrow"></div>
                                </div>
                                `
            insertString+=  `<ul class="iti__country-list iti__hide" id="country-list" aria-label="List of countries">`
            for (let i = 0; i < country.data.length; i++) {
                insertString += `
                                    <li class="iti__country iti__standard" tabindex="-1" id="iti-0__item-${country.data[i].iso2}" role="option" data-dial-code="${country.data[i].dialCode}" data-country-code="${country.data[i].iso2}" aria-selected="false">
                                        <div class="iti__flag-box">
                                            <div class="iti__flag iti__${country.data[i].iso2}"></div>
                                        </div>
                                        <span class="iti__country-name">${country.data[i].name}</span>
                                        <span class="iti__dial-code">+${country.data[i].dialCode}</span>
                                    </li>
                                `
            }
            
            insertString += `</ul>`
            $(".phone-mask").append(insertString)

            setTimeout(() => {
                $("input[name='user-phone']").mask(`${mask}`, { autoclear: false });

                /** Нажатие на выбор страны / телефона */                
                $(".iti__selected-flag").on('click',async function(e){
                    if(isPayStep2 && isPayStep2.length) {return}
                    let countryList = $("#country-list")[0]
                    if(countryList.classList.contains("iti__hide")){
                        countryList.classList.remove("iti__hide")
                        $(".iti__arrow")[0].classList.add("iti__arrow--up")
                    } 
                    else{
                        countryList.classList.add("iti__hide")
                        $(".iti__arrow")[0].classList.remove("iti__arrow--up")
                    } 
                });
                
                $(".iti__country").on('click',async function(e){
                    if(isPayStep2 && isPayStep2.length) {return}
                    let dialCode = this.dataset.dialCode
                    let countryCode = this.dataset.countryCode
                    $(".iti__selected-flag")[0].dataset.iso2 = countryCode                    
                    $(".iti__selected-flag")[0].innerHTML = `
                                                            <div class="iti__flag iti__${countryCode}"></div>
                                                            <div class="iti__selected-dial-code">&nbsp;&nbsp;+${dialCode}</div>
                                                            <div class="iti__arrow"></div>
                                                        `
                    $("#country-list")[0].classList.add("iti__hide")
                    $('input[name="user-phone"]')[0].value = ""
                });
                
                $('input[name="user-phone"]').click(function(){
                    $(this).focus();  // set position number
                });

                $("#user-phone").blur(function(e){  
                        let el = this.value.slice(-1)
                        if(el == "_") {
                            this.style.cssText= `
                                border: 2px solid red;
                            `
                        } else{ 
                            this.style.cssText= `
                                border: 2px solid #000;
                            `
                        }
                        return false
                });
                
                $("#user-phone").keyup(function(e){  
                    let el = this.value.slice(-1)
                    if(el == "_") {
                        this.style.cssText= `
                            border: 2px solid red;
                        `
                    } else{ 
                        this.style.cssText= `
                            border: 2px solid #000;
                        `
                    }    

                    if (e.keyCode === 13){
                        this.blur()                        
                        this.style.cssText= `
                            border: 2px solid #000;
                        `
                        return false
                    } 
                });


                
            }, 1000);
    });
    
    $("form.form-cart1").on("submit", function(e) {
                
        window.scrollTo(0, 0); 
        setOrderDate()
        
        let telIsValide = telValidator($("#user-phone")[0].value) 
        if(!telIsValide || cart.length == 0) return false
        let str =  $(".iti__selected-dial-code")[0].innerHTML
        let phone = str.substring(str.indexOf("+"))+ " -" + $("input[name='user-phone']")[0].value
        phone = phone.replace(/-/g, '')
        $("input[name='phone']")[0].value = phone
        const formData = new FormData(e.currentTarget);
        var client_data = {};
        client_data.phone = formData.get("phone")
        client_data.clientName = formData.get("clientName")
        client_data.clientBirthDay = formData.get("clientBirthDay")
        client_data.email = formData.get("email")        
        client_data.order_id = formData.get("order_id") == "undefined" ? "" : formData.get("order_id")
        localStorage.setItem("client_data", JSON.stringify(client_data));

    });

   

    $("form.form-cart2").on("submit", function() { 
        
        setOrderDate()
        let client_data = JSON.parse(localStorage.getItem("client_data") || "[]");
        client_data.order_id = $("input[name='order_id']")[0].value || ""
        localStorage.setItem("client_data", JSON.stringify(client_data));
        let str =  $(".iti__selected-dial-code")[0].innerHTML
        let phone = str.substring(str.indexOf("+"))+ " -" + $("input[name='user-phone']")[0].value
        phone = phone.replace(/-/g, '')        
        $("input[name='delivery_method']")[0].value = $("input[name='delivery']:checked")[0].value
        $("input[name='user-phone']")[0].value = phone   
       
       
    });

    $("form.form-cart3").on("submit", function() {        

        setOrderDate()
        window.scrollTo(0, 0); 

        $(".popup").fadeIn(500);
      
       
    });


    
    
    function setOrderDate(){
        let cart = JSON.parse( localStorage.getItem("cart") == "undefined" ? "[]" : (localStorage.getItem("cart") || "[]"));
        let promo = JSON.parse(localStorage.getItem("promo") || "[]");
        let response = {products: cart, promo_code: promo.id, promo: promo}
        $("input[name='order_data']")[0].value = JSON.stringify(response)
    }

}

function telValidator(tel){
    
    tel = tel.replace("(","")
    tel = tel.replace(")","")
    tel = tel.replace(/_/g, '')
    tel = tel.replace(/-/g, '')
    tel = tel.replace(/ /g, '')
    if(tel.length == 10) return true
    console.log(`tel.length= ${tel.length}, tel = ${tel} `)
    $("#user-phone")[0].style.cssText= `
        border: 2px solid red;
    `
    return false
}
//#endregion

//#region  ПРОМОКОД

$(".submit-promo").on('click',async function(e){    
    promoAction()
});
$(".pay-cart__promo input").on('blur',async function(e){    
    promoAction()
})
$(".pay-cart__promo input").keyup(async function(e){
    if(e.keyCode == 13) {        
        promoAction()
    }
})
async function promoAction(){
    let promoText = $(".pay-cart__promo input").val()
    let cart = JSON.parse( localStorage.getItem("cart") == "undefined" ? "[]" : (localStorage.getItem("cart") || "[]"));
    let url = $(".pay-cart__promo button")[0].dataset.url
    let response = await fetch(`${url}${promoText}`);
    if (response.ok) {  
        try {
            let promo = await response.json();           
            if(promo.result == "sugess" ){
                if( $(".sugess_promo")[0].classList.contains("hide")) $(".sugess_promo")[0].classList.remove("hide")
                $(".error_promo")[0].classList.add("hide")
                localStorage.setItem("promo", JSON.stringify(promo));
                summCalculation(cart)
            }
            if(promo.result == "error"){
                if( $(".error_promo")[0].classList.contains("hide")) $(".error_promo")[0].classList.remove("hide")
                $(".sugess_promo")[0].classList.add("hide")
                localStorage.removeItem('promo');
                summCalculation(cart)
            }
            

        } catch (error) {
            if( $(".error_promo")[0].classList.contains("hide")) $(".error_promo")[0].classList.remove("hide")
            $(".sugess_promo")[0].classList.add("hide")
            localStorage.removeItem('promo');
        }
      } else {
        // alert("Ошибка HTTP: " + response.status);
      }
}

//#endregion

 //#region  Ввод города/улицы

let cityId = -1
$(".city_name").keyup(function(){
    
    

    let searchString = this.value    
    let el = $('.city-list');
    if(el) el.remove();
    if(searchString == "") return 
    cityesNew =[]
    langIsSearch = ""
    cityesNew = cityes.citys_all.filter( e =>{
        if (e.en == null) return false
        return e.en.toLowerCase().startsWith(searchString.toLowerCase()) && e.en!="all"
    })
    if(cityesNew.length) langIsSearch = "en"
    if(!cityesNew.length)
    cityesNew = cityes.citys_all.filter( e =>{
        if (e.ru == null) return false
        return e.ru.toLowerCase().startsWith(searchString.toLowerCase())  && e.ru!="all"
    })
    if(cityesNew.length && langIsSearch == "") langIsSearch = "ru"
    if(!cityesNew.length)
    cityesNew = cityes.citys_all.filter( e =>{
        if (e.he == null) return false
        return e.he.toLowerCase().startsWith(searchString.toLowerCase())  && e.he!="all"
    })
    if(cityesNew.length  && langIsSearch == "") langIsSearch = "he"
        let listCity = `<ul class="city-list">`
        for (let i = 0; i < cityesNew.length; i++) {
            listCity += `
                            <li >                            
                                <span >${ langIsSearch == "en" ? cityesNew[i].en :  langIsSearch == "ru" ? cityesNew[i].ru : cityesNew[i].he }</span>                            
                            </li>   
                        `
        }            
        listCity += `</ul>`
        $(".city_name")[0].closest("label").classList.add("city-select")
        $(".city-select").append(listCity)

        if(searchString == "") {
            cityId = -1
            summDelivery(cityId)
        } 
        $(".city-list li").mouseenter(function() {
            this.style.background = "#f6f4ec";
        });
        $(".city-list li").mouseleave(function() {
            this.style.background = "none";
        });
        $(".city-list li").on('click', function(e) {                        
            $(".city_name")[0].value = this.innerText            
            for (let i = 0; i < cityes.citys_all.length; i++) {
                if(cityes.citys_all[i].en == this.innerText){ cityId = i; $("input[name='city_id']")[0].value = cityId.toString(); } 
                if(cityes.citys_all[i].he == this.innerText){ cityId = i; $("input[name='city_id']")[0].value = cityId.toString(); }
                if(cityes.citys_all[i].ru == this.innerText){ cityId = i; $("input[name='city_id']")[0].value = cityId.toString(); }
                if(cityId != -1) { $("input[name='city_id']").value = ""; break } 
            }            
            if(cityId != -1){               
                summDelivery(cityId)                
            }
            let el = $('.city-list');
            if(el) el.remove();           
        });
});

$("label.errors input").change(function(){   

    console.log(this)
    if( this.nextElementSibling.classList.contains("errors"))  this.nextElementSibling.classList.remove("errors")
    if( this.closest("label").classList.contains("errors"))  this.closest("label").classList.remove("errors")
    this.nextElementSibling.style.display = "none"
});


$(".city_name").blur(function(){
    setTimeout(() => {        
        if($('.city-list').length == 0) return
        $(".city_name")[0].value =""
        $("input[name='city_id']").value = "";
        cityId = -1
        let el = $('.city-list');
        if(el) el.remove(); 
        summDelivery(cityId)  
    }, 500);
     
})
$("input[name='delivery']").change(function(){   // переключалка самовывоза
    if(this.value == "pickup"){

        $(".delivery label .delivery").each(function() { this.style.display = "none" });
        $(".delivery label .pickup").each(function() { this.style.display = "block" });
        $(".delivery_address")[0].style.display = "none"
        $(".other-man")[0].style.display = "none"
        $(".city_name")[0].value = ""
        $("input[name='time']")[0].value = "";
        $("input[name='city_id']")[0].value = ""; 
        cityId = -1
        $(".delivery_price")[0].innerHTML = 0 + " ₪"
        $(".delivery input.show_calendar.date")[0].placeholder = $(".delivery input.show_calendar.date")[0].dataset.text_delivery
        let cart = JSON.parse( localStorage.getItem("cart") == "undefined" ? "[]" : (localStorage.getItem("cart") || "[]"));
        if(cart[0] && cart[0].delivery_params && cart[0].delivery_params.cityId) cart[0].delivery_params.cityId = -1
        localStorage.setItem("cart", JSON.stringify(cart));
        summDelivery(cityId)
    }    
    else if (this.value == "delivery"){
        $(".delivery label .delivery").each(function() { this.style.display = "block" });
        $(".delivery label .pickup").each(function() { this.style.display = "none" });
        $(".delivery_address")[0].style.display = "block"
        $(".other-man")[0].style.display = "block"
        $(".delivery_address")[0].style.display = "flex"
        $(".delivery input.show_calendar.date")[0].placeholder = $(".delivery input.show_calendar.date")[0].dataset.text_pickup
        summDelivery(cityId)
    } 
});


$("input.delivery_time").on('click',async function(e){
    summDelivery(cityId)     
    if($(".show_calendar.date").val() != "" && $(".delivery_time.city-lis")[0].style.display != "block") $(".delivery_time.city-lis")[0].style.display = "block"  
    else    $(".delivery_time.city-lis")[0].style.display = "none" 
    $(".delivery_time li").mouseenter(function() {
        this.style.background = "#f6f4ec";
    });
    $(".delivery_time li").mouseleave(function() {
        this.style.background = "none";
    });   
    $(".delivery_time.city-lis li").on('click',async function(e){ 
        
        $("input.delivery_time")[0].value = this.innerText
        //$(".delivery_time.city-lis")[0].style.display = "none"
        summDelivery(cityId) 
    }); 
});
 
 




$("input[name='otherPerson']").change(function(){    
    if(this.checked == false){
        $(".phone-mask")[0].closest("div").style.display = "none"
    } else{
        $(".phone-mask")[0].closest("div").style.display = "flex"
    }
});

function summDelivery(cityId){
    
    let cart = JSON.parse( localStorage.getItem("cart") == "undefined" ? "[]" : (localStorage.getItem("cart") || "[]")) 
    if(cityId == -1){        
        $(".delivery_price")[0].innerHTML = 0  + " ₪"
        if(cart[0].delivery_params){
            cart[0].delivery_params.deliverySumm = 0
            cart[0].delivery_params.cityId = -1
            localStorage.setItem("cart", JSON.stringify(cart));
            $("#summ-for-payment")[0].innerHTML = roundNumber(+$("#total-ammount")[0].innerHTML)
        } 
        return
    } 
    const deliveryParams = delivery.delivery[delivery.cityes_data[cityId]]  
    if(!deliveryParams){
        return
    }  
    let totalAmmount = +$("#total-ammount")[0].innerHTML                
    if(deliveryParams && deliveryParams.rate_delivery_to_summ_order.length){
        let isRate_delivery_to_summ_orderActivated = false
        for (let j = 0; j < deliveryParams.rate_delivery_to_summ_order.length; j++) {
            let min = +deliveryParams.rate_delivery_to_summ_order[j].sum_order.min
            let max = +deliveryParams.rate_delivery_to_summ_order[j].sum_order.max            
            if( totalAmmount > min && totalAmmount < max ) { 
                $(".delivery_price")[0].innerHTML = +deliveryParams.rate_delivery_to_summ_order[j].rate_delivery + " ₪"   
                isRate_delivery_to_summ_orderActivated = true             
            }
        }
        if(!isRate_delivery_to_summ_orderActivated) $(".delivery_price")[0].innerHTML = +deliveryParams.rate_delivery +  " ₪"
    } else{
        $(".delivery_price")[0].innerHTML = +deliveryParams.rate_delivery +  " ₪"
    }
    let koefTime = 1
    if($("input[name='time']")[0].value.indexOf("-") != -1 && $("input[name='time']")[0].value.indexOf(":") != -1)  koefTime = 1.3
    let deliveryPrice = $(".delivery_price")[0].innerHTML 
    if (deliveryPrice.length < 10) deliveryPrice = deliveryPrice.substring(0, $(".delivery_price")[0].innerHTML.length -2 )
    let deliverySumm =  +deliveryPrice * koefTime 
    if (koefTime == 1.3 ) $(".delivery_price")[0].classList.add("time")
    if( $("input[name='time']")[0].value) $(".delivery_price")[0].innerHTML = roundNumber( +deliverySumm)  + " ₪"               
    $("#summ-for-payment")[0].innerHTML = roundNumber(+$("#total-ammount")[0].innerHTML - +$(".discount")[0].innerHTML +deliverySumm)

    let delivery_params = {}
    delivery_params.koefTime = koefTime
    delivery_params.deliverySumm = deliverySumm
    delivery_params.deliveryPrice = deliveryPrice
    delivery_params.cityId = cityId
    cart[0].delivery_params =  delivery_params  
    localStorage.setItem("cart", JSON.stringify(cart));
   
}

function summDeliveryStep3(cityId){
    
    if(cityId == -1){
        $(".delivery_price")[0].innerHTML = 0  + " ₪"
        let totalAmmount = +$("#total-ammount")[0].innerHTML
        let summ_for_payment = roundNumber(+$("#total-ammount")[0].innerHTML - +$(".discount")[0].innerHTML)
        let pay_tips_value = +$('input[name="premium"]:checked').val() 
        summ_for_payment = roundNumber(summ_for_payment * (1 + pay_tips_value/100))
        $("#summ-for-payment")[0].innerHTML = summ_for_payment
        return
    } 
    const deliveryParams = delivery.delivery[delivery.cityes_data[cityId]]  
    if(!deliveryParams){
        $(".delivery_price")[0].innerHTML = 0 + " ₪"
        return
    } 

    let totalAmmount = +$("#total-ammount")[0].innerHTML                
    if(deliveryParams && deliveryParams.rate_delivery_to_summ_order.length){
        let isRate_delivery_to_summ_orderActivated = false
        for (let j = 0; j < deliveryParams.rate_delivery_to_summ_order.length; j++) {
            let min = +deliveryParams.rate_delivery_to_summ_order[j].sum_order.min
            let max = +deliveryParams.rate_delivery_to_summ_order[j].sum_order.max            
            if( totalAmmount > min && totalAmmount < max ) { 
                $(".delivery_price")[0].innerHTML = +deliveryParams.rate_delivery_to_summ_order[j].rate_delivery + " ₪"   
                isRate_delivery_to_summ_orderActivated = true             
            }
        }
        if(!isRate_delivery_to_summ_orderActivated) $(".delivery_price")[0].innerHTML = +deliveryParams.rate_delivery +  " ₪"
    } else{
        $(".delivery_price")[0].innerHTML = +deliveryParams.rate_delivery +  " ₪"
    }
    let cart = JSON.parse( localStorage.getItem("cart") == "undefined" ? "[]" : (localStorage.getItem("cart") || "[]"))[0] 

    let koefTime =  cart.delivery_params.koefTime
    let deliveryPrice =cart.delivery_params.deliveryPrice
    let deliverySumm = cart.delivery_params.deliverySumm 
    if (koefTime == 1.3 ) $(".delivery_price")[0].classList.add("time")
    $(".delivery_price")[0].innerHTML = deliverySumm + " ₪"
    let summ_for_payment = roundNumber(+$("#total-ammount")[0].innerHTML - +$(".discount")[0].innerHTML +deliverySumm)
    let pay_tips_value = +$('input[name="premium"]:checked').val() 
    summ_for_payment = roundNumber(summ_for_payment * (1 + pay_tips_value/100))
    $("#summ-for-payment")[0].innerHTML = summ_for_payment
}
//#endregion

//#region   Просчет Чаевых
    $('input[name="premium"]').change(function(e){         
        let cart = JSON.parse( localStorage.getItem("cart") == "undefined" ? "[]" : (localStorage.getItem("cart") || "[]"))[0] 
        if(cart[0] && cart[0].delivery_params && cart[0].delivery_params.cityId){
            summDeliveryStep3(cart[0].delivery_params.cityId)
        } 
        else(summDeliveryStep3(-1))
    });

//#endregion


function clearSelected(action){
    // перевод страницы в начально состояние 
    
    if(action == "add"){
        $(".option_value").each(function() { if(this.classList.contains("active")) this.classList.remove("active")});
        if($(".product-size.open-size-table span").length != 0) $(".product-size.open-size-table span")[0].innerHTML = "Выберите Размер "
    }  
    $(".body-product-info-input-text.option_input_text").each(function() { this.value = "" });
    $(".product-info-checkbox").each(function() { this.checked = false });
    $(".trans-btn").each(function() { this.innerHTML = "Добавить" });   
    $(".body-product-info-add .option_value").each(function() { if(this.classList.contains("active")) this.classList.remove("active")});    
    $(".body-product-info-add").each(function() { if(this.classList.contains("active")) this.classList.remove("active")});
    $(".product-info__add .option_value").each(function() { if(this.classList.contains("active")) this.classList.remove("active")});
    $("input[name='Box']").each(function() {this.checked = false });
}

$("input[name='methodPay']").change(function(e){ 
    if( this.value == 1 || this.value == 3 )  $(".main-btn.go-pay")[0].innerHTML = $(".main-btn.go-pay")[0].dataset.text_pay
    else if (this.value == 2 ||this.value == 4)  $(".main-btn.go-pay")[0].innerHTML = $(".main-btn.go-pay")[0].dataset.text_checkout
});


$(".main .main__wrap ul").click(function(e) {
   return
});
