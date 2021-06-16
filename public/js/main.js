
jQuery(document).ready(function ($) {

   console.log('start front');

   $('.show-hide').on('click', function () {
       $(this).siblings('.hide').toggle();
   });

});
