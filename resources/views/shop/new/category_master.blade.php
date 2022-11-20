
@extends('shop.new.shop_master')


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


    <link rel="canonical" href="{{ route("category_index", ['lang' => 'en', 'category' => $category->slag]) }}">


    <link rel="alternate" hreflang="ru" href="{{ route("category", ['lang' => 'ru', 'category' => $category->slag]) }}">
    <link rel="alternate" hreflang="en" href="{{ route("category_index", ['category' => $category->slag]) }}">

    @isset($category->data['seo']['description'][$lang])
        <meta name="description" content="{{ $category->data['seo']['description'][$lang] }}">
    @else
        <meta name="description" content="{{ $category_name }} 🧁 buy in the online store Take a Break: prices, reviews, composition - delivery to Gush Dan, Netanya, Ashdod, Haifa and Jerusalem ☎ +972 55-947-581">
    @endisset

    @isset($category->data['seo']['keywords'][$lang])
        <meta name="Keywords" content="{{ $category->data['seo']['keywords'][$lang] }}">
    @endisset


@stop

@section('product_filter')
    @include('shop.new.layouts.products_filters')
@stop

@section('content')

    @include("shop.new.layouts.left_sidebar")

    @php($translate = json_decode($category->translate, true))

    @include("shop.new.layouts.category_products")

@stop
@section('content_2')

    @isset($translate['descriptionTranslated'][$lang])
        @php($size = strlen($translate['descriptionTranslated'][$lang]))
        <div class="category-about">
            <div class="category-about__col">
                {!! $translate['descriptionTranslated'][$lang] !!}
            </div>
            <div class="btn-more">
                <button class="trans-btn open-text">Читать больше</button>
            </div>
        </div>
    @endisset


@stop


@section('scripts')
@stop


