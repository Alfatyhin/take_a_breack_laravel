
@extends('shop.new.shop_master')



@section('title', 'shop error')



@section('head')

<script>
    localStorage.removeItem("cart");
    localStorage.removeItem("client_data");
</script>
@stop


@section('content')




    <div class="checkout__item thank">
        <h2>
            {!! $message !!}
        </h2>

        <div class="checkout__action">
            <p>
                {{ __('shop.Привет, на сайте при оформлении заказа произошел сбой') }}
            </p>

            <div class="checkout__action">
                <button class="black-btn">
                    <a href="https://wa.me/9720559475812"></a>
                    +972 055-947-5812
                </button>
            </div>

        </div>
    </div>

@stop



@section('scripts')

@stop



