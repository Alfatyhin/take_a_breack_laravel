
@extends('shop.shop-master')

@php($translate = json_decode($category->translate, true))

@if (!empty($translate['nameTranslated'][$lang]))
    @php($category_name = $translate['nameTranslated'][$lang])
@else
    @php($category_name = $category->name)
@endif

@if ($lang == 'ru')
    @isset($category->data['seo']['title'][$lang])
        @php($title = $category->data['seo']['title'][$lang])
    @else
        @php($title = "Купить $category_name по лучшим ценам с доставкой ✈ в Гуш-Дан, Нетанию, Ашдод, Хайфу и Иерусалим")
    @endisset
    @php($general = 'Главная')
@else
    @isset($category->data['seo']['title'][$lang])
        @php($title = $category->data['seo']['title'][$lang])
    @else
        @php($title = "Buy $category_name at the best prices with delivery ✈ to Gush Dan, Netanya, Ashdod, Haifa and Jerusalem")
    @endisset
    @php($general = 'Home')
@endif

@section('title', $title)



@section('head')

    <link rel="stylesheet" href="{{ asset('css/areasAndPrices.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/areasAndPrices_adaptation.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/index_adaptation.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/index-0.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/category.css') }}?{{ $v }}">

    <link rel="canonical" href="{{ route("category_$lang", ['category' => $category->slag]) }}">


    <link rel="alternate" hreflang="ru" href="{{ route("category_ru", ['category' => $category->slag]) }}">
    <link rel="alternate" hreflang="en" href="{{ route("category_en", ['category' => $category->slag]) }}">

    @isset($category->data['seo']['description'][$lang])
        <meta name="description" content="{{ $category->data['seo']['description'][$lang] }}">
    @else
        <meta name="description" content="{{ $category_name }} 🧁 buy in the online store Take a Break: prices, reviews, composition - delivery to Gush Dan, Netanya, Ashdod, Haifa and Jerusalem ☎ +972 55-947-581">
    @endisset

    @isset($category->data['seo']['keywords'][$lang])
        <meta name="Keywords" content="{{ $category->data['seo']['keywords'][$lang] }}">
     @endisset

    <style>
        section {
            margin-bottom: 100px;
        }
        .shortBlock.close {
            max-height: 37rem;
        }
        .shortBlock {
            overflow: hidden;
            position: relative;
            padding-bottom: 5rem;
        }
        .openingShortBlock {
            position: absolute;
            bottom: 0;
            width: 98%;
            height: 30px;
            background: #ddd;
            border-radius: 5px;
            cursor: pointer;
            color: #ad7d80;
        }
        .shortBlock.open .openingShortBlock::after {
            content: ""; display: block;
            margin: auto;
            width: 15px;
            height: 15px;
            border-top: 2px solid;
            border-right: 2px solid;
            transform: rotate(-45deg);
            position: relative;
            top: 10px;
        }
        .shortBlock.close .openingShortBlock::after {
            content: "";
            display: block;
            margin: auto;
            width: 15px;
            height: 15px;
            border-bottom: 2px solid;
            border-right: 2px solid;
            transform: rotate(45deg);
            position: relative;
            top: 5px;
        }
        .openingShortBlock::before {
            content: "";
            display: block;
            margin: auto;
            width: 30px;
            height: 30px;
            position: absolute;
            left: 50%;
            transform: translate(-50%, 0);
            border: 1px solid;
            border-radius: 50%;
        }
        h2.blockTitle {
            text-align: center;
        }
    </style>

@stop

@section('content')

    @php($translate = json_decode($category->translate, true))


    <section class="select" id="anchor-select">
        <div class="container">

            <nav class="breadcrumbs"  itemscope itemtype="http://schema.org/BreadcrumbList">
                <span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <a itemprop="item" href="/">{{ $general }}</a>
                    <meta itemprop="name" content="{{ $general }}" />
                    <meta itemprop="position" content="1" />
                </span>
{{--                <span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">--}}
{{--                    <a itemprop="item" href="/url/">--}}
{{--                        {{ $category_name }}--}}
{{--                    </a>--}}
{{--                    <meta itemprop="name" content="{{ $category_name }}f" />--}}
{{--                    <meta itemprop="position" content="2" />--}}
{{--                </span>--}}
            </nav>

            <div class="select__body">
                <h2 class="select__title blockTitle">

                    @if (!empty($translate['nameTranslated'][$lang]))
                        {{ $translate['nameTranslated'][$lang] }}
                    @else
                        {{ $category->name }}
                    @endif
                </h2>
                <div class="select__header">
                    @include("shop.layouts.index_categories_products_box")
                </div>
                <div class="select__slider">
                    @include("shop.layouts.index_products_box")
                </div>
                <div class="select__sliderPagWrapper">
                    <div class="select__sliderPagination1 sliderPagination"></div>
                </div>
            </div>
        </div>
    </section>

    @isset($translate['descriptionTranslated'][$lang])
        @php($size = strlen($translate['descriptionTranslated'][$lang]))
        <section class="description ">
            <div class="container blockText @if($size > 1200) shortBlock close @endif">
                    {!! $translate['descriptionTranslated'][$lang] !!}
                @if($size > 1500)
                    <div class="openingShortBlock"></div>
                @endif
            </div>
        </section>
    @endisset

    @include("shop.$lang.anchor-advantage")
    @include("shop.$lang.master_popap")

@stop


@section('scripts')
    <script src="{{ asset('js/index.js') }}?{{ $v }}" defer></script>
    <script src="{{ asset('js/category.js') }}?{{ $v }}" defer></script>
@stop

