$(function(){
    $("#anchor-select").get(0).scrollIntoView();

    $('.openingShortBlock').click(function () {
        var parent = $(this).parent();

        $(parent).toggleClass('open');
        $(parent).toggleClass('close');
    })
});
