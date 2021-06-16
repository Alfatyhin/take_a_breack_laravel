
jQuery(document).ready(function ($) {

    var form_data = {
        'id': 82731109,
    };
    $.ajax({
        url: 'https://takeabreak.website/api/category',
        dataType: 'json',
        cache: false,
        contentType: false,
        data: form_data,
        type: 'get',
        success: function (zipData) {
            console.log(zipData);

        }
    });


});
