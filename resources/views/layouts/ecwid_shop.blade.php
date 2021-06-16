
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">

    <title>{{ config('app.name', 'Laravel') }} @yield('title')</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/normalize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.5.1.min.js') }}" defer></script>
</head>
<body class="antialiased min-h-screen">
@section('sidebar')
    <div class="wrapper">
        <!-- header start -->
        <header class="header header__order" style="background-image: url(image/background.png);">
            <div class="container">
                <nav class="header__menu">
                    <button class="header__burger">
                        <span></span>
                    </button>
                    <ul class="header__list">
                        <li class="header__link">
                            <a href="#">Categories</a>
                            <ul class="header__sublist">
                                <li><a class="link" data-goto=".catalog__cakes" href="#">Торты</a></li>
                                <li><a class="link" data-goto=".catalog__cupcakes" href="#">Капкейки</a></li>
                                <li><a class="link" data-goto=".catalog__sets" href="#">Наборы</a></li>
                                <li><a class="link" data-goto="" href="#">Открытки</a></li>
                            </ul>
                        </li>
                        <li class="header__link"><a class="link" data-goto=".about-us" href="#">About Us</a></li>
                        <li class="header__link"><a class="link" data-goto=".footer" href="#">Location</a></li>
                        <li class="header__link"><a class="link" data-goto=".footer" href="#">Contact</a></li>
                        <li class="header__link">
                            <a href="#">EN</a>
                            <ul class="header__sublist">
                                <li><a href="#">RU</a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
                <div class="header__page">
                    <div class="header__text-block">
                        <div class="text-block__logo">
                            <img src="image/logo.png" alt="">
                        </div>
                        <h2 class="text-block__subtitle">
                            Доставка полезных десертов по Израилю
                        </h2>
                        <p class="text-block__description">
                            Наши десерты подойдут всем - кто хочет попробовать что нибудь вкусное и натуральное, наши десерты на 100% состоят из натуральных продуктов, просто выбери десерт и наслаждайся моментом.
                        </p>
                    </div>
                    <div class="header__cake-week">
                        <div class="cake-week__image">
                            <img src="image/cake.png" alt="">
                        </div>
                        <a href="#" class="cake-week__button">
                            Торт недели
                        </a>
                    </div>
                </div>
            </div>
        </header>
        <!-- header start -->
    </div>

@show

<div class="wrapper">
    @section('content')


    @show
</div>
</body>
</html>


