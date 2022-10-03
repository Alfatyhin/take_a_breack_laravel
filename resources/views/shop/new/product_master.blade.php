
@extends('shop.new.shop_master')

@if (!empty($category->translate['nameTranslated'][$lang]))
    @php($category_name = $category->translate['nameTranslated'][$lang])
@else
    @php($category_name = $category->name)
@endif

@if (!empty($product->translate['nameTranslated'][$lang]))
    @php($product_name = $product->translate['nameTranslated'][$lang])
@else
    @php($product_name = $product->name)
@endif

@section('title')

    @if ($lang == 'ru')
        {{ $product_name }} – купить в интернет-магазине Take a Break: цены, отзывы, состав
        @php($general = 'Главная')
    @else
        {{ $product_name }} - buy in the online store Take a Break: prices, reviews, composition
        @php($general = 'Home')
    @endif
@stop

@section('head')

    <link rel="stylesheet" href="{{ asset('/assets/libs/swiper-bundle.min.css') }}?{{ $v }}">

    <link rel="canonical" href="{{ route("product_$lang", ['category' => $category->slag, 'product' => $product->slag]) }}">

    <link rel="alternate" hreflang="ru" href="{{ route("product_ru", ['category' => $category->slag, 'product' => $product->slag]) }}">
    <link rel="alternate" hreflang="en" href="{{ route("product_en", ['category' => $category->slag, 'product' => $product->slag]) }}">


    @if ($lang == 'ru')
        <meta name="description" content="{{ $product_name }} купить за {{ $product->price }} ₪ в интернет-магазине Take a Break. ☎ +972 55-947-5812. Отзывы ✔ Натуральные десерты ✈ Доставка ➤ Лучшие цены!">
    @else
        <meta name="description" content="{{ $product_name }} buy for {{ $product->price }} ₪ in the online store Take a Break.  ☎ +972 55-947-5812. Reviews ✔ Natural desserts ✈ Delivery ➤ Best prices!">
    @endif

    <script>
        var product = @json($product);
    </script>

@stop

@section('content')

    @include("shop.new.$lang.product")

    <div class="hidden" style="display: none">
        <div itemtype="http://schema.org/Product" itemscope>
            <meta itemprop="mpn" content="{{ $product->id }}" />
            <meta itemprop="name" content="@if (!empty($product->translate['nameTranslated'][$lang]))
            {{ $product->translate['nameTranslated'][$lang] }}
            @else
            {{ $product->name }}
            @endif" />

            @if (!empty($product->image))
                <link itemprop="image" href="{{ $product->image['image800pxUrl'] }}" />
                <link itemprop="image" href="{{ $product->image['image400pxUrl'] }}" />
                <link itemprop="image" href="{{ $product->image['image160pxUrl'] }}" />
            @endif

            <meta itemprop="description" content="{!! $product->translate['descriptionTranslated'][$lang] !!}" />
            <div itemprop="offers" itemtype="http://schema.org/Offer" itemscope>
                <link itemprop="url" href="{{ route('product_'.$lang, ['category' => $category->slag, 'product' => $product->slag]) }}" />
                <meta itemprop="availability" content="https://schema.org/InStock" />
                <meta itemprop="priceCurrency" content="ILS" />
                <meta itemprop="itemCondition" content="https://schema.org/UsedCondition" />

                @if(!empty($product->variables))
                    @if (sizeof($product->variables) == 1)

                        @foreach($product->variables as $kv => $variant)

                            @isset($variant['compareToPrice'])
                                <meta itemprop="price" content="{{ $variant['compareToPrice'] }}" />
                            @else

                                <meta itemprop="price" content="{{ $product->price }}" />
                            @endisset

                        @endforeach
                    @else
                        @if(!empty($product->compareToPrice))
                            <meta itemprop="price" content="{{ $product->compareToPrice }}" />
                        @else
                            <meta itemprop="price" content="{{ $product->price }}" />
                        @endif
                    @endif
                @else
                    @if(!empty($product->compareToPrice))
                        <meta itemprop="price" content="{{ $product->compareToPrice }}" />
                    @else
                        <meta itemprop="price" content="{{ $product->price }}" />
                    @endif
                @endif


                <meta itemprop="priceValidUntil" content="{{ $product->updated_at }}" />
                <div itemprop="seller" itemtype="http://schema.org/Organization" itemscope>
                    <meta itemprop="name" content="Executive Objects" />
                </div>
            </div>
            {{--            <div itemprop="aggregateRating" itemtype="http://schema.org/AggregateRating" itemscope>--}}
            {{--                <meta itemprop="reviewCount" content="89" />--}}
            {{--                <meta itemprop="ratingValue" content="4.4" />--}}
            {{--                <meta itemprop="worstRating" content = "1"> // Худший рейтинг--}}
            {{--                <meta itemprop="bestRating" content = "5"> // Худший рейтинг--}}
            {{--            </div>--}}
            {{--            <div itemprop="review" itemtype="http://schema.org/Review" itemscope>--}}
            {{--                <div itemprop="author" itemtype="http://schema.org/Person" itemscope>--}}
            {{--                    <meta itemprop="name" content="ФИО того, кто оставил отзыв" />--}}
            {{--                </div>--}}
            {{--                <div itemprop="reviewRating" itemtype="http://schema.org/Rating" itemscope>--}}
            {{--                    <meta itemprop="ratingValue" content="4" />--}}
            {{--                    <meta itemprop="bestRating" content="5" />--}}
            {{--                </div>--}}
            {{--            </div>--}}
            <meta itemprop="sku" content="{{ $product->sku }}" />
            <div itemprop="brand" itemtype="http://schema.org/Thing" itemscope>
                <meta itemprop="name" content="{{ $product->name }}" />
            </div>
        </div>
    </div>

@stop


@section('scripts')
    <script src="{{ asset('/assets/libs/swiper-bundle.min.js') }}?{{ $v }}" defer></script>
@stop


