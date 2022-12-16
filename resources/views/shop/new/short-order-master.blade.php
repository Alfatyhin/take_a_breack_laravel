
@extends('shop.new.shop_master_short_order')

@section('title', 'cart')


@section('head')

    <link rel="stylesheet" href="{{ asset('/css/cart-new.css') }}?{{ $v }}">
@parent

<link rel="stylesheet" href="{{ asset('/dist/intlTelInput.css') }}?{{ $v }}">
<script src="{{ asset('/scripts/jquery.maskedinput.min.js') }}?{{ $v }}" defer></script>

@stop

@section('content')

    @if($errors->any())

    @endif

    @include("shop.new.layouts.cart.cart-short_order")

@stop

@section('popup')
    <div class="shop-thanks">
        <h3 class="shop-thanks">{{ __('shop-thanks.ваш заказ оформлен') }}</h3>
        <p class="shop-thanks">{{ __('shop-thanks.Ожидайте подтверждение заказа!') }}</p>
    </div>
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

        // чаевые
        $('.pay-tips label').click(function () {
            changeSumm();
        });

        $("input[name='order_price']").change(function () {
            changeSumm();
        });
        
        function changeSumm() {
            var tips = $("input[name='premium']:checked").val();
            var order_summ = $("input[name='order_price']").val() / 1;

            var total = order_summ + order_summ * (tips / 100);=
            $('.total_order_price').text(total.toFixed(1));
        }

    </script>
    <script src="{{ asset('/assets/libs/mask-lib.js') }}?{{ $v }}" defer></script>

@stop


