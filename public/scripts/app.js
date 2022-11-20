document.addEventListener('DOMContentLoaded', ()=>{

    (function cartInit(){
       
        
        // localStorage.removeItem("cart"); 

        
        let cart = JSON.parse(localStorage.getItem("cart") || "[]");
        if(!cart.length)  return
        if($("#cart" != null)) cartInitProducts(cart);
        if($("#cart" != null)) summCalculation(cart);

        
    })();
   
    

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
    
    //#region Выбор размера продукта

    let openTableBtn = document.querySelector('.open-size-table'); 
    let tableSize = document.querySelector('.product-size__table');
    let tableSizeVar = document.querySelectorAll(".product-size-var");

    if (tableSizeVar.length != null) {
        tableSizeVar.forEach(function(item){
            item.addEventListener('click', ()=>{
                currentItem = item 
                openTableBtn.querySelector('span').innerHTML =  item.getAttribute('data-infosize');                
                selectedItemPrice = +item.querySelector(".price").innerText
                selectedItemWeightParams = item.querySelector(".weight-params").innerText
                $(".current-price")[0].innerHTML =  currentSumm(selectedItemPrice, $("#count-product")[0].value)
                $(".main-btn.go-to-cart")[0].disabled = false;
                $(".product-info-checkbox")[0].disabled = false;
                $(".product-info-decrement")[0].disabled = false;
                $(".product-info-count-input")[0].disabled = false;
                $(".product-info-increment")[0].disabled = false;
                $(".main-btn.go-to-cart")[0].style.opacity = "1.0";
                CloseTable();
            })
        })
    }
    if (openTableBtn) {
        openTableBtn.addEventListener('click', ()=>{
            if(openTableBtn.classList.contains('active')) {
                CloseTable();
            } else {
                OpenTable();
            }
        })
    }
    function CloseTable() {
        tableSize.classList.remove('active');
        openTableBtn.classList.remove('active');

    }
    function OpenTable() {
        tableSize.classList.add('active');
        openTableBtn.classList.add('active');
        document.addEventListener('click', (e)=> {
            let target = e.target;

            if (!target.closest('.open-size-table')) {
                CloseTable();
            }
        })
    } 
    //#endregion

    // #region Инициализация бейджика количества корзины в карточке товара
    let cart = JSON.parse(localStorage.getItem("cart") || "[]")    
    if(cart.length != 0) $(".badge")[0].style.opacity = "1"
    $("#cart-count")[0].innerText = cart.length;
    //#endregion

    //#region  Добавление в корзину
    $(".main-btn.go-to-cart").on('click', function(event){
        
        let cart = JSON.parse(localStorage.getItem("cart") || "[]");  
        let addedPosition = { 
                                id:  15/*product.id*/,
                                // size: "size",
                                // text: $(".body-product-info-input-text")[0].value,
                                // count: $(".product-info-count-input")[0].value   
                                name: $(".product-info__title h1")[0].innerText,
                                weightParams: selectedItemWeightParams,
                                price: selectedItemPrice,
                                itemSumm: currentSumm(selectedItemPrice, $("#count-product")[0].value),
                                count: $(".product-info-count-input")[0].value,
                                variant: "variant",
                                size: "size",
                                text: $(".body-product-info-input-text")[0].value,
                                imagePath: "assets/images/slider-img.png",                         
                            }
        cart.push(addedPosition)        
        $("#cart-count")[0].innerText = cart.length
        $(".badge")[0].style.opacity = "1"
        localStorage.setItem("cart", JSON.stringify(cart));
    });
    //#endregion

    //#region Кнопка  добавить к заказу 
    $(".trans-btn_additive").on('click', function(event){



        let cart = JSON.parse(localStorage.getItem("cart") || "[]");  
        debugger
        let addedPosition = { 
                                id:  16/*product.id*/,
                                name: $(".product-info__title h1")[0].innerText,
                                weightParams: selectedItemWeightParams,
                                price: selectedItemPrice,
                                itemSumm: currentSumm(selectedItemPrice, $("#count-product")[0].value),
                                count: $(".product-info-count-input")[0].value,
                                variant: "variant",
                                size: "size",
                                text: $(".body-product-info-input-text")[0].value,
                                imagePath: "assets/images/slider-img.png",                         
                            }
        cart.push(addedPosition)        
        $("#cart-count")[0].innerText = cart.length
        $(".badge")[0].style.opacity = "1"
        localStorage.setItem("cart", JSON.stringify(cart));
    });
    //#endregion

    //#region Кнопка  добавить текст к торту
    $(".trans-btn").on('click', function(event){

        if( $(".trans-btn")[0].innerHTML == "Добавить"){
            if( $(".body-product-info-input-text")[0].value != "" ){
                $(".trans-btn")[0].innerHTML = "Убрать текст"
                $(".current-price")[0].innerHTML = currentSumm(selectedItemPrice, $("#count-product")[0].value)
            }
        } else if($(".trans-btn")[0].innerHTML == "Убрать текст"){
            $(".trans-btn")[0].innerHTML = "Добавить"
            $(".body-product-info-input-text")[0].value = ""
            $(".current-price")[0].innerHTML =   currentSumm(selectedItemPrice, $("#count-product")[0].value)            
        }
    });
    //#endregion

    

    //#region  Удаление из корзины
    let productCount = document.querySelectorAll('.delete-item');
    if (productCount.length != 0) {            
        productCount.forEach(function(item){
            item.addEventListener('click', function(e){
                if(window.location.href.indexOf("pay1") != -1) {

                    
                    let cart = JSON.parse(localStorage.getItem("cart") || "[]");
                    let cartId = e.target.parentNode.dataset.id                                                
                    
                    const newCart = cart.filter((n, idx, cartId) => n.id !== cartId[0].id )
                    localStorage.removeItem("cart");   
                    localStorage.setItem("cart", JSON.stringify(newCart)); 
                    cartInitProducts(newCart)
                }                 
            })
        })
    }

    //#endregion
    
    //#region Checkbox для текста к торту
    $(".product-info-checkbox").change(function(){
       
        if($(".product-info-checkbox")[0].checked == true){
            $(".body-product-info-add")[0].classList.add("active")
            $(".product-info__add")[0].style.marginBottom = "30px"
        } 
        else{
            $(".body-product-info-add")[0].classList.remove("active")
            $(".product-info__add")[0].style.marginBottom = "107px"
        } 
        

    });
    //#endregion
    

    

    //#region маска вводы номера телефона
    
    var selector = document.getElementsByClassName("phone-mask-input");
    if (!selector.length == 0) {
        var im = new Inputmask("(999) 99-99-999");
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
        $(".current-price")[0].innerHTML =   currentSumm(selectedItemPrice, $("#count-product")[0].value)
    });
    $("#count-product").change(function(){  
        if($("#count-product")[0].value < 1) $("#count-product")[0].value = 1 
        
        $(".current-price")[0].innerHTML =   currentSumm(selectedItemPrice, $("#count-product")[0].value)       
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
    //     let cart = JSON.parse(localStorage.getItem("cart") || "[]");
    //     let cartId = e.currentTarget.dataset.id 
    //     for (let i = 0; i < cart.length; i++) {
    //         if(cart[i].id == cartId ) {
    //             cart[i].itemSumm = (+cart[i].itemSumm/cart[i].count*e.currentTarget.value).toString()
    //             cart[i].count = e.currentTarget.value
    //         }
    //     }       
    //     debugger
    //     localStorage.removeItem("cart");   
    //     localStorage.setItem("cart", JSON.stringify(cart)); 
    //     cartInitProducts(cart)
    // });
    // $(".cart-product-info-count-input").change(function(e){ 
    //     if($(".cart-product-info-count-input")[0].value < 1) $(".cart-product-info-count-input")[0].value = 1 
    //     let cart = JSON.parse(localStorage.getItem("cart") || "[]");
    //     let cartId = e.currentTarget.dataset.id
    //     for (let i = 0; i < cart.length; i++) {
    //         if(cart[i].id == cartId ) {
    //             cart[i].itemSumm = (+cart[i].itemSumm/cart[i].count*e.currentTarget.value).toString()
    //             cart[i].count = e.currentTarget.value
    //         }
    //     }       
    //     debugger
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

    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        let totalCartBtn = document.querySelector('.pay-cart__total-sum'),
        cartBox = document.querySelector('.pay-cart__box');
        if (totalCartBtn) {
            totalCartBtn.addEventListener('click', (e)=>{
            if (e.target.classList.contains('active')) {
                e.target.classList.remove('active');
                cartBox.style.maxHeight =  0 + 'px';
            } else {
                e.target.classList.add('active');
                cartBox.style.maxHeight = cartBox.scrollHeight + 'px';
            }
            })
        }
    }
    //#endregion

    
})
let selectedItemPrice;
let selectedItemWeightParams;
function currentSumm(selectedItemPrice, count){
    let currentTextPrice = $(".body-product-info-input-text")[0].value != "" && $(".trans-btn")[0].innerHTML == "Убрать текст" ?  $(".price-text")[0].innerText : "0"
    let currentPrice = +selectedItemPrice
    let currentSumm = (+currentTextPrice + currentPrice)*count
    return currentSumm
}
function currentCountCart( cart ){
    if(cart.length == 0) return 0
    let count = 0
    for (let i = 0; i < cart.length; i++) {
        count = +cart[i].count        
    }
    return count

}

