

@extends('shop.shop-order_thanks-master')

@section('title', 'Спасибо за заказ')

@section('head')

@stop

@section('content')

    <div class="payOrder__body  payOrder__body--money">
        <div class="payOrder__wrapper">
            <div class="payOrder__title">
                <p>
                    Мы получили ваш заказ №<span class='payOrder__number'>{{ $order->order_id }}</span>
                    на сумму <span class='payOrder__total'>{{ $order->orderPrice }}</span><span class='payOrder__unit'>₪</span>
                </p>
            </div>
            <div class="payOrder__text">
                @isset($message)
                    @foreach($message as $mess)
                        <p> {{ $mess }} </p>
                    @endforeach
                @endif
                @if ($order->paymentMethod == 4)
                    <p>Для оплаты заказа через <span>Bit</span> используйте номер телефона <br>
                        @include("shop.layouts.invoice.bit_phone_".$invoiceSettingData['invoice_mode_bit'])
                        или вернитесь обратно</p>
                @endif
            </div>
            <div class="payOrder__text">
                <a class="blockBtn blockBtn--bgc" href="{{ route("index_$lang") }}">вернуться на сайт</a>
            </div>
        </div>
    </div>

@stop


@section('scripts')

@stop

