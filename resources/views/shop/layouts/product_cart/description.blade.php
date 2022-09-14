<?php
$translate = [
    'Select size' => [
        'ru' => 'Выберите размер',
        'en' => 'Select size'
    ],
    'benefit' => [
        'ru' => 'выгода',
        'en' => 'benefit'
    ],
    'Add to Shopping Cart' => [
        'ru' => 'добавить в корзину',
        'en' => 'Add to Shopping Cart'
    ],
    'Continue Shopping' => [
        'ru' => 'Продолжить покупки',
        'en' => 'Continue Shopping'
    ],
    'go to cart' => [
        'ru' => 'перейти в корзину',
        'en' => 'go to cart'
    ],
    'from' => [
        'ru' => 'от',
        'en' => 'from'
    ]
];
?>


<h1 class="description__title">
    @if (!empty($product->translate['nameTranslated'][$lang]))
        {{ $product->translate['nameTranslated'][$lang] }}
    @else
        {{ $product->name }}
    @endif
</h1>

<div class="description__price">
    @if(!empty($product->variables))
        @if (sizeof($product->variables) == 1)

            @foreach($product->variables as $kv => $variant)

                @isset($variant['compareToPrice'])
                    <div class="description__priceItem description__priceItem--old">
                        <span class="description__priceItemNumber">{{ $variant['compareToPrice'] }}</span>
                        <span class="description__priceItemUnit">₪</span>
                    </div>

                    <div class="description__priceItem description__priceItem--current description__priceItem--new">
                        <span class="description__priceItemNumber">{{ $variant['defaultDisplayedPrice'] }}</span>
                        <span class="description__priceItemUnit">₪</span>
                    </div>
                @else
                    <div class="description__priceItem description__priceItem--current">
                        <span class="description__priceItemNumber">{{ $product->price }}</span>
                        <span class="description__priceItemUnit">₪</span>
                    </div>
                @endisset

            @endforeach
        @else
            @if(!empty($product->compareToPrice))
                <div class="description__priceItem description__priceItem--old">
                    <span class="description__priceItemNumber">{{ $product->compareToPrice }}</span>
                    <span class="description__priceItemUnit">₪</span>
                </div>

                <div class="description__priceItem description__priceItem--current description__priceItem--new">
                    <span class="description__priceItemNumber">{{ $product->price }}</span>
                    <span class="description__priceItemUnit">₪</span>
                </div>
            @else
                <div class="description__priceItem description__priceItem--current">
                    <span class="description__priceItemText">{{ $translate['from'][$lang] }}&nbsp</span>
                    <span class="description__priceItemNumber">{{ $product->price }}</span>
                    <span class="description__priceItemUnit">₪</span>
                </div>
            @endif
        @endif
    @else
        @if(!empty($product->compareToPrice))
            <div class="description__priceItem description__priceItem--old">
                <span class="description__priceItemNumber">{{ $product->compareToPrice }}</span>
                <span class="description__priceItemUnit">₪</span>
            </div>

            <div class="description__priceItem description__priceItem--current description__priceItem--new">
                <span class="description__priceItemNumber">{{ $product->price }}</span>
                <span class="description__priceItemUnit">₪</span>
            </div>
        @else
            <div class="description__priceItem description__priceItem--current">
                <span class="description__priceItemNumber">{{ $product->price }}</span>
                <span class="description__priceItemUnit">₪</span>
            </div>
        @endif
    @endif
</div>

<div class="description__size">
    @include("shop.layouts.product_cart.size")
</div>
<div class="description__text">{!! $product->translate['descriptionTranslated'][$lang] !!}</div>
<div class="description__slider">
    @isset($category_data['attributes']['desc_icons'])
        @include("shop.$lang.desc_slider")
    @endisset
</div>
<div class="description__addCart">
    <div class="description__count productCount">
        <button class="productCount__countMin productCount__countBtn" data-productId="{{ $product->id }}"></button>
        <div class="productCount__countNumber" data-productId="{{ $product->id }}">1</div>
        <button class="productCount__countMax productCount__countBtn" data-productId="{{ $product->id }}"></button>
    </div>
    <div class="description__cartBtns">
        <button class="description__btnAddToCart description__btnCart"><span class="description__btnAddToCartFirstTitle">
                {{ $translate['Add to Shopping Cart'][$lang] }}
            </span>
            <span class="description__btnAddToCartSecondTitle blockHide">
                {{ $translate['Continue Shopping'][$lang] }}
            </span></button>
        <a class="description__btnGoToCart description__btnCart" href="{{ route("cart_$lang") }}">
            {{ $translate['go to cart'][$lang] }}
        </a>
    </div>

</div>

@if($category->name == 'Cakes')
    @include("shop.layouts.product_cart.cake_size_".$lang)
@endif
