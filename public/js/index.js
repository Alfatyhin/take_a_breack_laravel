/* настройки слайдера "выбор товара" */
function selectSliderSettings() {
    $('.select__slider').slick({
        adaptiveHeight: true,
        dots: true,
        infinite: false,
        rows: 3,
        slidesPerRow: 4,
        appendArrows: $('.select__sliderPagination'),
        appendDots: $('.select__sliderPagination'),
        responsive: [
            {
                breakpoint: 1210,
                settings: {
                    slidesPerRow: 3,
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesPerRow: 2,
                    rows: 2,
                }
            },
        ]
    });
}

/********************************************/


/* настройки слайдера "отзывы" */

$('.feedback__slider').slick({
    dots: true,
    infinite: false,
    rows: 1,
    slidesPerRow: 3,
    appendArrows: $('.feedback__sliderPagination'),
    appendDots: $('.feedback__sliderPagination'),
    responsive: [

        {
            breakpoint: 481,
            settings: {
                slidesPerRow: 2,
            }
        },
    ]
});

/********************************************/

/*загрузка страницы*/
function loadPage() {

    /* очищаем текстовые поля */
    for (let i=0; i<textFields.length; i++) {
        textFields[i].value = "";
    }
    /**************************/

    /* формируем слайдер с товаром */
    // filterProduct(selectHeaderItems[0].getAttribute('data-type'));

    for (let i=0; i < selectHeaderItems.length; i++) {
        if (selectHeaderItems[i].classList.contains('active')) {
            filterProduct(selectHeaderItems[i].getAttribute('data-type'));
        }
    }

    selectSliderSettings();
    /**************************************************/

    /* переходим к товару, если выбрали товар в пункте меню (заголовок или подвал) */
    if (sessionStorage.getItem('productType') !== null) {
        clickOnProdType(sessionStorage.getItem('productType'));
        sessionStorage.removeItem('productType');
    }
    /****************************************/

    //маска для номера телефона
    let maskOptions = {
        mask: '+972 00-000-0000',
        lazy: false
    }

    for (let i = 0; i <= fieldsPhone.length-1; i++) {
        new IMask(fieldsPhone[i], maskOptions);
    }
    //**************************
    //маска для даты
    // let maskOptionsDate = {
    //     mask: Date,
    //     min: new Date(1900, 0, 1),
    //     max: new Date((new Date()).getFullYear()+1, 0, 1),
    //     lazy: false
    // }
    //
    // for (let i = 0; i <= fieldsDate.length-1; i++) {
    //     new IMask(fieldsDate[i], maskOptionsDate);
    // }
    //**************************
}
/************************/


//прокрутка страницы до нужного блока при выборе пункта меню
const anchors = document.querySelectorAll('a[href^="./#anchor"]')

for (let i = 0; i < anchors.length; i++) {
    let anchor = anchors[i];
    anchor.addEventListener('click', function (e) {
        e.preventDefault()

        let blockID = anchor.getAttribute('href').substr(3);
        document.getElementById(blockID).scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        })
    })
}
//**************************************************************************


/* выбираем тип товара */

let selectHeaderItems = document.querySelectorAll('.select__headerItem');
let sliderItems = document.querySelectorAll('.select__sliderItem');
let arRemoveSlides = [];

function filterProduct(typeProd) {

    /*восстанавливаем удаленные слайды*/
    for (let i=0; i<arRemoveSlides.length; i++) {
        $('.select__slider').prepend( arRemoveSlides[i]);
    }

    arRemoveSlides = [];
    /******************************/

    for (let i=0; i<sliderItems.length; i++) {
        if (sliderItems[i].hasAttribute('data-type')) {
            if (sliderItems[i].getAttribute('data-type').indexOf(typeProd) === -1) {
                arRemoveSlides.push(sliderItems[i]);
                sliderItems[i].remove();
            }
        } else {
            arRemoveSlides.push(sliderItems[i]);
            sliderItems[i].remove();
        }
    }
    /**************************************/
}

for (let i=0; i < selectHeaderItems.length; i++) {
    selectHeaderItems[i].addEventListener('click', function(e){

        let activeBtn = document.querySelector('.select__headerItem.active');
        activeBtn.classList.remove('active');

        e.target.classList.add('active');

        $('.select__slider').slick('unslick'); //сбрасываем настройки слайдера

        filterProduct(e.target.getAttribute('data-type'));

        selectSliderSettings(); //применяем настройки заново

    });
}

/*****************************************/


/* отправка почты */

let popupMessage = document.querySelector('.popupMessage');
let popupMessageCloseBtn = document.querySelector('.popupMessage__closeBtn');

if (popupMessageCloseBtn !== null) {
    popupMessageCloseBtn.addEventListener('click', function () {
        popupMessage.classList.remove('showBlock');
    })
}

function sendForm(e) {
    // let formData = new FormData();
    // formData.append('clientName', "значение из поля");
    // formData.append('phone', "значение из поля");
    // formData.append('message', "значение из поля");
    //
    // let request = new XMLHttpRequest();
    // request.open('POST', 'textMailMessage.php');
    //
    // request.send(formData);

    // request.addEventListener('readystatechange', function() {
    //   if (this.readyState === 4 && this.status === 200) {
    //     let data = this.responseText;
    //
    //     if (data === "ok") {
    popupMessage.classList.add('showBlock');
    //     } else {
    //       console.log('ошибка отправки письма' + data);
    //     }
    //
    //   }
    // });
}

function haveError() {

}

/****************************************/

