

@extends('shop.shop-master')

@section('title', 'Wholesale offer')

@section('head')


    <link rel="stylesheet" href="{{ asset('css/wholesaleOffer_adaptation.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/wholesaleOffer.css') }}?{{ $v }}">


@stop

@section('content')
    <section class="wholesale">
        <div class="container">
            <div class="wholesale__body"><a class="wholesale__btnBack btnBack" href="{{ route('index_'.$lang) }}#anchor-profitably"></a>
                <h1 class="wholesale__title blockTitle">Wholesale offer</h1>
                <div class="wholesale__content"><img class="wholesale__img" src="img/common/wholesale.jpg" alt="image">
                    <div class="wholesale__text">
                        <div class="wholesale__subtitle">Our store invites wholesale and small wholesale buyers to cooperation. If you plan to regularly place orders in our store for amounts from 1500₪ and want to save your time and money at the same time, then our offer is for you!</div>
                        <div class="wholesale__offer">
                            <div class="wholesale__offerTitle">We are ready to offer you:</div>
                            <div class="wholesale__offerItem blockText">
                                <p><span>Flexible system of individual discounts</span> - we will offer you favorable terms of cooperation and a discount on your first order.
                                </p>
                            </div>
                            <div class="wholesale__offerItem blockText">
                                <p><span>Free shipping orders</span> - We will deliver your order for free with an order amount of 1500₪.
                                </p>
                            </div>
                            <div class="wholesale__offerItem blockText">
                                <p><span>Personal consultation</span> - we are ready to help you in solving your issue with an individual approach.
                                </p>
                            </div>
                            <div class="wholesale__offerItem blockText">
                                <p><span>Long-term cooperation</span> – we are already cooperating with coffee shops, restaurants and health food stores, offices that arrange “happy hours” for their employees, and also make “sweet tables” to order. We will be glad to become your business partners!
                                </p>
                            </div>
                        </div>
                        <div class="wholesale__contact">
                            <div class="wholesale__contactTitle">Write to us and we will discuss the conditions:</div>
                            <div class="wholesale__contactItem">
                                <p><span>WhatsApp</span> <a class="hoverUnderline" href="https://wa.me/9720559475812" target="_blank">0559475812</a>
                                </p>
                            </div>
                            <div class="wholesale__contactItem">
                                <p><span>Email</span> <a class="hoverUnderline" href="mailto:info@takeabreak.co.il">info@takeabreak.co.il</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop


@section('scripts')
    <script src="{{ asset('js/wholesaleOffer.js') }}?{{ $v }}" defer></script>
@stop
