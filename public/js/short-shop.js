
/* при загрузке страницы */
function loadPage() {

    //маска для номера телефона
    let maskOptions = {
        mask: '+972 000-000-0000',
        lazy: false
    }

    for (let i = 0; i <= fieldsPhone.length-1; i++) {
        new IMask(fieldsPhone[i], maskOptions);
    }
    //**************************
}
window.onload = function() {

    //маска для номера телефона
    let maskOptions = {
        mask: '+972 000-000-0000',
        lazy: false
    }

    for (let i = 0; i <= fieldsPhone.length-1; i++) {
        new IMask(fieldsPhone[i], maskOptions);
    }
    //**************************
    //маска даты
    let dateMaskOption = {
        mask: '00-00-0000',
        lazy: false
    }

    //**************************

    $('.preloader').hide();
    $(function() {


        $('input.dateMask').on('focus', function () {
            for (let i = 0; i <= fieldsDates.length-1; i++) {
                new IMask(fieldsDates[i], dateMaskOption);
            }
        });



        $('.price_change').on('input', function () {
            changeTotalPrice();
        });
        $('input[name="premium"]').on('change', function () {
            changeTotalPrice();
        });


        function changeTotalPrice() {
            let order_price = $('input.order_price').val() / 1;
            let tips = $('input[name="premium"]:checked').val() / 1;
            let tips_val = (order_price * tips / 100);
            let total_prise = order_price + tips_val;
            $('.order__premiumPrice').text(tips_val);
            $('.order__totalPrice').text(total_prise);
        }

    });

    $('form').submit(function (e) {
        $('.preloader').show();
    });

};