function increment(e){
    if(window.location.href.indexOf("product.html") != -1) {  
        let count = +e.previousElementSibling.value + 1
        let summ = currentSumm(selectedItemPrice,count)
        $(".current-price")[0].innerHTML =   summ
        e.previousElementSibling.value = count
    } else {
        if(e.closest('.product-info__count')){
            let count = +e.previousElementSibling.value + 1
            e.previousElementSibling.value = count
            let cart = JSON.parse(localStorage.getItem("cart") || "[]");
            let cartId = e.parentNode.dataset.id 
            for (let i = 0; i < cart.length; i++) {
                if(cart[i].id == cartId ) {
                    cart[i].itemSumm = (+cart[i].itemSumm/cart[i].count*count).toString()
                    cart[i].count = count
                }
            }       
            localStorage.removeItem("cart");   
            localStorage.setItem("cart", JSON.stringify(cart)); 
            cartInitProducts(cart)

        } else if(e.closest('.product-info__count_additive')){
            let oldCount = +e.previousElementSibling.value
            let count = +e.previousElementSibling.value + 1
            $("#itemSumm_additive")[0].innerHTML =  $("#itemSumm_additive")[0].innerHTML/oldCount*count
            e.previousElementSibling.value = count
        }
    }
}
function decrement(e){
    
    if(window.location.href.indexOf("product.html") != -1) {
        let count = +e.nextElementSibling.value - 1
        count = count < 1 ? 1 : count
        let summ = currentSumm(selectedItemPrice,count)
        $(".current-price")[0].innerHTML =   summ
        e.nextElementSibling.value = count
    } else {
        if(e.closest('.product-info__count')){
            let count = +e.nextElementSibling.value - 1
            count = count < 1 ? 1 : count
            e.nextElementSibling.value = count
            let cart = JSON.parse(localStorage.getItem("cart") || "[]");
            let cartId = e.parentNode.dataset.id 
            for (let i = 0; i < cart.length; i++) {
                if(cart[i].id == cartId ) {
                    cart[i].itemSumm = (+cart[i].itemSumm/cart[i].count*count).toString()
                    cart[i].count = count
                }
            }       
            localStorage.removeItem("cart");   
            localStorage.setItem("cart", JSON.stringify(cart)); 
            cartInitProducts(cart)
        } else if(e.closest('.product-info__count_additive')){
            let oldCount = +e.nextElementSibling.value
            let count = +e.nextElementSibling.value - 1
            count = count < 1 ? 1 : count
            $("#itemSumm_additive")[0].innerHTML =  $("#itemSumm_additive")[0].innerHTML/oldCount*count
            e.nextElementSibling.value = count
        }
    }
}

