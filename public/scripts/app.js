document.addEventListener('DOMContentLoaded', ()=>{

    

    // Открытие меню - начало

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

    // Открытие меню - конец


    // слайдер продукта

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

    
    
    
    // Выбор размера продукта

    let openTableBtn = document.querySelector('.open-size-table'); 
    let tableSize = document.querySelector('.product-size__table');
    let tableSizeVar = document.querySelectorAll(".product-size-var");

    if (tableSizeVar.length != null) {
        tableSizeVar.forEach(function(item){
            item.addEventListener('click', ()=>{

                openTableBtn.querySelector('span').innerHTML =  item.getAttribute('data-infosize');
                $(".current-price")[0].innerHTML =  item.querySelector(".price").innerText
                $(".main-btn.go-to-cart")[0].disabled = false;
                $(".product-info-checkbox")[0].disabled = false;
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

    //  Добавление в корзину
    $(".main-btn.go-to-cart").on('click', function(event){

        let cart = JSON.parse(localStorage.getItem("cart") || "[]");       
        let addedPosition = { id: product.id,
                             size: "size",
                             text: $(".body-product-info-input-text")[0].value,
                             count: $(".product-info-count-input")[0].value                            
                            }
        cart.push(addedPosition)
        localStorage.setItem("cart", JSON.stringify(cart));
    });
    // Checkbox для текста к торту
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
    // Кнопка  добавить текст к торту
    $(".trans-btn").on('click', function(event){
        if( $(".trans-btn")[0].innerHTML == "Добавить"){
            if( $(".body-product-info-input-text")[0].value != "" ){
                $(".current-price")[0].innerHTML =  (+$(".current-price")[0].innerHTML + +$(".price-text")[0].innerHTML).toString()
                $(".trans-btn")[0].innerHTML = "Убрать текст"
            }
        } else if($(".trans-btn")[0].innerHTML == "Убрать текст"){
            $(".current-price")[0].innerHTML =  (+$(".current-price")[0].innerHTML - +$(".price-text")[0].innerHTML).toString()
            $(".trans-btn")[0].innerHTML = "Добавить"
            $(".body-product-info-input-text")[0].value = ""
        }
    });

    // маска вводы номера телефона
    
    var selector = document.getElementsByClassName("phone-mask-input");
    if (!selector.length == 0) {
        var im = new Inputmask("99-99-999 (999)");
        im.mask(selector);
    }


    // логика чаевые 

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

    // слайдер преимуществ продукта

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

    // табы продукта

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


    // количество продукта

    let productCount = document.querySelectorAll('.product-info__count');

    if (productCount.length != 0) {
        productCount.forEach(function(item){
            item.addEventListener('click', function(e){
                let productInfoNum = e.target.closest('.product-info__count').querySelector('.product-info-count-input');
                console.log(productInfoNum);
                if (e.target.classList.contains('product-info-decrement')) {
                    let num = +productInfoNum.value - 1 ;
                    if (num<1) {
                        num = 1;
                    } 
                    productInfoNum.value = num;
                } else if (e.target.classList.contains('product-info-increment')) {
                    let num = +productInfoNum.value + 1 ;
                    productInfoNum.value = num;
                }
            })
        })
    }

    $("input[name=product-count]").keyup(function(){
        if($("input[name=product-count]")[0].value > 999){
            $("input[name=product-count]")[0].value = $("input[name=product-count]")[0].value.toString().substring(0,3)
        }
    });
    $("input[name=product-count]").change(function(){        
        if($("input[name=product-count]")[0].value < 1) $("input[name=product-count]")[0].value = 1        
    });
    $("input[name=product-count]").keypress(function(e){
        if (e.keyCode === 13) $("input[name=product-count]").blur()  
    });

   

   
    // открытие текст на странице о категории

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

    // страница faq

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

    // удаление товара из корзины 

    let deleteBtn = document.querySelectorAll('.delete-item');

    if (deleteBtn.length != 0) {
        deleteBtn.forEach(function(item){
            item.addEventListener('click', (e)=>{
                let needElem = e.target.closest('.pay-cart__item');
                needElem.remove();

                
            })
        })
    }

    // Гармошка корзины товара

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
    
})



