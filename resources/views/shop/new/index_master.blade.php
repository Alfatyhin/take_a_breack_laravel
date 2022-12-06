
@extends('shop.new.shop_master')

@section('title')

    @if ($lang == 'ru')
        Интернет-магазин авторских десертов Take a Break
    @else
        Online store of author's desserts Take a Break
    @endif

@stop

@section('head')


    <link rel="canonical" href="{{ route("index") }}">

    <link rel="alternate" hreflang="ru" href="{{ route('index', ["lang" => 'ru']) }}">
    <link rel="alternate" hreflang="en" href="{{ route('index') }}">

    @if ($lang == 'ru')
        <meta name="description" content="Главная в интернет-магазине Take a Break 🧁 Натуральные авторские десерты ✈ Быстрая доставка в Гуш-Дан, Нетанию, Ашдод, Хайфу и Иерусалим ☎ +972 55-947-5812">
    @else
        <meta name="description" content="Home in the online store Take a Break 🧁 Natural author's desserts ✈ Fast delivery to Gush Dan, Netanya, Ashdod, Haifa and Jerusalem ☎ +972 55-947-5812">
    @endif

{{--    @include('shop.layouts.seo.re_captcha')--}}
@stop


@section('product_filter')
    @include('shop.new.layouts.products_filters')
@stop


@section('content')

    @include("shop.new.layouts.left_sidebar")
    @include("shop.new.layouts.index")

    <div class="hidden" style="display: none" itemscope itemtype="https://schema.org/Organization">
        <a itemprop="url" href="{{ route('index') }}"><div itemprop="name">Take a Break</div>
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

@stop

@section('scripts')

@stop