// function increment_decrement(){
//     let productCount = document.querySelectorAll('.product-info__count');
//     if (productCount.length != 0) {            
//         productCount.forEach(function(item){
//             item.addEventListener('click', function(e){
//                 if(window.location.href.indexOf("pay1") != -1) {
//                     let productInfoNum = e.target.closest('.product-info__count').querySelector('.product-info-count-input');
//                     let num;
//                     let cart = JSON.parse(localStorage.getItem("cart") || "[]");
//                     let cartId = e.target.parentNode.dataset.id                                                
//                     if (e.target.classList.contains('product-info-decrement')) {
//                         num = +productInfoNum.value - 1 ;
//                         if (num<1) {
//                             num = 1;
//                         } 
//                         productInfoNum.value = num;
//                     } else if (e.target.classList.contains('product-info-increment')) {
//                         num = +productInfoNum.value + 1 ;
//                         productInfoNum.value = num;
                        
//                     }
//                     for (let i = 0; i < cart.length; i++) {
//                         if(cart[i].id == cartId ) {
//                             cart[i].itemSumm = (+cart[i].itemSumm/cart[i].count*num).toString()
//                             cart[i].count = num
//                         }
//                     }       
//                     localStorage.removeItem("cart");   
//                     localStorage.setItem("cart", JSON.stringify(cart)); 
//                     cartInitProducts(cart)

