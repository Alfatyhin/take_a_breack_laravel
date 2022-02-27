

jQuery(document).ready(function ($) {


    let catId = $('select.categories_list').val();
    SetProducts(catId);

    $('select.categories_list').on('change', function () {
        let catId = $('select.categories_list').val();
        SetProducts(catId);
    });

    function SetProducts(catId) {
        $('.prodicts_list').html('');
        var option = '';
        for (key in products[catId]) {
            var product = products[catId][key];
            if (!! product['nameTranslated']['ru'] ) {
                var name = product['nameTranslated']['ru'];
            } else {
                var name = product['name'];
            }
            option = `${option} <option value="${product['id']}"> ${name}</option>`;

        }
        var box = `<select name="dey_offer_id">${option}</select>`;
        $('.prodicts_list').html(box);
        return true;
    }

    $('.order_calc_type_name').each(function () {
        var val = $(this).val();
        var key = $(this).attr('data_key');

        $(`div.calc_type.key_${key} .${val}`).show();
    });

    $('.order_calc_type_name').on('change', function () {
        var val = $(this).val();
        var key = $(this).attr('data_key');
        $(`div.calc_type.key_${key} div`).hide();
        $(`div.calc_type.key_${key} .${val}`).show();
    });


    $('select.categories_list_2').on('change', function () {
        let catId = $(this).val();
        var key = $(this).attr('data_key');
        SetProducts2(catId, key);
    });

    function SetProducts2(catId, key) {
        console.log(`.products_list_2.key_${key}`);
        $(`.products_list_2.key_${key}`).html('');
        var option = '';
        for (k in products[catId]) {
            var product = products[catId][k];
            if (!! product['nameTranslated']['ru'] ) {
                var name = product['nameTranslated']['ru'];
            } else {
                var name = product['name'];
            }
            option = `${option} <option value="${product['id']}"> ${name}</option>`;

        }
        var box = `<select name="order_calc[${key}][product_id]">${option}</select>`;
        $(`.products_list_2.key_${key}`).html(box);
        return true;
    }

    $('.pop-ap .close').click(function () {
        $('.pop-ap').hide();
    });

    $('.product_key').each(function () {
        var id = $(this).attr('data_id');
        var key = $(this).attr('data_key');
        var name = all_products[id]['name'];
        if (all_products[id]['nameTranslated']['ru']) {
            name = all_products[id]['nameTranslated']['ru'];
        }
        name = `<input hidden name="order_calc[${key}][product_id]" value="${id}">${name}`
        $(this).html(name);
    });


    $('table.delivery input.city_input').on('input', function () {
        var val = $(this).val();
        var data_x = $(this).attr('data_x');
        $('table.delivery .city_search_out').html('');
        if (val.length >= 1) {
            // console.log(cityes);

            for (k in cityes) {

                var city_name_ru = cityes[k]['ru'];
                var city_name_en = cityes[k]['en'];
                var city_name_he = cityes[k]['he'];
                var search = new RegExp(`^${val}`, 'i');

                if (city_name_ru.match(search)) {
                    var city_name = city_name_ru;
                } else if (city_name_he.match(search)) {
                    var city_name = city_name_he;
                } else if (city_name_en && city_name_en.match(search)) {
                    var city_name = city_name_en;
                } else {
                    var city_name = false;
                }

                console.log(city_name);

                if (city_name) {
                    var input_test = $(`table.delivery .city_select_out.out_${data_x} input.city_${k}`).val();

                    console.log('input test ' + input_test);
                    if (!input_test) {
                        $(`table.delivery .city_search_out.out_${data_x}`).
                        append(`<span class="add_city"
                        data_key="${k}" data_x="${data_x}"
                        data_name="${city_name}">${city_name}</span> <br>`);
                    } else {
                        $(`table.delivery .city_search_out.out_${data_x}`).
                        append(`<span class="city_isset"
                        data_key="${k}" data_x="${data_x}"
                        data_name="${city_name}">${city_name}</span> <br>`);
                    }
                }

            }


            $('table.delivery .city_search_out .add_city').click(function () {
                var key = $(this).attr('data_key');
                var name = $(this).attr('data_name');
                var data_x = $(this).attr('data_x');

                $(`table.delivery .city_select_out.out_${data_x}`).
                append(`<nobr><input class="city_${key}" type="checkbox" name="city[${data_x}][${key}]"
                value="${key}" checked> ${name} </nobr>`);

                $('table.delivery .city_search_out').html('');
                $('table.delivery input.city_input').val('');
            });
        }
    });

    // текущая дата
    var date=new Date();
    // текущий год
    var year=date.getFullYear();
    // текущий месяц
    var mon=date.getMonth();

    setCalendar(year, mon);

    function setCalendar(year, month) {
        $('.calendar_box').html(getCalendar(year, month));

        $('.unset_dates input').each(function () {
            var date = $(this).attr('data_date');
            $(`table.calendar td[data_date_string='${date}']`).toggleClass('selected');
        });

        $('table.calendar td').each(function () {
            var td_date = $(this).attr('data_date');
            var date_arr = td_date.split('-');
            var ndate = new Date(date_arr[0], date_arr[1], date_arr[2], 20, 0);

            if (ndate < date) {
                $(this). addClass('close_date');
            } else {
                $(this).addClass('open_date');
            }

        });


        $('table.calendar .data_change').click(function () {
            var year = $(this).attr('data_year');
            var month = $(this).attr('data_month');
            setCalendar(year, month);
        });

        $('table.calendar td.open_date').click(function () {
            var date_unset = $(this).attr('data_date_string');

            if ($(this).hasClass('selected')) {

                $(`.unset_dates .${date_unset}`).remove();
            } else {
                var date_input = `<p class="${date_unset} hidden"><input type="text" name="shop[delivery_date_time][unset_date][${date_unset}]"
                                  value="true" data_date="${date_unset}"> ${date_unset} </p>`;
                $('.unset_dates').append(date_input);
            }

            $(this).toggleClass('selected');
        });
    }



    function getCalendar(year, mon) {
        var jmon= ['январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь'];
        var jdn= ['вс', 'пн', 'вт', 'ср', 'чт', 'пт', 'сб'];

        // текущая дата
        var adate=new Date();
        // текущий год
        var ayear=adate.getFullYear();
        // текущий месяц
        var amon=adate.getMonth();
        //текущий день
        var adey=adate.getDate();

        //следующий год и месяц
        var nextd = new Date(year, mon, 31);
        nextd.setDate(nextd.getDate() + 1);
        var nexty=nextd.getFullYear();
        var nextm=nextd.getMonth();


        //предыдущий год и месяц
        var ford = new Date(year, mon, 1);
        ford.setDate(ford.getDate() - 1);
        var fory=ford.getFullYear();
        var form=ford.getMonth();


        // получаем день недели начала месяца
        var ndate = new Date(year, mon, 1);

        // текущий год календаря
        var tyear=ndate.getFullYear();
        // текущий месяц календаря
        var tmon=ndate.getMonth();


        // получаем номер дня недели
        var xdey=ndate.getDay();


        //получаем новую дату начала отсчета цикла календаря
        ndate.setDate(ndate.getDate() - xdey);
        // начальное значение месяца календаря
        var smon=ndate.getMonth();
        // начальный год календаря
        var syear=ndate.getFullYear();



        var txt=`<tr>`;

        for (x=0; x<=6; x++)
        {
            var dn=jdn[x];
            txt=`${txt} <th>${dn}</th>`;
        }
        txt=`${txt} </tr>`;

        var i=0;
        while (i<=6)
        {
            x=0;
            txt=`${txt} <tr>`;
            while (x<=6)
            {
                var nmon=ndate.getMonth();
                var ndm=ndate.getDate();
                if (nmon == tmon) {
                    cl='activmon';
                }
                else {
                    cl='unactivmon';
                }

                if (tmon == amon && ndm == adey) {
                    cl = cl + ' today_date';
                }

                txt=`${txt} <td class='${cl} date_${tyear}-${nmon}-${ndm}'
                data_week_day="${x}"
                data_date_string="${tyear}-${nmon+1}-${ndm}"
                data_date="${tyear}-${nmon}-${ndm}" >
                    <div>
                        ${ndm}
                    </div>
                </td>`;

                ndate.setDate(ndate.getDate() +1);
                x++;
            }
            if (nmon != tmon && nmon != smon)
                i=6;
            txt=`${txt} </tr>`;
            i++;
        }

        var calendar = `<table class="calendar">
        <tr>
            <th colspan="7" class="header">
                <span class="data_change" data_year="${fory}" data_month="${form}"><</span>
                <span>${year} - ${jmon[tmon]}</span>
                <span class="data_change" data_year="${nexty}" data_month="${nextm}">></span>
            </th>
        </tr>
        ${txt}
        </table>`;

        return calendar;
    }

    $('input.en_city').on('input', function () {
        var val = $(this).val();
        var data_x = $(this).attr('data_x');
        $(`div.search_en_city_${data_x}`).html('');
        if (val.length >= 1) {

            for (k in cityes_en) {
                var city_name = cityes_en[k]['name'];
                var search = new RegExp(`^${val}`, 'i');

                if (city_name.match(search)) {
                    $(`div.search_en_city_${data_x}`).append(`<span class="city_isset" data_x="${data_x}"
                        data_name="${city_name}">${city_name}</span> <br>`);
                }
            }
        }

        $('div.search_en_city span.city_isset').click(function () {
            var data_x = $(this).attr('data_x');
            var city_name = $(this).attr('data_name');

            $(`td.en_city_${data_x} input`).val(city_name);
            $(`div.search_en_city_${data_x}`).html('');
        });
    });

});
