
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
                Привет, на сайте при оформлении произошел сбой.
                Мы увидели ваш заказ. Свяжитесь с нами по
                <a href="https://wa.me/9720559475812">
                    WhatsApp
                </a>
                +972 055-947-5812
                если это срочно и сообщите номер ошибки. Или наш менеджер свяжется с вами в течение дня
            </p>


            <p>
                Hello, there was an error on the site during checkout.
                We have seen your order. Contact us at
                <a href="https://wa.me/9720559475812">
                    WhatsApp
                </a>
                +972 055-947-5812
                if it's urgent and give the error number. Or our manager will contact you during the day
            </p>

        </div>
    </div>

@stop



@section('scripts')

@stop