//                 } else {
//                     let productInfoNum = e.target.closest('.product-info__count').querySelector('.product-info-count-input');
//                     console.log(productInfoNum);
//                     if (e.target.classList.contains('product-info-decrement')) {
//                         let num = +productInfoNum.value - 1 ;
//                         if (num<1) {
//                             num = 1;
//                         } 
//                         productInfoNum.value = num;
//                         $(".current-price")[0].innerHTML =   currentSumm(selectedItemPrice)
//                     } else if (e.target.classList.contains('product-info-increment')) {
//                         let num = +productInfoNum.value + 1 ;
//                         productInfoNum.value = num;
//                         $(".current-price")[0].innerHTML =   currentSumm(selectedItemPrice)
//                     }
//                 }
//             })
//         })
//     }
// }
    
// function increment_decrement_additional(){
//     let productCountAdditive = document.querySelectorAll('.product-info__count_additive');
//     debugger
//     if (productCountAdditive.length != 0) {
//         productCountAdditive.forEach(function(item){
//             item.addEventListener('click', function(e){
//                 debugger
//                 let count = $(".product-info-count-input_additive")[0].value
//                 if(window.location.href.indexOf("pay1") != -1) {
//                     let productInfoNum = e.target.closest('.product-info__count_additive').querySelector('.product-info-count-input_additive');
//                     let summAdditive = $("#itemSumm_additive")[0].innerHTML;
//                     let num;   
//                     if (e.target.classList.contains('product-info-decrement')) {
//                         // let count = +productInfoNum.value
//                         num = +productInfoNum.value - 1 ;
//                         if (num<1) {
//                             num = 1;
//                         } 
//                         productInfoNum.value = num;
//                     } else if (e.target.classList.contains('product-info-increment')) {
//                         num = +productInfoNum.value + 1 ;
//                         productInfoNum.value = num;                            
//                     }  
//                     $("#itemSumm_additive")[0].innerHTML =   (summAdditive/count*num).toString() 
//                 } else {
//                     let productInfoNum = e.target.closest('.product-info__count_additive').querySelector('.product-info-count-input_additive');
//                     console.log(productInfoNum);
//                     if (e.target.classList.contains('product-info-decrement')) {
//                         // let count = +productInfoNum.value
//                         let num = +productInfoNum.value - 1 ;
//                         if (num<1) {
//                             num = 1;
//                         } 
//                         productInfoNum.value = num;                           
//                     } else if (e.target.classList.contains('product-info-increment')) {
//                         let num = +productInfoNum.value + 1 ;
//                         productInfoNum.value = num;                            
//                     }
//                     $("#itemSumm_additive")[0].innerHTML =   (summAdditive/count*num).toString()
//                 }
//             })
//         })
//     }
        
