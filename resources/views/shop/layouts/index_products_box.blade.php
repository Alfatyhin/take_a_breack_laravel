<?php

$translite['from'] = [
    'ru' => 'от',
    'en' => 'from'
];
?>

@if($products)
    @foreach($products as $product)
        <a class="select__sliderItem" href="{{ route('product_'.$lang, ['category' => $category->slag, 'product' => $product->slag]) }}" data-type="{{ $category->name }}">

            <div class="select__sliderItemContainer">
                @if ($product->in_stock)
                    <div class="select__sliderItemHave">@include("shop.$lang.product_in_stock_label")</div>
                @endif
                @if (!empty($product->image))
                    <img src="{{ $product->image['image400pxUrl'] }}" alt="{{ $category_active }} - {{ $product->name }}" title="{{ $product->name }}">
                @else
                    <img src=""  alt="{{ $category_active }} - {{ $product->name }}" title="{{ $product->name }}">
                @endif
                <div class="select__sliderItemText">
                    @if (!empty($product->translate['nameTranslated'][$lang]))
                        {{ $product->translate['nameTranslated'][$lang] }}
                    @else
                        {{ $product->name }}
                    @endif
                </div>
                <div class="select__sliderPrice">
                    <div class="select__sliderPriceItem">
                        @if(!empty($product->variables))
                            {{--                        {{ $translite['from'][$lang] }}--}}
                        @endif
                        <span class='sliderPrice'>{{ $product->price }}</span><span class='sliderUnit'>₪</span></div>
                    <div class="select__sliderPriceItem select__sliderPriceItem--old"></div>
                </div>
            </div>
        </a>
    @endforeach
@endif

