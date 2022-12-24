
@extends('shop.new.shop_master')



@section('title', 'order not found')



@section('head')

<script>
    // localStorage.removeItem("cart");
    // let client_data = JSON.parse(localStorage.getItem("client_data") || "[]");
    // client_data.order_id = '';
    // localStorage.setItem("client_data", JSON.stringify(client_data));
    localStorage.removeItem("client_data");
</script>
@stop


@section('content')




    <div class="checkout__item thank">
        <h2>
            Order {{ $order_id }} not Found!
        </h2>

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



