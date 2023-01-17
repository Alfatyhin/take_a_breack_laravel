
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
                Привет, на сайте при оформлении заказа произошел сбой, но всё хорошо,
                мы увидели ваш заказ и перезвоним вам в течение дня.
                Если заказ срочный, свяжитесь с нами по WhatsApp для подтверждения заказа, пожалуйста.
            </p>


            <p>
                Hello, there was an error on the site when placing an order, but everything is fine,
                we saw your order and will call you back within a day.
                If the order is urgent, please contact us via whatsapp to confirm the order.
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



