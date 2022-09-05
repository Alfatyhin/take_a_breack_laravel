@extends('layouts.master')

@section('title', 'Заказы')


@section('head')


@stop

@section('sidebar')
    @parent
@stop

@section('content')

    @include('shop-settings.layouts.popapp_message')

    <p>
        общая сумма текущий месяц - ( {{ $priceMonth }} )
    </p>

    <p>
        общая сумма за весь год - ( {{ $priceYear }} )
    </p>
    <hr>
    <form method="get" action="{{ route('shop_settings_orders') }}" >
        start date: <input type="date" name="date-from" value="{{ $date_from->format('Y-m-d') }}">
        end date: <input type="date" name="date-to" value="{{ $date_to->format('Y-m-d') }}">
        <input class="button" type="submit" name="date_filter" value="установить даты">
    </form>
    <a class="button" href="{{ route('shop_settings_orders', ['dates' => 'today']) }}"> за сегодня </a>
    <a class="button" href="{{ route('shop_settings_orders', ['dates' => 'month']) }}"> за месяц </a>
    <hr>
    <p>
        отчет за период с {{ $date_from->format('Y-m-d') }} по {{ $date_to->format('Y-m-d') }} <br>
        @foreach($paydPeriodInfo as $key => $summ)
            &nbsp; &nbsp; &nbsp; {{ $key }} - {{ $summ }} <br>
        @endforeach
    </p>


    <form action="{{ route('shop_settings_orders') }}" method="get">
        <p>
            Search bu order id <input name="order_id" type="text"
                                      @if ($order_id)
                                      value="{{ $order_id }}"
                @endif
            >

            <input type="submit" value="search">

            @if ($order_id && $orderSearch == false)
                <span>
                        order not found
                    </span>
            @endif
        </p>
    </form>


    <table>
        @if ($orderSearch)

            <tr>
                <td>
                    @if (!empty($orderSearch->deleted_at))
                        <p class="deleted_at" >
                            удален {{ $orderSearch->deleted_at }} <br>
                            <a class="button" href="{{ route('restore_order', ['id' => $orderSearch->order_id]) }}" >restore</a>
                        </p>

                    @endif

                ( {{ $orderSearch->id }} ) <b>#{{ $orderSearch->order_id }}</b>  <br>
                    Дата: {{ $orderSearch->created_at }} <br>
                    @if(empty($orderSearch->amoId))
                        <a class="button" href="{{ route('api_create_amo_order', ['id' => $orderSearch->order_id]) }}" >
                            api create amo lead
                        </a>
                    @else
                        Amo Id:  <a class="border-bottom" href="{{ 'https://takebreak.amocrm.ru/leads/detail/'.$orderSearch->amoId }}" target="_blank">
                            {{ $orderSearch->amoId }}
                        </a>
                    @endif
                    <br>
                    Оллата <b>{{ $paymentMethod[$orderSearch->paymentMethod] }}</b>
                    статус <b>{{ $paymentStatus[$orderSearch->paymentStatus] }}</b>
                    дата <b>{{ $orderSearch->paymentDate }}</b>
                    <br>
                    Инвойс <b>{{ $invoiceStatus[$orderSearch->invoiceStatus] }}</b>
                    <hr>
                    Сумма <b>{{ $orderSearch->orderPrice }}</b>
                    <br>

                </td>


                <td>
                    <span class="position-absolute text-small button show-hide"></span>

                    Детали: <br>

                    @php
                        $orderData = json_decode($orderSearch->orderData, true);
                    @endphp

                    <p>
                        <b>имя:</b> <a href="{{ route('client_data', ['client' => $orderSearch->clientId]) }}" >{{ $orderSearch->name }} </a><br>
                        <b>email:</b> {{ $orderSearch->email }} <br>
                        @isset($orderData['phone'])
                        <b>tel:</b> {{ $orderData['phone'] }}
                        @endisset
                    </p>

                    <hr>
                    @isset($orderData['order_data']['products'])
                        @foreach($orderData['order_data']['products'] as $product)
                            <b>
                                @if (!empty($product['name']['ru']))
                                    {{ $product['name']['ru'] }}

                                @else
                                    {{ $product['name']['en'] }}
                                @endif
                            </b>

                            @if (isset($product['options']))
                                @foreach($product['options'] as $option)
                                    {{ $option['name']['ru'] }} -
                                    {{ $option['value']['text'] }}
                                    ( {{ $option['value']['textTranslated']['ru'] }} )
                                @endforeach
                            @endif
                            - {{ $product['count'] }} шт

                            <br>
                        @endforeach
                    @endisset
                    @isset($orderData['otherPerson'])
                        <hr>
                        <b>заказ для друга:</b> <br>
                        <b>имя:</b> {{ $orderData['nameOtherPerson'] }} <br>
                        <b>tel:</b> {{ $orderData['phoneOtherPerson'] }}
                    @endisset
                    @isset($orderData['client_comment'])
                        <hr>
                        <b>комментарий клиента:</b> <br>
                        <div class="text_block">
                            {{ $orderData['client_comment'] }}
                        </div>
                    @endisset
                    @isset($orderData['order_data']['tips'])
                        <hr>
                        <b>чаевые:</b>
                        {{ $orderData['order_data']['tips'] }}
                    @endisset

                    @isset($orderData['order_data']['discount'])
                        <hr>
                        <b>Скидка купон:</b>
                    @endisset

                    @isset($orderData['delivery'])
                        @if($orderData['delivery'] == 'delivery')
                            <hr>
                            <b>Доставка:</b>
                            г-{{ $orderData['city'] }} <br>
                            ул-{{ $orderData['street'] }}
                            {{ $orderData['house'] }}
                            кв-{{ $orderData['flat'] }}
                            @isset($orderData['floor'])
                                эт-{{ $orderData['floor'] }}
                            @endisset
                        @else
                            <hr>
                            <b>Самовывоз</b>
                            @isset($orderData['order_data']['delivery_discount'])
                                <br> <b>скидка:</b>
                                {{ $orderData['order_data']['delivery_discount'] }}
                            @endisset
                        @endif
                    @endisset

                    <hr>
                    @isset($orderData['date'])
                        <b>Дата:</b>
                        {{ $orderData['date'] }} {{ $orderData['time'] }}

                    @endisset
                    <div class="hide">
                        {{ $orderSearch->orderData }}
                    </div>

                </td>


                <td>
                    <span class="position-absolute text-small button show-hide"></span>

                    <a class="hide button" href="{{ route('delete_order', ['id' => $orderSearch->order_id]) }}" >delete</a><br>
                    <a class="hide button" href="{{ route('orders_test_mail', ['id' => $orderSearch->order_id]) }}" >test mail</a>
                    <br>
                    {{--                           <a class="hide button" href="{{ route('amo.create.lead', ['id' => $item->order_id]) }}" >--}}
                    {{--                               new amo lead--}}
                    {{--                           </a> <br>--}}
                    <a class="hide button" href="{{ route('api_create_amo_order', ['id' => $orderSearch->order_id]) }}" >
                        api create amo lead
                    </a>
                    <div class="hide">
                        <hr><br>
                        <a class="button" href="{{ route('paypal_button', ['id' => $orderSearch->order_id]) }}" >
                            test paypal button
                        </a>
                    </div>

                    @if ($orderSearch->paymentMethod == 1 && $orderSearch->paymentStatus != 4)
                        <hr>
                        <a class="hide button" href="{{ route('order_sheck_payment_status_icredit', ['orderId' => $orderSearch->order_id]) }}" >
                            check status
                        </a>
                    @endif

                    @if ($orderSearch->invoiceStatus == 0)
                        <div class="hide">
                            <br>
                            <a class="button" href="{{ route('invoice_create', ['orderId' => $orderSearch->order_id]) }}" >
                                create invoice
                            </a> <br>
                        </div>
                    @endif
                </td>

            </tr>
        @endif


        @if(isset($orders))
            @foreach($orders as $item)
                <tr>
                    <td>
                        @if (!empty($item->deleted_at))
                           <p class="deleted_at" >
                               удален {{ $item->deleted_at }}<br>
                               <a class="hide button" href="{{ route('restore_order', ['id' => $item->order_id]) }}" >restore</a>
                           </p>
                        @endif
                        ( {{ $item->id }} ) <b>#{{ $item->order_id }}</b> | <b>gId:</b>{{ $item->gclientId }}  <br>
                        Дата: {{ $item->created_at }} <br>
                        @if(empty($item->amoId))
                            <a class="button" href="{{ route('api_create_amo_order', ['id' => $item->order_id]) }}" >
                                api create amo lead
                            </a>
                        @else
                            Amo Id:  <a class="border-bottom" href="{{ 'https://takebreak.amocrm.ru/leads/detail/'.$item->amoId }}" target="_blank">
                                {{ $item->amoId }}
                            </a>
                        @endif
                        <br>
                        Оллата <b>{{ $paymentMethod[$item->paymentMethod] }}</b>
                        статус <b>{{ $paymentStatus[$item->paymentStatus] }}</b>
                        <br>
                        дата опл <b>{{ $item->paymentDate }}</b>
                        <br>
                        Инвойс <b>{{ $invoiceStatus[$item->invoiceStatus] }}</b>
                            @if(!empty($item->invoiceData))
                                @php
                                $invoice_data = json_decode($item->invoiceData, true);
                                @endphp

                                @isset($invoice_data['url']['en'])
                                    <a class="button" href="{{ $invoice_data['url']['en'] }}">en</a>
                                @endisset
                                @isset($invoice_data['url']['he'])
                                    <a class="button" href="{{ $invoice_data['url']['he'] }}">he</a>
                                @endisset

                            @endif
                        <hr>
                        Сумма <b>{{ $item->orderPrice }}</b>
                        <br>

                    </td>


                    <td>
                        <span class="position-absolute text-small button show-hide"></span>

                        Детали: <br>

                        @php
                            $orderData = json_decode($item->orderData, true);
                        @endphp

                        @if (isset($orderData['Cart']))
                            {{ $orderData['Cart']['person']['name'] }} <br>
                            {{ $orderData['Cart']['person']['email'] }} <br>
                            {{ $orderData['Cart']['person']['phone'] }} <br>

                            <hr>
                            @foreach($orderData['Cart']['items'] as $product)
                                <b>
                                    @if (!empty($product['nameTranslated']['ru']))
                                        {{ $product['nameTranslated']['ru'] }}

                                    @else
                                        {{ $product['name'] }}
                                    @endif
                                </b>

                                @if (isset($product['info']))
                                    {{ $product['info'] }}
                                @endif
                                - {{ $product['count'] }} шт

                                <br>
                            @endforeach
                            <hr>

                            @if (isset($orderData['option']['tips_value']))
                                Tips: {{ $orderData['option']['tips_price'] }}% - {{ $orderData['option']['tips_value'] }}
                                <hr>
                            @endif

                            @if (!empty($orderData['option']['comment']))
                                Коментарий:
                                <p>{{ $orderData['option']['comment'] }}</p>
                            @endif
                            @if (!empty($orderData['option']['promo_code']))
                                <p>
                                    купон: {{ $orderData['option']['promo_code'] }}
                                </p>
                            @endif
                            <hr>

                            <p>
                                @if ($orderData['option']['delivery_method'] == 'pickup')
                                    Самовывоз
                                @else
                                    Доставка:
                                @endif
                                @if (isset($orderData['option']['delivery_variant']))
                                    {{ $orderData['option']['delivery_variant'] }}
                                @endif
                                <br>
                                Дата: {{ $orderData['option']['delivery_date'] }} <br>
                                время: {{ $orderData['option']['delivery_time'] }} <br>
                                @if (isset($orderData['Cart']['person']['address']))
                                    @foreach($orderData['Cart']['person']['address'] as $k=>$v)
                                        {{ $k }} - {{ $v }} <br>
                                    @endforeach
                                @endif
                            </p>

                            <hr>
                            <div class="hide">
                                {{ $item->orderData }}
                            </div>

                        @else

                            <p>
                                <b>имя:</b> <a href="{{ route('client_data', ['client' => $item->clientId]) }}" >{{ $item->name }} </a><br>
                                <b>email:</b> {{ $item->email }} <br>
                                @isset($orderData['phone'])
                                    <b>tel:</b> {{ $orderData['phone'] }}
                                @endisset
                            </p>

                            <hr>
                            @isset($orderData['order_data']['products'])
                                @foreach($orderData['order_data']['products'] as $product)
                                    <b>
                                        @if (!empty($product['name']['ru']))
                                            {{ $product['name']['ru'] }}

                                        @else
                                            {{ $product['name']['en'] }}
                                        @endif
                                    </b>

                                    @if (isset($product['options']))
                                        @foreach($product['options'] as $option)
                                            {{ $option['name']['ru'] }} -
                                            {{ $option['value']['text'] }}
                                            ( {{ $option['value']['textTranslated']['ru'] }} )
                                        @endforeach
                                    @endif
                                    - {{ $product['count'] }} шт

                                    <br>
                                @endforeach
                            @endisset
                            @isset($orderData['otherPerson'])
                                <hr>
                                <b>заказ для друга:</b> <br>
                                <b>имя:</b> {{ $orderData['nameOtherPerson'] }} <br>
                                <b>tel:</b> {{ $orderData['phoneOtherPerson'] }}
                            @endisset
                            @isset($orderData['client_comment'])
                                <hr>
                                <b>комментарий клиента:</b> <br>
                                <div class="text_block">
                                    {{ $orderData['client_comment'] }}
                                </div>
                            @endisset
                            @isset($orderData['order_data']['tips'])
                                <hr>
                                <b>чаевые:</b>
                                {{ $orderData['order_data']['tips'] }}
                            @endisset

                            @isset($orderData['order_data']['discount'])
                                <hr>
                                <b>Скидка купон:</b>
                            @endisset
                            @isset($orderData['delivery'])
                                @if($orderData['delivery'] == 'delivery')
                                    <hr>
                                    <b>Доставка:</b>
                                    г-{{ $orderData['city'] }} <br>
                                    ул-{{ $orderData['street'] }}
                                    {{ $orderData['house'] }}
                                    кв-{{ $orderData['flat'] }}
                                    @isset($orderData['floor'])
                                        эт-{{ $orderData['floor'] }}
                                    @endisset
                                @else
                                    <hr>
                                    <b>Самовывоз</b>
                                    @isset($orderData['order_data']['delivery_discount'])
                                        <br> <b>скидка:</b>
                                        {{ $orderData['order_data']['delivery_discount'] }}
                                    @endisset
                                @endif
                            <hr>
                            <b>Дата:</b>
                            {{ $orderData['date'] }} {{ $orderData['time'] }}

                            @endisset
                            <div class="hide">
                                {{ $item->orderData }}
                            </div>
                        @endif

                    </td>


                    <td>
                        <span class="position-absolute text-small button show-hide"></span>


                        <a class=" button" href="{{ route('amo_create_invoice_to_order', ['order' => $item->id]) }}" >
                            add amo invoice to lead
                        </a>
                        <br>

                        <a class="hide button" href="{{ route('delete_order', ['id' => $item->order_id]) }}" >delete</a><br>
                        <a class=" button" href="{{ route('orders_test_mail', ['id' => $item->order_id]) }}" >test mail</a><br>
                        <a class=" button" href="{{ route('orders_test_sendpulse', ['order' => $item->id]) }}" >test sendpulse</a>
                        <br>

                        <a class="hide button" href="{{ route('api_create_amo_order', ['id' => $item->order_id]) }}" >
                            api create amo lead
                        </a>
                        <div class="hide">
                            <hr><br>
                            <a class="button" href="{{ route('paypal_button', ['id' => $item->order_id]) }}" >
                                test paypal button
                            </a>
                        </div>

                        @if ($item->paymentMethod == 1 && $item->paymentStatus != 4)
                            <hr>
                            <a class="hide button" href="{{ route('order_sheck_payment_status_icredit', ['orderId' => $item->order_id]) }}" >
                                check status
                            </a>
                        @endif

                        @if ($item->invoiceStatus == 0)
                            <div class="hide">
                                <br>
                                <a class="button" href="{{ route('invoice_create', ['orderId' => $item->order_id]) }}" >
                                    create invoice
                                </a> <br>
                            </div>
                        @endif
                        <div class="hide">
                            <br>
                            <a class="button" href="{{ route('test_get_url', [ 'order_id' => $item->id]) }}" >
                                test Icredit
                            </a> <br>
                        </div>
                        <div class="hide">
                            <br>
                            <a class="button" href="{{ route('paypal_button', [ 'order_id' => $item->order_id]) }}" >
                                test PayPal
                            </a> <br>
                        </div>
                    </td>

                </tr>
            @endforeach
        @endif
    </table>

    <div>
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 paginate">
            {{ $orders->links() }}
        </div>
    </div>

@stop


