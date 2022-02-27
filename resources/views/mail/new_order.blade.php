

@extends('layouts.mail')

@section('title', 'new order')

@section('sidebar')
    @parent
@stop

@section('content')

    <h1>#{{ $order->order_id }}</h1>

    <div class="lang_{{ $order->orderData['option']['lang'] }}">

        @foreach($order->orderData['Cart']['items'] as $item)
            @if (!empty($item['nameTranslated'][$order->orderData['option']['lang']]))
                <p>{{ $item['nameTranslated'][$order->orderData['option']['lang']] }} {{ $item['info'] ?? ' '}} x {{ $item['count'] }}</p>
            @else
                <p>{{ $item['name'] }} {{ $item['info'] ?? ' '}} x {{ $item['count'] }}</p>
            @endif


        @endforeach

        @if($order->orderData['option']['delivery_method'] == 'delivery')
            <p>
                Delivery address -
                {{ $order->orderData['Cart']['person']['address']['city'] }}
                {{ $order->orderData['Cart']['person']['address']['street'] }}
                {{ $order->orderData['Cart']['person']['address']['house_number'] }}

                @if(!empty($order->orderData['Cart']['person']['address']['room_number']))
                    {{ $order->orderData['Cart']['person']['address']['room_number'] }}
                @endif
            </p>

            <p>
                Date delivery: {{ $order->orderData['option']['delivery_date'] }} time: {{ $order->orderData['option']['delivery_time'] }}
            </p>
        @else
            <div class="pre">
                {{ $shop_setting['pickup']['note'][$order->orderData['option']['lang']] }}
                <hr>
                {{ $shop_setting['pickup']['info'][$order->orderData['option']['lang']] }}
            </div>
        @endif
    </div>

    <hr>
    <div class="right">
        @if($order->orderData['option']['delivery_price'] > 0)
            <p>Delivery price: {{ $order->orderData['option']['delivery_price'] }}₪</p>
        @endif

        @if($order->orderData['Cart']['discount'] != 'false')
            <p>Discount: {{ $order->orderData['Cart']['discount']['display'] }}</p>
        @endif
        @if(!empty($order->orderData['option']['tips_price']))
            <p>Tips: {{ $order->orderData['option']['tips_price'] }}%</p>
        @endif

        <p>Total price: {{ $order->orderData['option']['total_price'] }}₪ </p>
    </div>


    @if($order->orderData['option']['payment_method'] == 'Сash payment' )
        <hr>

        <div class="pre">
            {{ $shop_setting['cash_payment']['note'][$order->orderData['option']['lang']] }}
        </div>

        <hr>
    @endif

@stop

