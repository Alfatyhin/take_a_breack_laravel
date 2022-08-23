

@extends('shop.shop-order_thanks-master')

@section('title', 'Спасибо за заказ')

@section('head')


@stop

@section('content')

    @if ($order)
        <div class="payOrder__body  payOrder__body--money">
            <div class="payOrder__wrapper">
                <div class="payOrder__title">
                    <p>
                        We have received your order №<span class='payOrder__number'>{{ $order->order_id }}</span>
                        for the amount <span class='payOrder__total'>{{ $order->orderPrice }}</span><span class='payOrder__unit'>₪</span>

                    </p>
                </div>
                <div class="payOrder__text">
                    @isset($message)
                        @foreach($message as $mess)
                            <p> {{ $mess }} </p>
                        @endforeach
                    @endif
                    @if ($order->paymentMethod == 4)
                            <p>To pay for an order via <span>Bit</span> use phone number <br>
                                @include("shop.layouts.invoice.bit_phone_".$invoiceSettingData['invoice_mode_bit'])
                                or go back </p>
                    @endif
                </div>
                <div class="payOrder__text">
                    <a class="blockBtn blockBtn--bgc" href="{{ route("index_$lang") }}"> go back </a>
                </div>
            </div>
    @endif

@stop


@section('scripts')

@stop

