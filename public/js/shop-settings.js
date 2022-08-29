

jQuery(document).ready(function ($) {
    // $('.pop-ap .close').click(function () {
    //     $('.pop-ap').hide();
    // });

    $('.close').click(function () {
        var box = $(this).parent().parent();
        $(box).toggleClass('hidden');
    });



    $('.box_list  ul.list li:first-child').each(function () {
        ContentBoxMenuActivate(this);
    });


    $('.page_box ul.list li').click(function () {
        ContentBoxMenuActivate(this);
    });


    $('.content_list_menu span.box_data').click(function () {
        ContentBoxMenuActivate(this);
    });



    $('form.save_sortable').on('submit', function (e) {

        var box_name = $(this).attr('data_name');
        var form_append = $(this).find('.form_append');
        $(form_append).html('');

        var x = 0;
        $('ul.sortable.' + box_name + ' li').each(function () {
            var id = $(this).attr('data_id');
            var el = `<input type="hidden" name="sort[${id}]" value="${x}" />`;
            $(form_append).append(el);
            x++;
        });

    });


    $('form.ajax .submit[name="enabled"]').change(function () {
        if (this.checked) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
        var form = $(this).parent('form');
        $(form).parent().toggleClass('status_0');
        var data = GetDataForm(form);
        var url = $(form).attr('action');
        var method = $(form).attr('method');

        $.ajax({
            'url': url,
            'type': method,
            'data': data,
            'success': function (result) {
                console.log(result);
            }
        });

    });



    // добавление бокового меню (продукты вкладка параметры)
    $('.options_header span.add_option').click(function () {
        $(this).parent().find('.new_option').toggleClass('hidden');
    });
    $('.option_choice_add').click(function () {
        var data_key = $(this).attr('data_key') / 1;
        var tr = $(this).parents('tr').html();
        var table = $(this).parents('table');
        $(table).append('<tr>' + tr + '</tr>');
    });
    ///////////////////////////

    function GetDataForm(form) {
        var data = {};
        $(form).find ('input, textearea, select').each(function() {
            data[this.name] = $(this).val();
        });
        return data;
    }

    function ContentBoxMenuActivate(el) {
        let name = $(el).attr('data_name');
        let parent = $(el).parent();
        let old_el = $(parent).find('.active');
        let old_name = $(old_el).attr('data_name');
        $(old_el).removeClass('active');
        $(el).addClass('active')
        let parent_box = $(el).parent().parent().parent();
        let content_box = $(parent_box).children('.content_list').first();
        $(content_box).find(`.content_item.${old_name}`).first().removeClass('active');
        $(content_box).find(`.content_item.${name}`).first().addClass('active');
    }

    function ContentBoxMenuActivate2(el) {
        let name = $(el).attr('data_name');
        console.log(name);
        let parent = $(el).parent();
        let old_el = $(parent).find('.active');
        let old_name = $(old_el).attr('data_name');
        console.log(old_name);
        $(old_el).removeClass('active');
        $(el).addClass('active')
        let parent_box = $(el).parent().parent().parent();;
        $(parent_box).addClass('test');
        let content_box = $(parent_box).children('.content_list').first();
        $(content_box).addClass('test2');
        $(content_box).find(`.content_item.${old_name}`).first().removeClass('active');
        $(content_box).find(`.content_item.${name}`).first().addClass('active');
    }

    $( ".sortable" ).sortable({
        revert: true
    });
    $( "ul.list, ul.list li" ).disableSelection();


    $('.image .fa-plus-square').click(function () {
        var parent = $(this).parents('.image');
        var form = $(parent).find('form');
        $(form).show();
    });

    $('tr .fa-trash').click(function () {
       $(this).parents('tr').first().remove();
    });

});
