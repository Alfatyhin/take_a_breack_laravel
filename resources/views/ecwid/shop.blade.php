
@extends('layouts.ecwid_shop')

@section('title', 'Shop')

@section('sidebar')
    @parent

@stop

@section('content')

        <!-- about-us start -->
        <section class="about-us">
            <div class="container">
                <div class="about-us__wrap">
                    <div class="about-us__text">
                        <h2 class="about-us__title">
                            О нас
                        </h2>
                        <p>
                            Добро пожаловать в студию здоровых десертов Take a Break!
                        </p>
                        <p>
                            Место, где мы разделяем две главные семейные традиции - радость от приготовления пищи и здоровый образ жизни.
                        </p>
                        <p>
                            Мы обслуживаем торты, печенье, конфеты и плитки шоколада и доставляем их по адресам клиентов.
                        </p>
                        <p>
                            Наши восхитительные сладости ручной работы помогут вам не только хорошо выглядеть, но и чувствовать себя лучше
                        </p>
                    </div>
                    <div class="about-us__pictures">
                        <img src="image/circle-edge.png" alt="">
                        <img src="image/vegan-circle.png" alt="">
                        <img src="image/circle-dairy.png" alt="">
                        <img src="image/gluten-circle.png" alt="">
                    </div>
                </div>
            </div>
        </section>
        <!-- about-us end -->
        <!-- catalog start -->
        <section class="catalog">
            <div class="catalog__title">
                <h2>
                    Наше меню <span><img src="image/vegan-icon.png" alt=""></span>
                </h2>
            </div>
            <div class="catalog__menu">
                <ul class="catalog__list">
                    <li><a class="link" data-goto=".catalog__cakes" href="#">Торты</a></li>
                    <li><a class="link" data-goto=".catalog__cupcakes" href="#">Капкейки</a></li>
                    <li><a class="link" data-goto=".catalog__sets" href="#">Наборы</a></li>
                    <li><a class="link" data-goto="" href="#">Открытки</a></li>
                </ul>
            </div>

            @if(!empty($productList))
                @foreach($productList as $item)
                    <div class="catalog__{{ $item['description']['name'] }}">

                        <h3 class="{{ $item['description']['name'] }}__title subcatalog-title">
                            {{ $item['description']['nameTranslated']['ru'] }}
                        </h3>

                        <div class="{{ $item['description']['name'] }}__subcatalog subcatalog container">
                            @foreach($item['catalog']['items'] as $product)
                                @if($product['enabled'])

                                    <div class="product">
                                        <a href="{{ $product['url'] }}">
                                            <div class="product__img">
                                                <picture>
                                                    <source srcset="{{ $product['smallThumbnailUrl'] }}" media="(max-width: 800px)">
                                                    <img src="{{ $product['thumbnailUrl'] }}"  alt="{{ $product['name'] }}" width="100%">
                                                </picture>
                                            </div>
                                        </a>

                                        <div class="product__name">
                                            @if (!empty($product['nameTranslated']['ru']))
                                                {{ $product['nameTranslated']['ru'] }}
                                            @else
                                                {{ $product['name'] }}
                                            @endif
                                        </div>
                                        <div class="product__price">
                                            <span>{{ $product['price'] }}</span>₪
                                        </div>
                                        <button class="product__button" type="button" data="{{ $product['id'] }}">Купить</button>
                                    </div>

                                @endif
                            @endforeach
                        </div>

                    </div>
                @endforeach
            @endif


        </section>
        <!-- catalog end -->
        <!-- footer-nav start -->
        <div class="catalog__menu footer_nav">
            <ul class="catalog__list">
                <li><a class="link" data-goto=".catalog__cakes" href="#">Торты</a></li>
                <li><a class="link" data-goto=".catalog__cupcakes" href="#">Капкейки</a></li>
                <li><a class="link" data-goto=".catalog__sets" href="#">Наборы</a></li>
                <li><a class="link" data-goto="" href="#">Открытки</a></li>
            </ul>
        </div>
        <!-- footer-nav end -->
        <!-- footer start -->
        <footer class="footer">
            <div class="container">
                <div class="footer__notice">
                    <p>Для встречи, пожалуйста, позвоните за 15 минут до прибытия.</p>
                </div>
                <div class="footer__info">
                    <div class="footer__worktime">
                        <h3>Часы работы</h3>
                        <ul class="shedule">
                            <li>Воскресенье-четверг 10:00-20:00</li>
                            <li>Пятница 10:00-16:00</li>
                            <li>Суббота Выходной</li>
                        </ul>
                    </div>
                    <div class="footer__contacts">
                        <h3>Контакты</h3>
                        <ul class="contacts">
                            <li><a href="tel:972559475812"><span>+</span>972559475812</a> </li>
                            <li><a href="mailto:info@takeabreak.co.il">info@takeabreak.co.il</a> </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
        <!-- footer end -->
        <script src="{{ asset('js/ecwid-shop.js') }}" defer></script>

    <script>
        var products = @json($productList);
        console.log(products);
    </script>

@stop

