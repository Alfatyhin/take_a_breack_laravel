
document.addEventListener('DOMContentLoaded', ()=> {

   // чаевые
   //  $('.pay-tips label').click(function () {
   //      var tips = $("input[name='premium']:checked").val();
   //      var order_summ = $('.order_price').text() / 1;
   //
   //      var total = order_summ + order_summ * (tips / 100);
   //      $('.total_order_price').text(total.toFixed(1) + '0');
   //  });

    $(".popup_site_message .go_prod").click(function() {
        $(".popup_site_message").fadeOut(500);
    });
    $(".popup_site_message .popup__content .close span").click(function() {
        $(".popup_site_message").fadeOut(500);
    });
});