
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

    <link rel="canonical" href="{{ route("product", ['category' => $category->slag, 'product' => $product->slag]) }}">

    <link rel="alternate" hreflang="he" href="{{ route("product", ['lang' => 'he', 'category' => $category->slag, 'product' => $product->slag]) }}">
    <link rel="alternate" hreflang="ru" href="{{ route("product", ['lang' => 'ru', 'category' => $category->slag, 'product' => $product->slag]) }}">
    <link rel="alternate" hreflang="en" href="{{ route("product", ['category' => $category->slag, 'product' => $product->slag]) }}">


    @if ($lang == 'ru')
        <meta name="description" content="{{ $product_name }} купить за {{ $product->price }} ₪ в интернет-магазине Take a Break. ☎ +972 55-947-5812. Отзывы ✔ Натуральные десерты ✈ Доставка ➤ Лучшие цены!">
    @else
        <meta name="description" content="{{ $product_name }} buy for {{ $product->price }} ₪ in the online store Take a Break.  ☎ +972 55-947-5812. Reviews ✔ Natural desserts ✈ Delivery ➤ Best prices!">
    @endif

    <script>
        var product = @json($product);
        console.log(product);
    </script>

    @if($lang == 'en')
        @php($rout = route("product", ['category' => $category->slag, 'product' => $product->slag]))
    @else
        @php($rout = route("product", ['lang' => $lang, 'category' => $category->slag, 'product' => $product->slag]))
    @endif


@stop

@section('content')

    @include("shop.new.layouts.left_sidebar")

    @include("shop.new.layouts.product_cart.product")

    <div class="hidden" style="display: none">
        <div itemtype="http://schema.org/Product" itemscope>
            <meta itemprop="mpn" content="{{ $product->id }}" />
            @if (!empty($product->translate['nameTranslated'][$lang]))
                @php($name = $product->translate['nameTranslated'][$lang])
            @else
                @php($name = $product->name)
            @endif
            <meta itemprop="name" content="{{ $name }}f" />


            @if (!empty($product->image))
                <link itemprop="image" href="{{ $product->image['image800pxUrl'] }}" />
                <link itemprop="image" href="{{ $product->image['image400pxUrl'] }}" />
                <link itemprop="image" href="{{ $product->image['image160pxUrl'] }}" />
            @endif


            @if (!empty($product->translate['descriptionTranslated'][$lang]))
                @php($description = $product->translate['descriptionTranslated'][$lang])
            @elseif(!empty($product->translate['descriptionTranslated']['en']))
                @php($description = $product->translate['descriptionTranslated']['en'])
            @else
                @php($description = '')
            @endif

            @isset($product->data['attributes']['composition'][$lang])
                @php($description .= ' ' . __('shop.Состав') . ' ' . $product->data['attributes']['composition'][$lang])
            @endisset

            <meta itemprop="description" content="{{ $description }}" />


            <div itemprop="offers" itemtype="http://schema.org/Offer" itemscope>
                <link itemprop="url" href="{{ $rout }}" />
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

@section('popup')
    <div class="text_add">
        @if (!empty($product->translate['nameTranslated'][$lang]))
            {{ $product->translate['nameTranslated'][$lang] }}
        @else
            {{ $product->name }}
        @endif
        {{ __('shop.добавлен в корзину') }}</div>
    <div class="count_in_cart_text">{{ __('shop.в корзине') }}
        <span class="count_in_cart"></span>{{ __('shop.шт') }}. {{ __('shop.данного товара') }}
    </div>

    <div class="text_in_cart">{{ __('shop.Для продолжения оформления заказа перейдите пожалуйста в корзину') }}</div>

    <div class="buttons_popup">
        <button class="go_prod">{{ __('shop.ВЕРНУТЬСЯ К ТОВАРАМ') }}</button>
        <a href="{{ route('cart', ['lang' => $lang]) }}">
            <button class="go_cart">
                {{ __('shop.ПЕРЕЙТИ В КОРЗИНУ') }}
            </button>
        </a>
    </div>
@stop


@section('scripts')
    <script src="{{ asset('/assets/libs/swiper-bundle.min.js') }}?{{ $v }}" defer></script>
    <script>
        window.onload = function() {
            console.log('test jQ v-' + jQuery.fn.jquery);


            // var products_data = {}; // тут по идее берем из корзины если есть
            // $('.main-btn.go-to-cart').click(function () {
            //     var options = {};
            //     var cart_key = product.id;
            //     var variant = false;
            //     $('.product_options .product_option').each(function () {
            //         var option = {};
            //         var option_el = $(this).find('.option_value.active');
            //
            //         if (option_el.length != 0) {
            //             var option_key = $(option_el).attr('data-option_key');
            //             option.type = $(this).attr('data_optiontype');
            //             option.key = option_key;
            //             option.value = $(option_el).attr('data-option_value');
            //             option.text = $(option_el).find('.option_text').text();
            //             option.input_text = $(option_el).find('.option_input_text').val();
            //             var variant_number = $(option_el).attr('data-variant_number');
            //
            //             cart_key = `${cart_key}-${option_key}-${option.value}`;
            //             if (!!variant_number) {
            //                 variant = variant_number;
            //             }
            //
            //             options[option_key] = option;
            //         }
            //
            //
            //     });
            //     var count = $('#count-product').val();
            //     var product_item = {
            //         'id' : product.id,
            //         'count' : count,
            //         'variant' : variant,
            //         'options': options
            //     }
            //     products_data[cart_key] = product_item;
            //     console.log(products_data);
            // });
        }

    </script>
@stop


