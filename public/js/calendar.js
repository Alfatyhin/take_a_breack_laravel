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
    },
    'select_date': {
        'ru': 'Выберите дату',
        'en': 'Select date',
        'he': 'Select date'
    }
};


$(function(){

    $('.show_calendar').click(function () {
        $('.calendar_table').removeClass('hidden');

    });
    
    // текущая дата
    var date=new Date();
    // текущий год
    var year=date.getFullYear();
    // текущий месяц
    var mon=date.getMonth();

    setCalendar(year, mon);


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

        //////////////////////////////////////////////

        //////////////////////////////////////////////

        console.log('stock_mode');
        console.log(stock_mode);

        console.log('delivery_mode');
        console.log(delivery_mode);

        var add_day = shop_setting['delivery_date_time'][delivery_mode][stock_mode] / 1;


        // текущая дата
        var adate=new Date();
        adate.setDate(adate.getDate() + add_day);

        $('.calendar_box .calendar .date').each(function () {
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

        // var date_set = $('.shop_cart_box input.date').val();
        // if (date_set) {
        //     $(`.shop_cart_box table.calendar td[data_date_string='${date_set}']`).addClass('select_date');
        // }

        $('.calendar_box .calendar .data_change').click(function () {
            var year = $(this).attr('data_year');
            var month = $(this).attr('data_month');
            setCalendar(year, month);
        });


        $(document).mouseup(function (e) {
            var container = $(".calendar_table .calendar");
            if (container.has(e.target).length === 0){
                if (!$('.calendar_table').hasClass('hidden')) {
                    $('.calendar_table').addClass('hidden');
                }
            }
        });


        $('.calendar_box .calendar .open_date').click(function () {
            console.log('test');
            var date_delivery = $(this).attr('data_date_string');
            var week_day = $(this).attr('data_week_day');
            $('.calendar_box .calendar .select_date').toggleClass('select_date');
            $(this).toggleClass('select_date');
            $('input.date').val(date_delivery);
            $('.calendar_table').addClass('hidden');


            var times = shop_setting[delivery_mode + '_date_time']['time_day'][week_day];
            $('ul.delivery_time li').each(function () {

                if ($(this).hasClass('default')) {

                } else {
                    $(this).remove();
                }
            });
            for (k in times){
                var time = times[k];
                $('ul.delivery_time').append(`<li  data-time="${time}"><span>${time}</span></li>`);
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



        var txt=``;

        for (x=0; x<=6; x++)
        {
            var dn=jdn[lang][x];
            txt=`${txt} <li class="weekday">${dn}</li>`;
        }
        txt=`${txt}`;

        var i=0;
        while (i<=6)
        {
            x=0;
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

                txt=`${txt} <li class='date ${cl} date_${tyear}-${nmon}-${ndm}'
                data_week_day="${x}"
                data_date_string="${tyear}-${nmon+1}-${ndm}"
                data_date="${tyear}-${nmon}-${ndm}" >
                    <div>
                        ${ndm}
                    </div>
                </li>`;

                ndate.setDate(ndate.getDate() +1);
                x++;
            }
            if (nmon != tmon && nmon != smon)
                i=6;
            // txt=`${txt} </tr>`;
            i++;
        }

        var calendar = `<ul class="calendar">
            <li class="label_select_date">
                <h5>${translite['select_date'][lang]}</h5>
            </li>
            <li  class="header">
                <div>
                    <span class="data_month">${jmon[lang][tmon]}</span>
                    <span class="data_year">${year}</span>
                    <div class="arrows">
                        <span class="data_change back" data_year="${fory}" data_month="${form}">&lt;</span>
                        <span class="data_change next" data_year="${nexty}" data_month="${nextm}"">&gt;</span>
                    </div>
                </div>
            </li>
        ${txt}
        </ul>`;

        return calendar;
    }


});
