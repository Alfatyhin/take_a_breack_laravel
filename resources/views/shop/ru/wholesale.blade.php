

@extends('shop.shop-master')

@section('title', 'Оптовое предложение')

@section('head')


    <link rel="stylesheet" href="{{ asset('css/wholesaleOffer_adaptation.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/wholesaleOffer.css') }}?{{ $v }}">


@stop

@section('content')
    <section class="wholesale">
        <div class="container">
            <div class="wholesale__body"><a class="wholesale__btnBack btnBack" href="{{route('index_'.$lang)}}#anchor-profitably"></a>
                <h1 class="wholesale__title blockTitle">Оптовое предложение</h1>
                <div class="wholesale__content"><img class="wholesale__img" src="/img/common/wholesale.jpg" alt="image">
                    <div class="wholesale__text">
                        <div class="wholesale__subtitle">Наш магазин приглашает к сотрудничеству оптовых и мелкооптовых покупателей. Если вы планируете регулярное оформление заказов в нашем магазине на суммы от 1500₪ и хотите при этом сэкономить ваши время и деньги, то наше предложение для вас!</div>
                        <div class="wholesale__offer">
                            <div class="wholesale__offerTitle">Мы готовы вам предложить:</div>
                            <div class="wholesale__offerItem blockText">
                                <p><span>Гибкую систему индивидуальных скидок</span> - мы предложим вам выгодные условия сотрудничества и начисление скидки с вашего первого заказа.
                                </p>
                            </div>
                            <div class="wholesale__offerItem blockText">
                                <p><span>Бесплатную доставку заказов</span> - доставим ваш заказ бесплатно при сумме заказа от 1500₪.
                                </p>
                            </div>
                            <div class="wholesale__offerItem blockText">
                                <p><span>Персональную консультацию</span> - готовы вам помочь в решении вашего вопроса с индивидуальным подходом.
                                </p>
                            </div>
                            <div class="wholesale__offerItem blockText">
                                <p><span>Долгосрочное сотрудничество</span> – мы уже сотрудничаем с кофейнями, ресторанами и магазинами правильного питания, офисами, которые устраивают “счастливые часы” для своих сотрудников, а также делаем “сладкие столы” под заказ. Будем рады стать и вашими партнёрами по бизнесу!
                                </p>
                            </div>
                        </div>
                        <div class="wholesale__contact">
                            <div class="wholesale__contactTitle">Напишите нам и мы обсудим условия:</div>
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
