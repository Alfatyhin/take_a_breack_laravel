
@extends('shop.new.shop_master')

@section('title', 'cart')


@section('head')

    <link rel="stylesheet" href="{{ asset('/css/cart-new.css') }}?{{ $v }}">
@parent

<link rel="stylesheet" href="{{ asset('/dist/intlTelInput.css') }}?{{ $v }}">
<script src="{{ asset('/scripts/jquery.maskedinput.min.js') }}?{{ $v }}" defer></script>

    <script>
        var delivery = @json($delivery);
        console.log('delivery');
        console.log(delivery);
        var cityes = @json($cityes);
        console.log('cityes');
        console.log(cityes);
        var shop_setting = {!! $shop_setting !!};
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

@section('popup')
    <p>test</p>
@stop




@section('scripts')
    <script>
        if(typeof ga !== 'undefined') {
            ga(function(tracker) {
                var clientId = tracker.get('clientId');
                $("input[name='gClientId']").val(clientId);
            });
        } else {
            console.log('no function');
        }
    </script>
    <script src="{{ asset('/assets/libs/mask-lib.js') }}?{{ $v }}" defer></script>
    @if ($step == 2)
        <script src="{{ asset('js/calendar.js') }}?{{ $v }}" defer></script>
    @endif
@stop


