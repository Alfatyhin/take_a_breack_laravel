@extends('layouts.master')

@section('title', 'orders')

@section('sidebar')
    @parent
@stop

@section('content')
    <div class="max-w-10xl mx-auto sm:px-10 lg:px-10">
        <p>
            общая сумма текущий месяц - ( {{ $priceMonth }} )
        </p>

        <p>
            общая сумма за весь год - ( {{ $priceYear }} )
        </p>
        <hr>
        <form method="get" action="{{ route('orders') }}" >
            start date: <input type="date" name="date-from" value="{{ $date_from->format('Y-m-d') }}">
            end date: <input type="date" name="date-to" value="{{ $date_to->format('Y-m-d') }}">
            <input class="button" type="submit" name="date_filter" value="установить даты">
        </form>
        <a class="button" href="{{ route('orders', ['dates' => 'today']) }}"> за сегодня </a>
        <a class="button" href="{{ route('orders', ['dates' => 'month']) }}"> за месяц </a>
        <hr>
        <p>
            отчет за период с {{ $date_from->format('Y-m-d') }} по {{ $date_to->format('Y-m-d') }} <br>
            @foreach($paydPeriodInfo as $key => $summ)
                &nbsp; &nbsp; &nbsp; {{ $key }} - {{ $summ }} <br>
            @endforeach
        </p>


        <form action="{{ route('orders') }}" method="get">
            <p>
               Search bu ecwid id <input name="order_id" type="text"
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
            <caption>test</caption>
            @if ($orderSearch)

                <tr>
                    <td>
                        ( {{ $orderSearch->id }} ) <b>#{{ $orderSearch->order_id }}</b>  <br>
                        Дата: {{ $orderSearch->created_at }} <br>
                        @if(empty($orderSearch->amoId))
                            <a class="button" href="{{ route('api_create_amo_order', ['id' => $item->order_id]) }}" >
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
                                {{ $orderSearch->orderData }}
                            </div>

                        @else

                            <p>
                                {{ $orderSearch->name }} __ {{ $orderSearch->email }}
                            </p>

                            <hr>
                            <div class="hide">
                                {{ $orderSearch->orderData }}
                            </div>
                        @endif

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
                           ( {{ $item->id }} ) <b>#{{ $item->order_id }}</b>  <br>
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
                           дата <b>{{ $item->paymentDate }}</b>
                           <br>
                           Инвойс <b>{{ $invoiceStatus[$item->invoiceStatus] }}</b>
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
                                   {{ $item->name }} __ {{ $item->email }}
                               </p>

                               <hr>
                               <div class="hide">
                                   {{ $item->orderData }}
                               </div>
                           @endif

                       </td>


                       <td>
                           <span class="position-absolute text-small button show-hide"></span>

                           <a class="hide button" href="{{ route('delete_order', ['id' => $item->order_id]) }}" >delete</a><br>
                           <a class="hide button" href="{{ route('orders_test_mail', ['id' => $item->order_id]) }}" >test mail</a>
                           <br>
                           <hr><br>
                           <a class="button" href="{{ route('google_add_delivery', ['id' => $item->order_id]) }}" >
                               google_add_delivery
                           </a>


{{--                           <a class="hide button" href="{{ route('amo.create.lead', ['id' => $item->order_id]) }}" >--}}
{{--                               new amo lead--}}
{{--                           </a> <br>--}}
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
                       </td>

                   </tr>
               @endforeach
           @endif
       </table>
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 paginate">
            {{ $orders->links() }}
        </div>
    </div>

@stop

