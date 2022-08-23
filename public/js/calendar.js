var translite = {
    'month_name': {
        'ru': ['январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь'],
        'en': ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        'he': ['ינואר', 'פברואר', 'מרץ', 'אפריל', 'מאי', 'יוני', 'יולי', 'אוגוסט', 'ספטמבר', 'אוקטובר', 'נובמבר', 'דצמבר']
    },
    'day_week_name': {
        'ru': ['вс', 'пн', 'вт', 'ср', 'чт', 'пт', 'сб'],
        'en': ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        'he': ['א', 'שני', 'ג', 'רביעי', 'ה', 'שי', 'שבת']
    }
};

var Cart = {};

$(function(){

    $('.show_calendar').click(function () {
        $('.calendar_table').removeClass('hidden');

        // текущая дата
        var date=new Date();
        // текущий год
        var year=date.getFullYear();
        // текущий месяц
        var mon=date.getMonth();

        setCalendar(year, mon);
    });



    $("input[name='delivery']").on('change', function () {
        // текущая дата
        var date=new Date();
        // текущий год
        var year=date.getFullYear();
        // текущий месяц
        var mon=date.getMonth();

        setCalendar(year, mon);
    });

    function setCalendar(year, month) {
        var calendar = getCalendar(year, month);
        $('.calendar_table').html(calendar);

        console.log('delivery_date_time');
        console.log(shop_setting['delivery_date_time']);

        var delivery_mode = $("input[name='delivery']:checked").val();
        var stock_mode = 'in_stock';

        /////////////////////////////////////////////////
        $('.listProduct__table tr').each(function () {
            var prod_id = $(this).attr('data-productid');
            if (prod_id) {
                var product = products_cart[prod_id];
                var count = $(`.productCount__countNumber[data-productid="${prod_id}"]`).text() / 1;

                if (product['stock_count'] > 0 && count <= product['stock_count'] && stock_mode == 'in_stock') {

                } else {
                    stock_mode = 'pre_order';
                }
            }

        });
        //////////////////////////////////////////////

        console.log('stock_mode');
        console.log(stock_mode);

        console.log('delivery_mode');
        console.log(delivery_mode);

        var add_day = shop_setting['delivery_date_time'][delivery_mode][stock_mode] / 1;


        // текущая дата
        var adate=new Date();
        adate.setDate(adate.getDate() + add_day);

        $('table.calendar td').each(function () {
            var td_date = $(this).attr('data_date');
            var week_day = $(this).attr('data_week_day');
            var date_str = $(this).attr('data_date_string');
            var date_arr = td_date.split('-');
            var ndate = new Date(date_arr[0], date_arr[1], date_arr[2], 20, 0);


            if (ndate < adate) {
                $(this).addClass('close_date');
            } else {
                if (!shop_setting['delivery_date_time']['weeks_day'][week_day] ) {
                    $(this).addClass('close_date');
                } else {
                    if (shop_setting['delivery_date_time']['unset_date']) {
                        if (shop_setting['delivery_date_time']['unset_date'][date_str] ) {
                            $(this).addClass('close_date');
                        } else {
                            $(this).addClass('open_date');

                        }
                    } else {
                        $(this).addClass('open_date');
                    }
                }
            }

        });

        var date_set = $('.shop_cart_box input.date').val();
        if (date_set) {
            $(`.shop_cart_box table.calendar td[data_date_string='${date_set}']`).addClass('select_date');
        }

        $('table.calendar .data_change').click(function () {
            var year = $(this).attr('data_year');
            var month = $(this).attr('data_month');
            setCalendar(year, month);
        });


        $('table.calendar td.open_date').click(function () {
            console.log('test');
            var date_delivery = $(this).attr('data_date_string');
            var week_day = $(this).attr('data_week_day');
            $('table.calendar td.select_date').toggleClass('select_date');
            $(this).toggleClass('select_date');
            $('input.date').val(date_delivery);
            $('.calendar_table').addClass('hidden');

            var times = shop_setting['delivery_date_time']['time_day'][week_day];
            $('select[name="time"] option').remove();
            for (k in times){
                var time = times[k];
                $('select[name="time"]').append(`<option value="${time}">${time}</option>`);
            }
        });
    }


    function getCalendar(year, mon) {
        var jmon= translite['month_name'];
        var jdn= translite['day_week_name'];

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
            var dn=jdn[lang][x];
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
                <span>${year} - ${jmon[lang][tmon]}</span>
                <span class="data_change" data_year="${nexty}" data_month="${nextm}">></span>
            </th>
        </tr>
        ${txt}
        </table>`;

        return calendar;
    }


});
