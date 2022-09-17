document.addEventListener('DOMContentLoaded', ()=>{
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
    

    let openTableBtn = document.querySelector('.open-size-table'); 
    let tableSize = document.querySelector('.product-size__table');
    let tableSizeVar = document.querySelectorAll(".product-size-var");

    if (tableSizeVar.length != null) {
        tableSizeVar.forEach(function(item){
            item.addEventListener('click', ()=>{
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
    
    var selector = document.getElementsByClassName("phone-mask-input");

    var im = new Inputmask("99-99-999 (999)");
    im.mask(selector);
})