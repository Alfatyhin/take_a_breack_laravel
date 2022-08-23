@extends('layouts.master')

@section('title', 'Shop Ecwid Settings')

@section('head')
    <script src="{{ asset('js/server-ecwid-setting.js?v0.0.9') }}" defer></script>
    <script>

        var cityes = @json($cityes['citys_all']);
    </script>
    <style>
        textarea {
            min-width: 400px;
            min-height: 200px;
        }
    </style>

@stop

@section('sidebar')
    @parent
@stop

@section('content')
    @if ($message)
        <div class="pop-ap message">
            <div class="body">
                <span class="close"></span>
                @foreach($message as $mess)
                    <p>
                        {{ $mess }}
                    </p>
                @endforeach
            </div>
        </div>
    @endif




    <br> <hr>
    <h3>Shop setting</h3>


    <form action="{{ route('ecwid_settings') }}" method="POST" >
        @csrf
        <table class="order_calc">
            <caption>
                Калькулятор Заказа
            </caption>
            <tr>
                <th>
                    Описание
                </th>
                <th>
                    Верхняя сумма
                </th>
                <th>
                    Бонусы
                </th>
            </tr>

            @if (!empty($order_calc))

                @php
                    $selected['present'] = '';
                    $selected['discount'] = '';
                    $selected['discount_delivery'] = '';
                    $discount_checked['ABC'] = '';
                    $discount_checked['PERCENT'] = '';
                @endphp

                @foreach($order_calc as $key => $item)
                    <tr>
                        <td>
                            ru: <br>
                            <textarea name="order_calc[{{ $key }}][lang][ru]">{{ $item['lang']['ru'] }}</textarea> <br>
                            en: <br>
                            <textarea name="order_calc[{{ $key }}][lang][en]">{{ $item['lang']['en'] }}</textarea> <br>
                            he: <br>
                            <textarea name="order_calc[{{ $key }}][lang][he]">{{ $item['lang']['he'] }}</textarea> <br>
                        </td>
                        <td>
                            <input type="number" name="order_calc[{{ $key }}][to_summ]" value="{{ $item['to_summ'] }}">
                        </td>
                        <td>
                            <select class="order_calc_type_name" data_key="{{ $key }}" data_val="{{ $item['type'] }}" name="order_calc[{{ $key }}][type]">
                                @php
                                    $selected[$item['type']] = 'selected';
                                @endphp
                                <option value="present" {{ $selected['present'] }}> present </option>
                                <option value="discount"  {{ $selected['discount'] }}> discount </option>
                                <option value="discount_delivery" {{ $selected['discount_delivery'] }}> discount delivery </option>
                            </select>
                            <div class="calc_type key_{{ $key }}"  data_key="{{ $key }}" >

                                <div class="present hidden">
                                    <p> Categories:

                                        <select class="categories_list_2 key_{{ $key }}" data_key="{{ $key }}" >
{{--                                            @foreach($categories['items'] as $k => $category)--}}
{{--                                                <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>--}}
{{--                                            @endforeach--}}
                                        </select>
                                        <br>
                                        Products:
{{--                                        @if (isset($item['product_id']))--}}
{{--                                            <span class="products_list_2 key_{{ $key }} product_key" data_key="{{ $key }}" data_id="{{ $item['product_id'] }}">{{ $item['product_id'] }}</span>--}}
{{--                                            @else--}}
{{--                                            <span class="products_list_2 key_{{ $key }}"></span>--}}
{{--                                        @endif--}}

                                    </p>
                                </div>
                                <div class="discount discount_delivery hidden" data_val="">
                                    @php
                                        $discount_checked[$item['discount']['type']] = 'checked';
                                    @endphp
                                    <label>
                                        <input type="radio" name="order_calc[{{ $key }}][discount][type]" value="PERCENT"
                                            {{ $discount_checked['PERCENT'] }}> в процентах <br>
                                    </label>
                                    <label>
                                        <input type="radio" name="order_calc[{{ $key }}][discount][type]" value="ABS"
                                            {{ $discount_checked['ABC'] }}> абсолютная сумма <br>
                                    </label>
                                    @if (isset($item['discount']['value']))
                                        <input type="text" name="order_calc[{{ $key }}][discount][value]" value="{{ $item['discount']['value'] }}">
                                        @else
                                        <input type="text" name="order_calc[{{ $key }}][discount][value]" >
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                    @php
                        $key++;
                    @endphp
            @endif


            <tr>
                <td>
                    ru: <br>
                    <textarea name="order_calc[{{ $key }}][lang][ru]"></textarea> <br>
                    en: <br>
                    <textarea name="order_calc[{{ $key }}][lang][en]"></textarea> <br>
                    he: <br>
                    <textarea name="order_calc[{{ $key }}][lang][he]"></textarea> <br>
                </td>
                <td>
                    <input type="number" name="order_calc[{{ $key }}][to_summ]">
                </td>
                <td>
                    <select class="order_calc_type_name" data_key="{{ $key }}" name="order_calc[{{ $key }}][type]">
                        <option value="present"> present </option>
                        <option value="discount"> discount </option>
                        <option value="discount_delivery"> discount delivery </option>
                    </select>
                    <div class="calc_type key_{{ $key }}"  data_key="{{ $key }}" >
                        <div class="present hidden">
                            <p> Categories:

                                <select class="categories_list_2 key_{{ $key }}" data_key="{{ $key }}" >
{{--                                    @foreach($categories['items'] as $k => $category)--}}
{{--                                        <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>--}}
{{--                                    @endforeach--}}
                                </select>
                                <br>
                                Products:
                                <span class="products_list_2 key_{{ $key }}"></span>
                            </p>
                        </div>
                        <div class="discount hidden">
                            <label>
                                <input type="radio" name="order_calc[{{ $key }}][discount][type]" value="PERCENT" checked> в процентах <br>
                            </label>
                            <label>
                                <input type="radio" name="order_calc[{{ $key }}][discount][type]" value="ABS"> абсолютная сумма <br>
                            </label>
                            <input type="text" name="order_calc[{{ $key }}][discount][value]" >
                        </div>
                        <div class="discount_delivery hidden">
                            <label>
                                <input type="radio" name="order_calc[{{ $key }}][discount][type]" value="PERCENT" checked> в процентах <br>
                            </label>
                            <label>
                                <input type="radio" name="order_calc[{{ $key }}][discount][type]" value="ABS"> абсолютная сумма <br>
                            </label>
                            <input type="text" name="order_calc[{{ $key }}][discount][value]" >
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <input class="button" type="submit">
    </form>


    <hr>
    <form action="{{ route('ecwid_settings') }}" method="POST" >
    @csrf
        <table>
            <caption>
                Самовывоз
            </caption>
            <tr>
                <th>
                    название
                </th>
                <th>
                    инструкция
                </th>
                <th>
                    инфо блок для корзины
                </th>
                <th>
                    стоимость
                </th>
            </tr>
            <tr>
                <td>
                    ru: <br>
                    <input type="text" name="shop[pickup][name][ru]" value="{{ $shop_setting['pickup']['name']['ru'] }}"
                           placeholder=""> <br>
                    en: <br>
                    <input type="text" name="shop[pickup][name][en]" value="{{ $shop_setting['pickup']['name']['en'] }}"
                           placeholder=""> <br>
                    he: <br>
                    <input type="text" name="shop[pickup][name][he]" value="{{ $shop_setting['pickup']['name']['he'] }}"
                           placeholder="">
                </td>
                <td>
                    ru: <br>
                    <textarea name="shop[pickup][note][ru]">{{ $shop_setting['pickup']['note']['ru'] }}</textarea> <br>
                    en: <br>
                    <textarea name="shop[pickup][note][en]">{{ $shop_setting['pickup']['note']['en'] }}</textarea> <br>
                    he: <br>
                    <textarea name="shop[pickup][note][he]">{{ $shop_setting['pickup']['note']['he'] }}</textarea>
                </td>
                <td>
                    ru: <br>
                    <input type="text" name="shop[pickup][info][ru]" value="{{ $shop_setting['pickup']['info']['ru'] }}"
                           placeholder=""> <br>
                    en: <br>
                    <input type="text" name="shop[pickup][info][en]" value="{{ $shop_setting['pickup']['info']['en'] }}"
                           placeholder=""> <br>
                    he: <br>
                    <input type="text" name="shop[pickup][info][he]" value="{{ $shop_setting['pickup']['info']['he'] }}"
                           placeholder="">
                </td>
                <td>
                    <input type="text" name="shop[pickup][rate]" value="{{ $shop_setting['pickup']['rate'] }}"
                           placeholder="">
                </td>
            </tr>
        </table>

        <table>
            <caption>
                оплата наличными
            </caption>
            <tr>
                <th>
                    инструкция
                </th>
            </tr>
            <tr>
                <td>
                    ru: <br>
                    <textarea name="shop[cash_payment][note][ru]">{{ $shop_setting['cash_payment']['note']['ru'] }}</textarea> <br>
                    en: <br>
                    <textarea name="shop[cash_payment][note][en]">{{ $shop_setting['cash_payment']['note']['en'] }}</textarea> <br>
                    he: <br>
                    <textarea name="shop[cash_payment][note][he]">{{ $shop_setting['cash_payment']['note']['he'] }}</textarea>
                </td>
            </tr>
        </table>
        <table>
            <caption>
                настройка даты времени доставки
            </caption>
            <tr>
                <th>
                    общая информация
                </th>
                <th>
                    настройка по дням недели
                </th>
                <th>
                    даты исключения
                </th>
                <th>
                    сдвиг дней для доставки
                </th>
            </tr>
            <tr>
                <td>
                    ru: <br>
                    <textarea name="shop[delivery_date_time][note][ru]">{{ $shop_setting['delivery_date_time']['note']['ru'] }}</textarea> <br>
                    en: <br>
                    <textarea name="shop[delivery_date_time][note][en]">{{ $shop_setting['delivery_date_time']['note']['en'] }}</textarea> <br>
                    he: <br>
                    <textarea name="shop[delivery_date_time][note][he]">{{ $shop_setting['delivery_date_time']['note']['he'] }}</textarea>
                </td>
                <td>
                    <input type="checkbox" name="shop[delivery_date_time][weeks_day][0]"
                    @if (isset($shop_setting['delivery_date_time']['weeks_day'][0]))
                        checked
                    @endif
                    > Вс <br>
                    <input type="checkbox" name="shop[delivery_date_time][time_day][0][0]"
                           value="11:00-14:00"
                                @if (isset($shop_setting['delivery_date_time']['time_day'][0][0]))
                                checked
                            @endif
                        >11:00-14:00
                    <input type="checkbox" name="shop[delivery_date_time][time_day][0][1]"
                           value="14:00-17:00"
                                @if (isset($shop_setting['delivery_date_time']['time_day'][0][1]))
                           checked
                            @endif
                        >14:00-17:00
                    <input type="checkbox" name="shop[delivery_date_time][time_day][0][2]"
                           value="17:00-20:00"
                                @if (isset($shop_setting['delivery_date_time']['time_day'][0][2]))
                                checked
                            @endif
                        >17:00-20:00
                    <hr> <br>
                    <input type="checkbox" name="shop[delivery_date_time][weeks_day][1]"
                        @if (isset($shop_setting['delivery_date_time']['weeks_day'][1]))
                           checked
                        @endif
                    > Пн <br>
                    <input type="checkbox" name="shop[delivery_date_time][time_day][1][0]"
                           value="11:00-14:00"
                           @if (isset($shop_setting['delivery_date_time']['time_day'][1][0]))
                           checked
                        @endif
                    >11:00-14:00
                    <input type="checkbox" name="shop[delivery_date_time][time_day][1][1]"
                           value="14:00-17:00"
                           @if (isset($shop_setting['delivery_date_time']['time_day'][1][1]))
                           checked
                        @endif
                    >14:00-17:00
                    <input type="checkbox" name="shop[delivery_date_time][time_day][1][2]"
                           value="17:00-20:00"
                           @if (isset($shop_setting['delivery_date_time']['time_day'][1][2]))
                           checked
                        @endif
                    >17:00-20:00
                    <hr> <br>
                    <input type="checkbox" name="shop[delivery_date_time][weeks_day][2]"
                        @if (isset($shop_setting['delivery_date_time']['weeks_day'][2]))
                           checked
                        @endif
                    > Вт <br>
                    <input type="checkbox" name="shop[delivery_date_time][time_day][2][0]"
                           value="11:00-14:00"
                           @if (isset($shop_setting['delivery_date_time']['time_day'][2][0]))
                           checked
                        @endif
                    >11:00-14:00
                    <input type="checkbox" name="shop[delivery_date_time][time_day][2][1]"
                           value="14:00-17:00"
                           @if (isset($shop_setting['delivery_date_time']['time_day'][2][1]))
                           checked
                        @endif
                    >14:00-17:00
                    <input type="checkbox" name="shop[delivery_date_time][time_day][2][2]"
                           value="17:00-20:00"
                           @if (isset($shop_setting['delivery_date_time']['time_day'][2][2]))
                           checked
                        @endif
                    >17:00-20:00
                    <hr> <br>
                    <input type="checkbox" name="shop[delivery_date_time][weeks_day][3]"
                        @if (isset($shop_setting['delivery_date_time']['weeks_day'][3]))
                           checked
                        @endif
                    > Ср <br>
                    <input type="checkbox" name="shop[delivery_date_time][time_day][3][0]"
                           value="11:00-14:00"
                           @if (isset($shop_setting['delivery_date_time']['time_day'][3][0]))
                           checked
                        @endif
                    >11:00-14:00
                    <input type="checkbox" name="shop[delivery_date_time][time_day][3][1]"
                           value="14:00-17:00"
                           @if (isset($shop_setting['delivery_date_time']['time_day'][3][1]))
                           checked
                        @endif
                    >14:00-17:00
                    <input type="checkbox" name="shop[delivery_date_time][time_day][3][2]"
                           value="17:00-20:00"
                           @if (isset($shop_setting['delivery_date_time']['time_day'][3][2]))
                           checked
                        @endif
                    >17:00-20:00
                    <hr> <br>
                    <input type="checkbox" name="shop[delivery_date_time][weeks_day][4]"
                           @if (isset($shop_setting['delivery_date_time']['weeks_day'][4]))
                           checked
                        @endif
                    > Чт <br>
                    <input type="checkbox" name="shop[delivery_date_time][time_day][4][0]"
                           value="11:00-14:00"
                           @if (isset($shop_setting['delivery_date_time']['time_day'][4][0]))
                           checked
                        @endif
                    >11:00-14:00
                    <input type="checkbox" name="shop[delivery_date_time][time_day][4][1]"
                           value="14:00-17:00"
                           @if (isset($shop_setting['delivery_date_time']['time_day'][4][1]))
                           checked
                        @endif
                    >14:00-17:00
                    <input type="checkbox" name="shop[delivery_date_time][time_day][4][2]"
                           value="17:00-20:00"
                           @if (isset($shop_setting['delivery_date_time']['time_day'][4][2]))
                           checked
                        @endif
                    >17:00-20:00
                    <hr> <br>
                    <input type="checkbox" name="shop[delivery_date_time][weeks_day][5]"
                           @if (isset($shop_setting['delivery_date_time']['weeks_day'][5]))
                           checked
                        @endif
                    > Пт <br>
                    <input type="checkbox" name="shop[delivery_date_time][time_day][5][0]"
                           value="11:00-14:00"
                           @if (isset($shop_setting['delivery_date_time']['time_day'][5][0]))
                           checked
                        @endif
                    >11:00-14:00
                    <input type="checkbox" name="shop[delivery_date_time][time_day][5][1]"
                           value="14:00-17:00"
                           @if (isset($shop_setting['delivery_date_time']['time_day'][5][1]))
                           checked
                        @endif
                    >14:00-17:00
                    <input type="checkbox" name="shop[delivery_date_time][time_day][5][2]"
                           value="17:00-20:00"
                           @if (isset($shop_setting['delivery_date_time']['time_day'][5][2]))
                           checked
                        @endif
                    >17:00-20:00
                    <hr> <br>
                    <input type="checkbox" name="shop[delivery_date_time][weeks_day][6]"
                           @if (isset($shop_setting['delivery_date_time']['weeks_day'][6]))
                           checked
                        @endif
                    > Сб <br>
                    <input type="checkbox" name="shop[delivery_date_time][time_day][6][0]"
                           value="11:00-14:00"
                           @if (isset($shop_setting['delivery_date_time']['time_day'][6][0]))
                           checked
                        @endif
                    >11:00-14:00
                    <input type="checkbox" name="shop[delivery_date_time][time_day][6][1]"
                           value="14:00-17:00"
                           @if (isset($shop_setting['delivery_date_time']['time_day'][6][1]))
                           checked
                        @endif
                    >14:00-17:00
                    <input type="checkbox" name="shop[delivery_date_time][time_day][6][2]"
                           value="17:00-20:00"
                           @if (isset($shop_setting['delivery_date_time']['time_day'][6][2]))
                           checked
                        @endif
                    >17:00-20:00
                    <hr>
                </td>
                <td>
                    <div class="calendar_box"></div>
                    <div class="unset_dates" >
                        @if (!empty($shop_setting['delivery_date_time']['unset_date']))
                            @foreach($shop_setting['delivery_date_time']['unset_date'] as $key => $val)
                                <p class="{{ $key }} hidden">
                                    <input type="text" name="shop[delivery_date_time][unset_date][{{ $key }}]"
                                           value="true" data_date="{{ $key }}"> {{ $key }}
                                </p>
                            @endforeach
                        @endif
                    </div>
                </td>
                <td>
                    Самовывоз: <br>
                    +<input type="number" name="shop[delivery_date_time][pickup][in_stock]"
                            value="{{ $shop_setting['delivery_date_time']['pickup']['in_stock'] }}"> in stock
                    <br>
                    +<input type="number" name="shop[delivery_date_time][pickup][pre_order]"
                            value="{{ $shop_setting['delivery_date_time']['pickup']['pre_order'] }}"> pre order
                    <hr>
                    Доставка: <br>
                    +<input type="number" name="shop[delivery_date_time][delivery][in_stock]"
                            value="{{ $shop_setting['delivery_date_time']['delivery']['in_stock'] }}"> in stock
                    <br>
                    +<input type="number" name="shop[delivery_date_time][delivery][pre_order]"
                            value="{{ $shop_setting['delivery_date_time']['delivery']['pre_order'] }}"> pre order
                </td>
            </tr>
        </table>

        <input class="button" type="submit">
    </form>

    <br> <hr>
    <h3>Delivery Settings</h3>


    <form action="{{ route('ecwid_settings') }}" method="POST" >
    @csrf
        <table class="delivery">
            <tr>
                <th>
                    n
                </th>
                <th>
                    Служба доставки
                </th>
                <th>
                    Зона доставки (города)
                </th>
                <th>
                    Мин. сумма заказа
                </th>
                <th>
                    Стоимость доставки
                </th>
                <th>
                    Стоимость доставки от суммы закза
                </th>
            </tr>
            @php
                $xk = 0;
            @endphp
            @foreach($delivery['delivery'] as $k => $value)
                <tr>
                    <td>
                        {{ $xk }}
                    </td>
                    <td>
                        ru: <br>
                        <input type="text" name="delivery[{{ $xk }}][name][ru]" value="{{ $value['name']['ru'] }}"
                               placeholder="введите название доставки">
                        en: <br>
                        <input type="text" name="delivery[{{ $xk }}][name][en]" value="{{ $value['name']['en'] }}"
                               placeholder="введите название доставки">
                        he: <br>
                        <input type="text" name="delivery[{{ $xk }}][name][he]" value="{{ $value['name']['he'] }}"
                               placeholder="введите название доставки">
                    </td>
                    <td>
                        <div class="city_select_out out_{{ $xk }}">
                            @foreach($value['cityes'] as $key => $v)
                                <nobr>
                                    <input  class="city_{{ $key }}" type="checkbox" data_key="{{ $key }}"
                                            name="city[{{ $xk }}][{{ $v }}]"
                                            value="{{ $key }}" checked> {{ $cityes['citys_all'][$key]['ru'] }}
                                </nobr>
                            @endforeach
                        </div>
                        <br>
                        <input class="city_input test" data_x="{{ $xk }}" type="text">
                        <br>
                        <div class="city_search_out out_{{ $xk }}"></div>

                        <hr>
                        только для категорий:
                        <select class="categories" name="delivery[{{ $xk }}][only_categories][]" multiple >
{{--                            @foreach($categories['items'] as $item)--}}
{{--                                @if (isset($value['only_categories']))--}}
{{--                                    @php--}}
{{--                                        $selected = '';--}}
{{--                                    @endphp--}}
{{--                                    @foreach($value['only_categories'] as $delivery_cat_id)--}}
{{--                                        @if ($delivery_cat_id == $item['id'])--}}
{{--                                            @php--}}
{{--                                                $selected = 'selected';--}}
{{--                                            @endphp--}}
{{--                                        @endif--}}
{{--                                    @endforeach--}}
{{--                                    <option {{ $selected }} value="{{ $item['id'] }}">{{ $item['name'] }}</option>--}}
{{--                                @else--}}
{{--                                    <option value="{{ $item['id'] }}">{{ $item['name'] }}</option>--}}
{{--                                @endif--}}
{{--                            @endforeach--}}
                        </select>
                    </td>
                    <td>
                        <input size="3" type="number" name="delivery[{{ $xk }}][min_sum_order]"
                               value="{{$value['min_sum_order']}}"
                               placeholder="Мин. сумма заказа">
                    </td>
                    <td>
                        <input size="3" type="number" name="delivery[{{ $xk }}][rate_delivery]"
                               value="{{ $value['rate_delivery'] }}"
                               placeholder="Стоимость доставки">
                    </td>
                    <td>
                        @if (isset($value['rate_delivery_to_summ_order']))
                            @php
                                $x = 0;
                            @endphp
                            @foreach($value['rate_delivery_to_summ_order'] as $key => $val)
                                <hr>
                                <nobr>
                                    от <input size="3" type="number"
                                           name="delivery[{{ $xk }}][rate_delivery_to_summ_order][{{ $x }}][sum_order][min]"
                                           value="{{ $val['sum_order']['min'] }}"
                                           placeholder="от">
                                    до <input size="3" type="number"
                                           name="delivery[{{ $xk }}][rate_delivery_to_summ_order][{{ $x }}][sum_order][max]"
                                           value="{{ $val['sum_order']['max'] }}"
                                           placeholder="до">
                                </nobr>
                            <br>
                                Стоимость доставки: <input size="3" type="number"
                                       name="delivery[{{ $xk }}][rate_delivery_to_summ_order][{{ $x }}][rate_delivery]"
                                       value="{{ $val['rate_delivery'] }}"
                                       placeholder="">
                                <hr>
                                @php
                                    $x++;
                                @endphp
                            @endforeach
                        @endif
                        добавить:<br>
                            <nobr>
                                от <input size="3" type="number"
                                          name="delivery[{{ $xk }}][rate_delivery_to_summ_order][{{ $key+1 }}][sum_order][min]"
                                          placeholder="от">
                                до <input size="3" type="number"
                                          name="delivery[{{ $xk }}][rate_delivery_to_summ_order][{{ $key+1 }}][sum_order][max]"
                                          placeholder="до">
                            </nobr>
                            <br>
                            Стоимость доставки: <input size="3" type="number"
                                   name="delivery[{{ $xk }}][rate_delivery_to_summ_order][{{ $key+1 }}][rate_delivery]"
                                   placeholder="">
                    </td>
                </tr>
                @php
                    $xk++;
                @endphp
            @endforeach


            <tr>
                <td>{{ $xk }}</td>
                <td>
                    ru: <br>
                    <input type="text" name="delivery[{{ $xk }}][name][ru]" placeholder="введите название доставки">
                    en: <br>
                    <input type="text" name="delivery[{{ $xk }}][name][en]" placeholder="введите название доставки">
                    he: <br>
                    <input type="text" name="delivery[{{ $xk }}][name][he]" placeholder="введите название доставки">
                </td>
                <td>
                    <div class="city_select_out out_{{ $xk }}"></div>
                    <br>
                    <input class="city_input test" data_x="{{ $xk }}" type="text">
                    <br>
                    <div class="city_search_out out_{{ $xk }}"></div>
                </td>
                <td>
                    <input size="3" type="number" name="delivery[{{ $xk }}][min_sum_order]" placeholder="Мин. сумма заказа">
                </td>
                <td>
                    <input size="3" type="number" name="delivery[{{ $xk }}][rate_delivery]" placeholder="Стоимость доставки">
                </td>
                <td>
                       <nobr>
                           от <input size="3" type="number"
                                     name="delivery[{{ $xk }}][rate_delivery_to_summ_order][0][sum_order][min]"
                                     placeholder="от">
                           до <input size="3" type="number"
                                     name="delivery[{{ $xk }}][rate_delivery_to_summ_order][0][sum_order][max]"
                                     placeholder="до">
                       </nobr>
                    <br>
                    Стоимость доставки: <input size="3" type="number"
                           name="delivery[{{ $xk }}][rate_delivery_to_summ_order][0][rate_delivery]"
                           placeholder="">
                </td>
            </tr>
        </table>

        <input class="button" type="submit">
    </form>

    <br> <hr>
    <form action="{{ route('ecwid_settings') }}" method="POST" >
    @csrf
        <table>
            <caption>Таблица городов Израиля</caption>
            <tr>
                <th>
                    №
                </th>
                <th>
                    ru
                </th>
                <th>
                    he
                </th>
                <th>
                    en
                </th>
            </tr>

            @php
                $x = 0;
            @endphp


            @foreach($cityes['citys_all'] as $city)

                <tr>
                    <td>
                        {{ $x+1 }}
                    </td>
                    <td>
                        {{ $city['ru'] }} <br>
                        <input type="text" name="city[{{ $x }}][ru]" value="{{ $city['ru'] }}">
                    </td>
                    <td>
                        {{ $city['he'] }} <br>
                        <input type="text" name="city[{{ $x }}][he]" value="{{ $city['he'] }}">
                    </td>
                    <td class="en_city_{{ $x }}">
                        @if (empty($city['en']))
                            <input class="en_city" data_x="{{ $x }}" type="text" name="city[{{ $x }}][en]" value="">
                            <div class="search_en_city search_en_city_{{ $x }}"></div>
                        @else
                            {{ $city['en'] }} <br>
                            <input type="text" name="city[{{ $x }}][en]" value="{{ $city['en'] }}">
                        @endif
                    </td>
                </tr>
                @php
                    $x++;
                @endphp

            @endforeach

            <tr>
                <td>
                    {{ $x+1 }}
                </td>
                <td>
                    <input type="text" name="city[{{ $x }}][ru]" value="" placeholder="введите название ru">
                </td>
                <td>
                    <input type="text" name="city[{{ $x }}][he]" value="" placeholder="введите название he">
                </td>
                <td>
                    <input type="text" name="city[{{ $x }}][en]" value="" placeholder="введите название en">
                </td>
            </tr>
        </table>
        <input class="button" type="submit" name="save_cityes" value="save" />
    </form>

    <br><hr>
    <h2> Пререводы текстов для магазина </h2>
    <form action="{{ route('ecwid_settings') }}" method="POST" >
    @csrf
        <textarea class="textarea_json" name="shop_translit_json">{{ $shop_translit }}</textarea>

        <input class="button" type="submit" name="save_translit" />
    </form>


@stop

