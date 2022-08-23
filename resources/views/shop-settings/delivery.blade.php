@extends('layouts.master')

@section('title', 'настройки доставки')

@section('head')


    <script src="{{ asset('js/delivery-settings.js') }}" defer></script>


    <script>
        var cityes = @json($cityes['citys_all']);
    </script>

    <style>
        th, td {
            max-width: 500px;
        }
    </style>

@stop
@section('sidebar')
    @parent
@stop

@section('content')

        <div class="box_inline">
            <div class="content_list_menu">
                <div>
                    <span class="box_data active" data_name="box_list_1">Основное</span>
                    <span class="box_data" data_name="box_list_2" >Города</span>
                    <span class="box_data" data_name="box_list_3" >дата время</span>
                </div>
            </div>
            <h3><b>Доставка</b></h3>

            <div class="content_list">
                <div class="box_inline content_item box_list_1 active">
                    <h3>Варианты доставки</h3>


                    <form action="{{ route('delivery_save') }}" method="POST" >
                        @csrf
                        <table class="delivery">
                            <tr>
                                <th>
                                    id
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
                            @foreach($delivery['delivery'] as $xk => $value)
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
                                            @foreach($categories as $item)
                                                @if (isset($value['only_categories']))
                                                    @php
                                                        $selected = '';
                                                    @endphp
                                                    @foreach($value['only_categories'] as $delivery_cat_id)
                                                        @if ($delivery_cat_id == $item->id)
                                                            @php
                                                                $selected = 'selected';
                                                            @endphp
                                                        @endif
                                                    @endforeach
                                                    <option {{ $selected }} value="{{ $item->id }}">{{ $item->name }}</option>
                                                @else
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endif
                                            @endforeach
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
                            @endforeach

                            @php
                                $xk++;
                            @endphp

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
                </div>

                <div class="box_inline content_item box_list box_list_2">
                    <form action="{{ route('delivery_save') }}" method="POST" >
                        <input class="blockTextField inputDate" type="date" placeholder="__.__.____" name="mailingBirthday" data-text="Birthday">
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

                </div>

                <div class="box_inline content_item box_list box_list_3">
                    <h3>
                        настройка даты времени доставки
                    </h3>
                    <form action="{{ route('delivery_save') }}" method="POST" >
                        @csrf
                        <div class="box_inline box_list">
                            <ul class="list options_list">
                                <li data_name="all">
                                    <a >общая информация</a>
                                </li>
                                <li data_name="days">
                                    <a data_name="days">настройка по дням недели</a>
                                </li>
                                <li data_name="date_close">
                                    <a data_name="date_close">даты исключения</a>
                                </li>
                                <li data_name="days_move">
                                    <a data_name="days_move">сдвиг дней для доставки</a>
                                </li>
                            </ul>
                        </div>

                        <div class="box_inline content_list">

                            <div class="content_item all" >

                                ru: <br>
                                <textarea name="shop[delivery_date_time][note][ru]">{{ $shop_setting['delivery_date_time']['note']['ru'] }}</textarea> <br>
                                en: <br>
                                <textarea name="shop[delivery_date_time][note][en]">{{ $shop_setting['delivery_date_time']['note']['en'] }}</textarea> <br>
                                he: <br>
                                <textarea name="shop[delivery_date_time][note][he]">{{ $shop_setting['delivery_date_time']['note']['he'] }}</textarea> <br>
                                <input class="button" type="submit" value="сохранить"><br>
                            </div>

                            <div class="content_item days" >

                                <table>
                                    <tr>
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
                                    </tr>
                                </table>
                                <br>
                                <input class="button" type="submit" value="сохранить">
                            </div>

                            <div class="content_item date_close">
                                <div>
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
                                </div>
                                <br>
                                <input class="button" type="submit" value="сохранить"><br>
                            </div>

                            <div class="content_item days_move">
                                <div>
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
                                </div>
                                <input class="button" type="submit" value="сохранить"><br>
                            </div>

                        </div>
                    </form>

                </div>
            </div>

        </div>

@stop

