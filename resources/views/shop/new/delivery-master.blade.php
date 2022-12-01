
@extends('shop.new.shop_master')



@if ($lang == 'ru')
    @php($title = "Доставка")
@else

    @php($title = "Delivery")
@endif

@section('title', $title)



@section('head')


{{--    <link rel="canonical" href="{{ route("category_index", ['lang' => 'en', 'category' => $category->slag]) }}">--}}


{{--    <link rel="alternate" hreflang="ru" href="{{ route("category", ['lang' => 'ru', 'category' => $category->slag]) }}">--}}
{{--    <link rel="alternate" hreflang="en" href="{{ route("category_index", ['category' => $category->slag]) }}">--}}

{{--    @isset($category->data['seo']['description'][$lang])--}}
{{--        <meta name="description" content="{{ $category->data['seo']['description'][$lang] }}">--}}
{{--    @else--}}
{{--        <meta name="description" content="{{ $category_name }} 🧁 buy in the online store Take a Break: prices, reviews, composition - delivery to Gush Dan, Netanya, Ashdod, Haifa and Jerusalem ☎ +972 55-947-581">--}}
{{--    @endisset--}}

{{--    @isset($category->data['seo']['keywords'][$lang])--}}
{{--        <meta name="Keywords" content="{{ $category->data['seo']['keywords'][$lang] }}">--}}
{{--    @endisset--}}


@stop

{{--@section('product_filter')--}}
{{--    @include('shop.new.layouts.products_filters')--}}
{{--@stop--}}

@section('content')

    @include("shop.new.layouts.left_sidebar")


    <div class="deliv">
        <div class="deliv__title">
            <h2>
                {{ __('shop-delivery.Доставка и оплата') }}
            </h2>
        </div>
        <div class="deliv__info">
            <h3>

                {{ __('shop-delivery.Зона доставки и цены') }}

            </h3>
            <p>
                {{ __('shop-delivery.Минимальная сумма заказа инфо') }}

            </p>
        </div>
        <div class="deliv__select">
            <div class="product-info__size">
                <div class="product-size open-size-table">
                    <span style="text-transform: uppercase;">
                {{ __('shop-delivery.введите город доставки') }}</span>
                </div>
                <div class="product-size__table">
                    @foreach($delivery['cityes_data'] as $city_id => $item)
                        @foreach($item as $delivery_id)
                            <label class="product-size-var"
                                   data-infosize="{{ $cityes['citys_all'][$city_id][$lang] }}"
                                   value="{{ $delivery_id }}">
                                <div class="option-info">
                                    <p>
                                        {{ $cityes['citys_all'][$city_id][$lang] }}
                                    </p>
                                </div>
                            </label>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>

        <div class="deliv__list">
            <div class="deliv-header">
                <span>
                {{ __('shop-delivery.зона доставки') }}</span>
                <span>
                {{ __('shop-delivery.минимальная сумма заказа') }}</span>
                <span>
                {{ __('shop-delivery.стоимость доставки') }}</span>
            </div>

            @foreach($delivery['delivery'] as $item)
                <article class="deliv-item">
                    <p>
                        @foreach($item['cityes'] as $city_id)
                            @if(!$loop->first)
                                ,
                            @endif
                            {{ $cityes['citys_all'][$city_id][$lang] }}

                        @endforeach
                    </p>
                    <p>
                        {{ $item['min_sum_order'] }}₪
                    </p>
                    <p>
                        {{ $item['rate_delivery'] }}₪
                    </p>
                </article>
            @endforeach

        </div>
        <div class="deliv__action">
            <p>
                {{ __('shop-delivery.Если вашего города нет в списке') }}
            </p>
            <button class="black-btn">
                {{ __('shop-delivery.написать в') }}  whatsapp <img src="assets/images/icons/checkout-svg.svg" alt=""></button>
        </div>
    </div>

@stop



@section('scripts')
@stop



