
@extends('shop.shop-master')

@section('title', "Natural author's desserts")

@section('head')

    <link rel="stylesheet" href="{{ asset('css/areasAndPrices.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/areasAndPrices_adaptation.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/index_adaptation.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/index-0.css') }}?{{ $v }}">

@stop

@section('content')
    @include("shop.$lang.master_popap")


    <section class="welcome" id="anchor-welcome">
        <div class="welcome__bgShadow"></div>
        <div class="container">
            <div class="welcome__body">

                @if(!empty($banner[$lang]))
                    <div class="welcome__banner">
                        {{ $banner[$lang] }}
                    </div>
                @endif
                <div class="welcome__container">
                    <h1 class="welcome__title">natural author's desserts for you</h1>
                    <div class="welcome__subtitle">The highest quality ingredients are the most important thing in our work</div>
                    <div class="welcome__btns">
                        <a class="welcome__selectDessertBtn welcome__btn blockBtn blockBtn--bgc" href="#anchor-select">choose a dessert</a>
                        {{--                        <a class="welcome__fastOrderBtn welcome__btn blockBtn blockBtn--transparent" href="./fast_order.html">quick order</a>--}}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="select" id="anchor-select_2">

        <ul style="padding-left: 40px;">
            @foreach($products as $product)
                @php($category = $categories[$product->category_id])
                <li>
                    <a class="select__sliderItem" href="{{ route('product_'.$lang, ['category' => $category->slag, 'product' => $product->slag]) }}" data-type="{{ $product->cat_names }}">

                        <div class="select__sliderItemText">
                            @if (!empty($product->translate['nameTranslated'][$lang]))
                                {{ $product->translate['nameTranslated'][$lang] }}
                            @else
                                {{ $product->name }}
                            @endif
                        </div>
                        <div class="select__sliderPrice">
                            <div class="select__sliderPriceItem">
                                {{ $product->name }}
                                <span class='sliderPrice'>{{ $product->price }}</span><span class='sliderUnit'>₪</span></div>
                            <div class="select__sliderPriceItem select__sliderPriceItem--old"></div>
                        </div>
                    </a>
                </li>

            @endforeach

        </ul>


    </section>


@stop


@section('scripts')
    <script src="{{ asset('js/index.js') }}?{{ $v }}" defer></script>
@stop

