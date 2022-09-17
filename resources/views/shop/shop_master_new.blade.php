<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>

    <meta charset="UTF-8">
    <title>
        @section('title')

        @show
    </title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}?{{ $v }}">

    @if($noindex)
        <meta name="robots" content="noindex, follow" />
    @endif

    <script>
        var general_url = "{{ route('index_'.$lang) }}";
        console.log('cart v-{{ $v }}');
    </script>
    @section('head')

    @show

    @include('shop.layouts.seo.head-scripts')
</head>
<body>
@include('shop.layouts.seo.body_top')

<div class="wrapper">
    <div class="content">
        <div class="modal"></div>
        <header class="header">
            <div class="container">
                <div class="header__body">
                    <div class="header__mobile-btn">
                        <button class="menu-btn">
                            <span></span>
                        </button>
                    </div>
                    <div class="header__login">
                        <a href="#"><img src="/assets/images/icons/bag.svg" alt=""></a>
                        <a href="#"><img src="/assets/images/icons/user.svg" alt=""></a>
                    </div>
                    <div class="header__title">
                        <a href="#">
                            <img src="/assets/images/logo.png" alt="">
                        </a>
                    </div>
                    <nav class="header__nav">
                        <ul class="header__row header-top">
                            <li>
                                <a href="#">
                                    О нас
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Доставка и Оплата
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Отзывы
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Контакты
                                </a>
                            </li>
                        </ul>
{{--                        <ul class="header__row header-bottom">--}}
{{--                            <li>--}}
{{--                                <a class="active" href="#">--}}
{{--                                    Все--}}
{{--                                </a>--}}
{{--                            </li>--}}
{{--                            <li>--}}
{{--                                <a href="#">--}}
{{--                                    В наличии--}}
{{--                                </a>--}}
{{--                            </li>--}}
{{--                            <li>--}}
{{--                                <a href="#">--}}
{{--                                    Под заказ--}}
{{--                                </a>--}}
{{--                            </li>--}}
{{--                        </ul>--}}
                    </nav>
                </div>
            </div>
        </header>
        <main class="main">
            <div class="container">
                <div class="main__wrap">
                    <div class="category">
                        <button class="close-menu-btn"></button>
                        <p>
                            <img src="/assets/images/icons/category.svg" alt="">
                        </p>
                        <ul>
                            @foreach($categories as $category)
                                @php($translate = json_decode($category->translate, true))
                                <li>
                                    <a href="{{ route("category_$lang", ['category' => $category->slag]) }}"
                                       data-type="{{ $category->slag }}">
                                        @isset($translate['nameTranslated'][$lang])
                                            {{ $translate['nameTranslated'][$lang] }}
                                        @else
                                            {{ $category->name }}
                                        @endisset
                                    </a>
                                </li>
                            @endforeach

                        </ul>
                        <ul class="for-mobile">
                            <li>
                                <a href="#">
                                    О нас
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Доставка и Оплата
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Отзывы
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Контакты
                                </a>
                            </li>
                        </ul>
                    </div>
                    @section('content')

                    @show

                </div>
            </div>
        </main>
        <footer class="footer">
            <div class="container">
                <div class="footer__body">
                    <div class="footer__row">
                        <p>
                            Израиль, <br>
                            Emanuel Ringelblum 3, Holon
                        </p>
                        <a href="#">+972 055-947-5812</a>
                    </div>
                    <div class="footer__row">
                        <p>
                            © 2022 Design by GSaga. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <div class="mark">
        <div>
            <a href="#" class="mark-link">
                <img src="/assets/images/icons/bag.svg" alt="">
            </a>
            <a href="#" class="mark-link">
                <img src="/assets/images/icons/user.svg" alt="">
            </a>
            <a class="social-link" href="#">
                Facebook
            </a>
            <a class="social-link" href="#">
                Instagram
            </a>
        </div>
    </div>
</div>
@section('scripts')

@show
<script src="{{ asset('/scripts/app.js') }}?{{ $v }}" defer></script>

@include('shop.layouts.seo.footer-scripts')
@include("shop.$lang.popap_sendpulse")
</body>
</html>
