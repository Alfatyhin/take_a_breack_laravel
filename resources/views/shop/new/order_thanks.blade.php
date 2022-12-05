
@extends('shop.new.shop_master')



@if ($lang == 'ru')
    @php($title = "Доставка")
@else

    @php($title = "Delivery")
@endif

@section('title', $title)



@section('head')


@stop


@section('content')


    <div class="checkout__item thank">
        <h2>
            {{ __('shop-thanks.благодарим за ваш заказ') }}!
        </h2>
        <p>
            {{ __('shop-thanks.Заказ') }} <b> № {{ $order->order_id }}</b>
            {{ __('shop-thanks.на сумму') }} {{ $order->orderPrice }} ₪
            {{ __('shop-thanks.передан в обработку') }}. <br>
            {{ __('shop-thanks.Как только Ваш заказ будет готов') }}
        </p>
        @if ($order->paymentMethod == 4)
            <p>
                {{ __('shop-thanks.Для оплаты заказа через') }} <span>Bit</span>
                {{ __('shop-thanks.используйте номер телефона') }} <br>
                @include("shop.layouts.invoice.bit_phone_".$invoiceSettingData['invoice_mode_bit'])
            </p>
        @endif
        <div class="checkout__action">
            <button class="black-btn">
                <a href="{{ route("index", ['lang' => $lang]) }}"></a>
                {{ __('shop-thanks.перейти в каталог товаров') }}
            </button>
        </div>
    </div>

@stop



@section('scripts')

@stop



