@extends('layouts.master')

@section('title', 'Заказы')


@section('head')


@stop

@section('sidebar')
    @parent
@stop

@section('content')

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
    <div class="statistic">
        <p>
            отчет за период с {{ $date_from->format('Y-m-d') }} по {{ $date_to->format('Y-m-d') }} <br>
            заказов - {{ $paydPeriodInfo['заказов'] }} <br>
            @isset($paydPeriodInfo['orders'])
                @foreach($paydPeriodInfo['orders'] as $keypm => $item)
                    &nbsp; &nbsp; &nbsp; <b>{{ $paymentMethod[$keypm] }}</b>  <br>
                    @foreach($item as $kps => $value)
                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {{ $paymentStatus[$kps] }} - summ
                        <span class="order_summ">{{ $value['summ'] }}</span>, count {{ $value['count'] }} <br>
                    @endforeach
                @endforeach
            @endisset
        </p>
        <div class="out">

        </div>
    </div>


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
            @php
                $orderData = json_decode($orderSearch->orderData, true);
            @endphp
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
                        @if($orderSearch->paymentMethod != 0 || $orderSearch->paymentStatus != 0)
                            Оллата <b>{{ $paymentMethod[$orderSearch->paymentMethod] }}</b>
                            статус <b>{{ $paymentStatus[$orderSearch->paymentStatus] }}</b>
                        @else
                            статус <b> не оформлен </b>
                            @isset($orderData['step'])
                                step -  <b>{{ $orderData['step'] }}</b>
                                <a class="button" href="{{ route('cart', ['lang' => $orderData['lang'], 'step' => 3, 'order_id' => $orderSearch->order_id]) }}" >
                                    проверить корзину
                                </a>
                            @endisset
                        @endif
                    <br>
                    @isset($orderData['step'])
                        step -  <b>{{ $orderData['step'] }}</b>
                        <br>
                    @endisset
                    дата опл  <b>{{ $orderSearch->paymentDate }}</b>
                    <br>
                    Инвойс <b>{{ $invoiceStatus[$orderSearch->invoiceStatus] }}</b>
                    <hr>
                    Сумма <b>{{ $orderSearch->orderPrice }}</b>
                    <br>

                </td>


                <td>
                    <span class="position-absolute text-small button show-hide fa fa-eye"></span>

                    Детали: <br>

                    <p>
                        <b>имя:</b> {{ $orderSearch->name }}
                        <a class="button" href="{{ route('client_data', ['client' => $orderSearch->clientId]) }}" > карточка клиента </a>
                        <br>
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
                    <span class="position-absolute text-small button show-hide fa fa-eye"></span>

                    <a class="hide button" href="{{ route('amo_create_invoice_to_order', ['order' => $orderSearch->id]) }}" >
                        add amo invoice to lead
                    </a>
                    <br>
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
                        <a class="button" href="{{ route('paypal_button', ['order_id' => $orderSearch->order_id]) }}" >
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

                    <br>
                    <a class="button" href="{{ route('api_order_view', [ 'order_id' => $orderSearch->order_id]) }}" >
                        распечатать заказ
                    </a> <br>
                    <div class="hide">
                        <a class="button" href="{{ route('cart', ['lang' => $orderData['lang'], 'step' => 3, 'order_id' => $orderSearch->order_id]) }}" >
                            проверить корзину
                        </a>
                    </div><br>
                    <div class="hide">
                        <a class="button" href="{{ route('get_order_data_to_amo', ['order' => $orderSearch->id]) }}" >
                            get order data to amo
                        </a>
                    </div>
                    <hr>
                    @if ($errors->any())
                        <h3>errors</h3>
                        <div class="alert alert-danger" style="color: brown;">
                            @foreach ($errors->all() as $error)
                                <p class="errors">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                </td>

            </tr>
        @endif


        @if(isset($orders))
            @foreach($orders as $item)
                @php
                    $orderData = json_decode($item->orderData, true);
                @endphp
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
                        @if(empty($item->amoId) && ($item->paymentMethod != 0 || $item->paymentStatus != 0))
                            <a class="button" href="{{ route('api_create_amo_order', ['id' => $item->order_id]) }}" >
                                api create amo lead
                            </a>
                        @else
                            Amo Id:  <a class="border-bottom" href="{{ 'https://takebreak.amocrm.ru/leads/detail/'.$item->amoId }}" target="_blank">
                                {{ $item->amoId }}
                            </a>
                        @endif
                        <br>
                            @if($item->paymentMethod != 0 || $item->paymentStatus != 0)
                                Оллата <b>{{ $paymentMethod[$item->paymentMethod] }}</b>
                                статус <b>{{ $paymentStatus[$item->paymentStatus] }}</b>
                            @else
                                статус <b> не оформлен </b>
                                @isset($orderData['step'])
                                    step -  <b>{{ $orderData['step'] }}</b>
                                    <a class="button" href="{{ route('cart', ['lang' => $orderData['lang'], 'step' => 3, 'order_id' => $item->order_id]) }}" >
                                        проверить корзину
                                    </a>
                                @endisset
                            @endif
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
                        <span class="position-absolute text-small button show-hide fa fa-eye"></span>

                        Детали: <br>



                        <p>
                            <b>имя:</b>{{ $item->name }}
                            <a class="button" href="{{ route('client_data', ['client' => $item->clientId]) }}" > карточка клиента </a>
                            <br>
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
                            <b>Скидка купон:
                            </b>
                                {{ $orderData['order_data']['discount']['code'] }} -
                                {{ $orderData['order_data']['discount']['text'] }}
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
                            <hr>
                            <hr>
                            @foreach($orderData as $k => $v)
                                @if(is_string($v) || empty($v))
                                    <p>{{ $k }} - {{ $v }}</p>
                                @else
                                    <hr>
                                    <p>{{ $k }} - @php(print_r($v))</p>
                                    <hr>
                                @endif
                            @endforeach
                        </div>

                    </td>


                    <td>
                        <span class="position-absolute text-small button show-hide fa fa-eye"></span>



                        @if ($item->paymentMethod == 1 && $item->paymentStatus != 4)
                            <hr>
                            <a class="hide button" href="{{ route('order_sheck_payment_status_icredit', ['orderId' => $item->order_id]) }}" >
                                check status
                            </a>
                        @endif

                        @if ($item->invoiceStatus == 0 && $item->paymentStatus != 0)
                            <div class="hide">
                                <br>
                                <a class="button" href="{{ route('invoice_create', ['orderId' => $item->order_id]) }}" >
                                    create invoice
                                </a> <br>
                            </div>
                        @endif
                        <a class="hide button" href="{{ route('delete_order', ['id' => $item->order_id]) }}" >delete</a><br>


                    @if($item->paymentStatus != 0)

                            <a class="hide button" href="{{ route('amo_create_invoice_to_order', ['order' => $item->id]) }}" >
                                add amo invoice to lead
                            </a>
                            <br>

                             <a class=" button" href="{{ route('orders_test_mail', ['id' => $item->order_id]) }}" >test mail</a><br>
                            <a class=" button" href="{{ route('orders_test_sendpulse', ['order' => $item->id]) }}" >test sendpulse</a>
                            <br>

                            <a class="hide button" href="{{ route('api_create_amo_order', ['id' => $item->order_id]) }}" >
                                api create amo lead
                            </a>

                            <div class="hide">
                                <br>
                                <a class="button" href="{{ route('test_get_url', [ 'order_id' => $item->id]) }}" >
                                    test Icredit
                                </a> <br>
                            </div>

                            <div class="hide">
                                <br>
                                <a class="button" href="{{ route('icredit_order_thanks', [ 'id' => $item->id]) }}" >
                                    test Icredit thanks
                                </a> <br>
                            </div>
                            <div class="hide">
                                <br>
                                <a class="button" href="{{ route('paypal_button', [ 'order_id' => $item->order_id]) }}" >
                                    test PayPal
                                </a> <br>
                            </div>
                            <br>
                            <a class="button" href="{{ route('api_order_view', [ 'order_id' => $item->order_id]) }}" >
                                распечатать заказ
                            </a> <br>
                            <div class="hide">
                                <a class="button" href="{{ route('change_order_id', [ 'id' => $item->id]) }}" >
                                    change order id
                                </a>
                            </div> <br>
                            <div class="hide">
                                <a class="button" href="{{ route('cart', ['lang' => $orderData['lang'], 'step' => 3, 'order_id' => $item->order_id]) }}" >
                                    проверить корзину
                                </a>
                            </div>
                        @endif
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


