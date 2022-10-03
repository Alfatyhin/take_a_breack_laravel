
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


@stop

@section('content')

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
{{--    <div class="category-about">--}}
{{--        <div class="category-about__col">--}}
{{--            <h2>--}}
{{--                Пастила веганская--}}
{{--            </h2>--}}
{{--            <p>--}}
{{--                Веганская пастила — это превосходное лакомство, содержащее огромное количество витаминов и полезных веществ. Вас покорят нотки сладости, непревзойденный аромат и отличный вкус.--}}
{{--            </p>--}}
{{--            <h3>--}}
{{--                Польза пастилы без сахара и глютена--}}
{{--            </h3>--}}
{{--            <p>--}}
{{--                Веганская пастила без сахара повышает иммунную защиту организма, снижает риск развития авитаминоза, улучшает состав крови, нормализует пищеварение. Она подходит в качестве элемента диетического питания.--}}
{{--            </p>--}}
{{--            <h3>--}}
{{--                Как выбрать пастилу?--}}
{{--            </h3>--}}
{{--            <p>--}}
{{--                При выборе пастилы веганской без глютена и без сахара внимательно изучите ее состав. В хорошем продукте не должно быть муки, крахмала, растительного масла. Минусом считается использование сахарозаменителей. Не забудьте проверить срок годности.--}}
{{--            </p>--}}
{{--            <div class="category-about__list">--}}
{{--                <div class="listitem">--}}
{{--                    <h3>--}}
{{--                        · Яблочная пастила--}}
{{--                    </h3>--}}
{{--                    <p>--}}
{{--                        Вкусное и ароматное лакомство изготавливается из яблочного пюре. Если вместо него используется пектин, то откажитесь от покупки.--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--                <div class="listitem">--}}
{{--                    <h3>--}}
{{--                        · Ягодная пастила--}}
{{--                    </h3>--}}
{{--                    <p>--}}
{{--                        Продукт производится из натуральных ягод или асептического пюре, предназначенного для детского питания.--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--                <div class="listitem">--}}
{{--                    <h3>--}}
{{--                        · Фруктовая пастила--}}
{{--                    </h3>--}}
{{--                    <p>--}}
{{--                        Продукт производится из натуральных ягод или асептического пюре, предназначенного для детского питания.--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--                <div class="listitem">--}}
{{--                    <h3>--}}
{{--                        ·  Пастила микс--}}
{{--                    </h3>--}}
{{--                    <p>--}}
{{--                        Продукт включает в себя смесь ягод или фруктов, поражает разнообразием вкусов. Но все ингредиенты должны быть натуральными.--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

{{--    </div>--}}

@stop


@section('scripts')
@stop