// }
function cartInitProducts(cart){

    var el = $('.pay-cart__title');
    // debugger
    if(el) el.remove();
    el = $(".pay-cart__items")
    // debugger
    for (let i = 0; i < el.length; i++) {
        // debugger
        el[i].remove();            
    }
    let rootElement = $(".pay-cart__box");
    for (let i = 0; i < cart.length; i++) {
        let payCartItems = `
                                <div class="pay-cart__item">
                                    <img src="${cart[i].imagePath}" alt="">
                                    <div class="pay-cart__item-info">
                                        <p>
                                            ${cart[i].name}
                                        </p>
                                        <span>
                                            ${cart[i].weightParams}
                                        </span>
                                        <div class="product-info__count" data-id="${cart[i].id}">
                                            <button class="product-info-decrement" onclick="decrement(this)">-</button>
                                            <input class="cart-product-info-count-input" value="${cart[i].count}" data-id="${cart[i].id}" type="number" name="product-count">
                                            <button class="product-info-increment" onclick="increment(this)">+</button>
                                        </div>
                                        
                                    </div>
                                    <div class="pay-cart__item-price">
                                        <span>
                                            <span class="itemSumm">${cart[i].itemSumm}</span> ₪
                                        </span>
                                    </div>                                    
                                </div>
                                <button class="delete-item" data-id="${cart[i].id}">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M13.4099 12L17.7099 7.71C17.8982 7.5217 18.004 7.2663 18.004 7C18.004 6.7337 17.8982 6.47831 17.7099 6.29C17.5216 6.1017 17.2662 5.99591 16.9999 5.99591C16.7336 5.99591 16.4782 6.1017 16.2899 6.29L11.9999 10.59L7.70994 6.29C7.52164 6.1017 7.26624 5.99591 6.99994 5.99591C6.73364 5.99591 6.47824 6.1017 6.28994 6.29C6.10164 6.47831 5.99585 6.7337 5.99585 7C5.99585 7.2663 6.10164 7.5217 6.28994 7.71L10.5899 12L6.28994 16.29C6.19621 16.383 6.12182 16.4936 6.07105 16.6154C6.02028 16.7373 5.99414 16.868 5.99414 17C5.99414 17.132 6.02028 17.2627 6.07105 17.3846C6.12182 17.5064 6.19621 17.617 6.28994 17.71C6.3829 17.8037 6.4935 17.8781 6.61536 17.9289C6.73722 17.9797 6.86793 18.0058 6.99994 18.0058C7.13195 18.0058 7.26266 17.9797 7.38452 17.9289C7.50638 17.8781 7.61698 17.8037 7.70994 17.71L11.9999 13.41L16.2899 17.71C16.3829 17.8037 16.4935 17.8781 16.6154 17.9289C16.7372 17.9797 16.8679 18.0058 16.9999 18.0058C17.132 18.0058 17.2627 17.9797 17.3845 17.9289C17.5064 17.8781 17.617 17.8037 17.7099 17.71C17.8037 17.617 17.8781 17.5064 17.9288 17.3846C17.9796 17.2627 18.0057 17.132 18.0057 17C18.0057 16.868 17.9796 16.7373 17.9288 16.6154C17.8781 16.4936 17.8037 16.383 17.7099 16.29L13.4099 12Z" fill="#222222"></path>
                                    </svg>                                            
                                </button>
                            `
        let insertedElement = document.createElement('div');
        insertedElement.classList.add("pay-cart__items");
        insertedElement.innerHTML = payCartItems;            
        rootElement.prepend(insertedElement);    
    }
    rootElement.prepend(`
                            <div class="pay-cart__title">
                                <span>
                                    Ваш заказ
                                </span>
                            </div>
                        `);
}
function summCalculation(cart){
    if(!cart.length)  return
    let totalAmount = 0
    let summForPayment = 0
    for (let i = 0; i < cart.length; i++) {
        totalAmount += +cart[i].itemSumm                    
    }
    $("#total-ammount")[0].innerHTML = totalAmount
    summForPayment += totalAmount                       // далее добавить скидку и доставку
    $("#summ-for-payment")[0].innerHTML = summForPayment
    

}