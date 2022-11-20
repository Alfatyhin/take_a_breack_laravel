
@extends('shop.new.shop_master')

@section('title', 'cart')


@section('head')

@parent

<link rel="stylesheet" href="{{ asset('/dist/intlTelInput.css') }}?{{ $v }}">
<script src="{{ asset('/scripts/jquery.maskedinput.js') }}?{{ $v }}" defer></script>

    <script>
        var delivery = @json($delivery);
        console.log('delivery');
        console.log(delivery);
        var cityes = @json($cityes);
        console.log('cityes');
        console.log(cityes);
        var shop_setting = @json($shop_setting);
        console.log('shop_setting');
        console.log(shop_setting);
    </script>

@stop

@section('content')

    @if($errors->any())

    @endif

    @include("shop.new.layouts.cart.cart-step_$step")

    @include("shop.new.layouts.cart.cart-products_box")

@stop


@section('scripts')
    <script src="{{ asset('/assets/libs/mask-lib.js') }}?{{ $v }}" defer></script>
    <script src="{{ asset('js/calendar.js') }}?{{ $v }}" defer></script>

@stop


