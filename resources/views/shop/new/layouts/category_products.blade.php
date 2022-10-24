<?php

$translite['from'] = [
'ru' => 'от',
'en' => 'from'
];
?>

@if($products)
    <div class="card__items">
    @foreach($products as $product)
       <a class="card__item" href="{{ route('product_'.$lang, ['category' => $category->slag, 'product' => $product->slag]) }}">
           @if ($product->in_stock)
               @include("shop.new.layouts.product_in_stock_label")
           @endif
           <div class="card-img">
                @if (!empty($product->image))
                    <img src="{{ $product->image['image400pxUrl'] }}" alt="{{ $category_active }} - {{ $product->name }}" title="{{ $product->name }}">

                   @if (!empty($product->galery))
                       <img src="{{ $product->galery[1]['image400pxUrl'] }}" alt="{{ $category_active }} - {{ $product->name }}" title="{{ $product->name }}">
                   @endif
               @else
                    <img src=""  alt="{{ $category_active }} - {{ $product->name }}" title="{{ $product->name }}">
                @endif
            </div>
            <div class="card__text">
                <div class="card-name">
                    <p>
                        @if (!empty($product->translate['nameTranslated'][$lang]))
                            {{ $product->translate['nameTranslated'][$lang] }}
                        @else
                            {{ $product->name }}
                        @endif
                    </p>
                </div>
                <div class="card-price">
{{--                    <span>400 ₪</span>--}}
                    <p>
                        @if(!empty($product->variables))
                            {{ $translite['from'][$lang] }}
                        @endif
                            {{ $product->price }} ₪
                    </p>
                </div>
            </div>
        </a>
    @endforeach

{{--        <div class="card__pagination">--}}
{{--            <a href="#" class="prev">--}}
{{--                <svg width="6" height="9" viewBox="0 0 6 9" fill="none" xmlns="http://www.w3.org/2000/svg">--}}
{{--                    <path d="M4.63895 7.68324C4.66283 7.68324 4.71058 7.68324 4.73445 7.65936C4.7822 7.61161 4.7822 7.53999 4.73445 7.49223L1.46355 4.22133L4.73445 0.974302C4.7822 0.926551 4.7822 0.854926 4.73445 0.807175C4.6867 0.759425 4.61507 0.759425 4.56732 0.807175L1.22479 4.1497C1.17704 4.19746 1.17704 4.26908 1.22479 4.31683L4.56732 7.65936C4.56732 7.68324 4.61507 7.68324 4.63895 7.68324Z" stroke-width="1.5"/>--}}
{{--                </svg>--}}
{{--            </a>--}}
{{--            <a href="#" class="next">--}}
{{--                <svg width="6" height="9" viewBox="0 0 6 9" fill="none" xmlns="http://www.w3.org/2000/svg">--}}
{{--                    <path d="M4.63895 7.68324C4.66283 7.68324 4.71058 7.68324 4.73445 7.65936C4.7822 7.61161 4.7822 7.53999 4.73445 7.49223L1.46355 4.22133L4.73445 0.974302C4.7822 0.926551 4.7822 0.854926 4.73445 0.807175C4.6867 0.759425 4.61507 0.759425 4.56732 0.807175L1.22479 4.1497C1.17704 4.19746 1.17704 4.26908 1.22479 4.31683L4.56732 7.65936C4.56732 7.68324 4.61507 7.68324 4.63895 7.68324Z" stroke-width="1.5"/>--}}
{{--                </svg>--}}
{{--            </a>--}}
{{--        </div>--}}
    </div>

@endif


