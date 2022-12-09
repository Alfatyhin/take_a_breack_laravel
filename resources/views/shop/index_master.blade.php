
@extends('shop.shop-master')

@section('title')

    @if ($lang == 'ru')
        Интернет-магазин авторских десертов Take a Break
    @else
        Online store of author's desserts Take a Break
    @endif

@stop

@section('head')

    <link rel="stylesheet" href="{{ asset('css/areasAndPrices.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/areasAndPrices_adaptation.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/index_adaptation.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/index-0.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/popup-cart.css') }}?{{ $v }}">
    <script src="lazyload.js"></script>


    <link rel="canonical" href="{{ route("index_$lang") }}">

    <link rel="alternate" hreflang="ru" href="{{ route('index_ru') }}">
    <link rel="alternate" hreflang="en" href="{{ route('index_en') }}">

    @if ($lang == 'ru')
        <meta name="description" content="Главная в интернет-магазине Take a Break 🧁 Натуральные авторские десерты ✈ Быстрая доставка в Гуш-Дан, Нетанию, Ашдод, Хайфу и Иерусалим ☎ +972 55-947-5812">
    @else
        <meta name="description" content="Home in the online store Take a Break 🧁 Natural author's desserts ✈ Fast delivery to Gush Dan, Netanya, Ashdod, Haifa and Jerusalem ☎ +972 55-947-5812">
    @endif

    @include('shop.layouts.seo.re_captcha')
@stop

@section('content')

    @include("shop.$lang.index")
    @include("shop.ru.new-cart")

    <div class="hidden" itemscope itemtype="https://schema.org/Organization">
        <a itemprop="url" href="{{ route('index_en') }}"><div itemprop="name">Take a Break</div>
        </a>
        @if ($lang == 'ru')
            <div itemprop="description">Главная в интернет-магазине Take a Break 🧁 Натуральные авторские десерты ✈ Быстрая доставка в Гуш-Дан, Нетанию, Ашдод, Хайфу и Иерусалим ☎ +972 55-947-5812</div>
        @else
            <div itemprop="description">Home in the online store Take a Break 🧁 Natural author's desserts ✈ Fast delivery to Gush Dan, Netanya, Ashdod, Haifa and Jerusalem ☎ +972 55-947-5812</div>

        @endif
        <div itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
            {{--            <span itemprop="postalCode">Индекс</span><br>--}}
            <span itemprop="streetAddress">Emanuel Ringelblum 3</span><br>
            <span itemprop="addressLocality">Holon</span><br>
            <span itemprop="addressCountry">Israel</span><br>
        </div>
        <img itemprop="logo" src="https://takeabreak.co.il/img/common/logo.webp" />
        ☎: <span itemprop="telephone">+972 55-947-5812</span>
        ✉: <span itemprop="email">info@takeabreak.co.il</span>
        <div itemscope itemtype="https://schema.org/LocalBusiness">
            <span itemprop="name">Take a Break</span>
            <link itemprop="image" href="/img/common/logo.webp" />
            @if ($lang == 'ru')
                <time itemprop="openingHours" datetime="Su-Th 10:00-20:00">Вс.-Чт.: 9:00-21:00</time>
                <time itemprop="openingHours" datetime="Fr 10:00-16:00">Пт.-.: 10:00-16:00</time>
            @else
                <time itemprop="openingHours" datetime="Su-Th 10:00-20:00">Su-Th: 10:00-20:00</time>
                <time itemprop="openingHours" datetime="Fr 10:00-16:00">Fr: 10:00-16:00</time>
            @endif
            <meta itemprop="priceRange" content="ILS" />
            <span itemprop="telephone">+972 55-947-5812</span>
            <div itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                {{--<span itemprop="postalCode">Индекс</span><br>--}}
                <span itemprop="streetAddress">Emanuel Ringelblum 3</span><br>
                <span itemprop="addressLocality">Holon</span><br>
                <span itemprop="addressCountry">Israel</span><br>
            </div>
        </div>
    </div>


    <div class="preloader"></div>
@stop

@section('scripts')
    <script src="{{ asset('js/index.js') }}?{{ $v }}" defer></script>
    <script src="{{ asset('js/cart.js') }}?{{ $v }}" defer></script>
    <script src="{{ asset('js/calendar.js') }}?{{ $v }}" defer></script>

<noscript>

	<style>
		img[data-src] {
			display: none !important;
		}
	</style>

</noscript>

<script>

	let images = document.querySelectorAll("img");

	lazyload(images);

</script>
@stop

