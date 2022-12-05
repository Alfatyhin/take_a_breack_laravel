
<div class="product">
    <nav class="breadcrumbs"  itemscope itemtype="http://schema.org/BreadcrumbList">

        @if($lang == 'en')
            @php($rout = route("index"))
        @else
            @php($rout = route("index", ['lang' => $lang]))
        @endif
                <span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <a itemprop="item" href="{{ $rout }}">{{ $general }}</a>
                    <meta itemprop="name" content="{{ $general }}" />
                    <meta itemprop="position" content="1" />
                </span>
        @if($lang == 'en')
            @php($rout = route("category_index", ['category' => $category->slag]))
        @else
            @php($rout = route("category", ['lang' => $lang, 'category' => $category->slag]))
        @endif
        <span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <a itemprop="item" href="{{ $rout }}">
                        @php($translate = $category->translate)
                        @isset($translate['nameTranslated'][$lang])
                            @php($category_name = $translate['nameTranslated'][$lang] )
                        @else
                            @php($category_name = $category->name )
                        @endisset
                        {{ $category_name }}
                    </a>
                    <meta itemprop="name" content="{{ $category_name }}" />
                    <meta itemprop="position" content="2" />
                </span>
        <span>
                    <a itemprop="item" >
                          @if (!empty($product->translate['nameTranslated'][$lang]))
                            {{ $product->translate['nameTranslated'][$lang] }}
                        @else
                            {{ $product->name }}
                        @endif
                    </a>

                </span>
    </nav>
    @auth()
        @if(Auth::user()->user_role == 'admin' )
            <a class="admin fa fa-pencil" title="product redact" href="{{ route('product_redact', ['product' => $product]) }}" target="_blank">
            </a>
        @endif
    @endauth
    <div class="product__body">
        @include("shop.new.layouts.product_cart.galery")
        <div class="product__info product-info">
            <div class="product-info__title">
                <h1>
                    @if (!empty($product->translate['nameTranslated'][$lang]))
                        {{ $product->translate['nameTranslated'][$lang] }}
                    @else
                        {{ $product->name }}
                    @endif

                </h1>
            </div>
            <div class="product-info__price">
                <span>
                    <span class="current-price">
                        @if(!empty($product->variables) && sizeof($product->variables) > 1)
                            {{ __('shop.от') }}
                        @endif
                        {{ $product->price }}
                    </span>
                    ₪
                </span>
            </div>
            <div class="product_options">
                @include("shop.new.layouts.product_cart.options")
            </div>
            <div class="product-info__action">
                <div class="product-info__count">
                    <button class="product-info-decrement" disabled="true" onclick="decrement(this)">-</button>
                    <input id="count-product" class="product-info-count-input" value="1" type="number" name="product-count" min="1" max="999" disabled="true">
                    <button class="product-info-increment" disabled="true" onclick="increment(this)">+</button>
                </div>
                <button class="main-btn go-to-cart">{{ __('shop.Добавить в корзину') }}</button>
            </div>
            <div class="product-info__edge swiper">
                <div class="product-info__edge-wrapper swiper-wrapper">

                    @isset($category_data['attributes']['desc_icons'])
                        @include("shop.new.layouts.product_cart.desc_slider")
                    @endisset

                </div>
                <div class="product-info__edge-nav">
                    <div class="edge-prev">
                        <svg width="6" height="9" viewBox="0 0 6 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.63895 7.68324C4.66283 7.68324 4.71058 7.68324 4.73445 7.65936C4.7822 7.61161 4.7822 7.53999 4.73445 7.49223L1.46355 4.22133L4.73445 0.974302C4.7822 0.926551 4.7822 0.854926 4.73445 0.807175C4.6867 0.759425 4.61507 0.759425 4.56732 0.807175L1.22479 4.1497C1.17704 4.19746 1.17704 4.26908 1.22479 4.31683L4.56732 7.65936C4.56732 7.68324 4.61507 7.68324 4.63895 7.68324Z" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <div class="edge-next">
                        <svg width="6" height="9" viewBox="0 0 6 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.63895 7.68324C4.66283 7.68324 4.71058 7.68324 4.73445 7.65936C4.7822 7.61161 4.7822 7.53999 4.73445 7.49223L1.46355 4.22133L4.73445 0.974302C4.7822 0.926551 4.7822 0.854926 4.73445 0.807175C4.6867 0.759425 4.61507 0.759425 4.56732 0.807175L1.22479 4.1497C1.17704 4.19746 1.17704 4.26908 1.22479 4.31683L4.56732 7.65936C4.56732 7.68324 4.61507 7.68324 4.63895 7.68324Z" stroke-width="1.5"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="product-info__text">
                {!! $product->translate['descriptionTranslated'][$lang] !!}
            </div>

            <div class="product-info__tabs">
                @if(isset($product->data['attributes']) && (isset($product->data['attributes']['composition'][$lang]) || $product->data['attributes']['calories'] || $category_data['attributes']['keeping']))

                    <div class="product-info__tabs-btns">
                        <span class="product-info__tabs-btn">{{ __('shop.Состав') }}</span>
                        <span class="product-info__tabs-btn">{{ __('shop.Калорийность') }}</span>
                        <span class="product-info__tabs-btn">{{ __('shop.Хранение') }}</span>
                    </div>
                    <div class="product-info__tab-text">
                        @isset($product->data['attributes']['composition'][$lang])
                            <p>
                                <b>{{ __('shop.В составе десерта ингридиенты:') }}</b>
                                {{ $product->data['attributes']['composition'][$lang] }}
                            </p>
                        @endisset
                    </div>
                    <div class="product-info__tab-text">
                        @isset($product->data['attributes']['calories'])
                            <p>
                                <b>{{ __('shop.Энергетическая ценность') }}</b>
                            </p>
                            <p>
                                <b>{{ __('shop.Калорийность') }}</b>
                                {{ $product->data['attributes']['calories']['calories'] }} {{ __('shop.ккал') }}
                            </p>
                            <p>
                                <b>{{ __('shop.Белки') }}</b>
                                {{ $product->data['attributes']['calories']['protein'] }} {{ __('shop.г') }}
                            </p>
                            <p>
                                <b>{{ __('shop.Жиры') }}</b>
                                {{ $product->data['attributes']['calories']['fat'] }} г
                            </p>
                            <p>
                                <b>{{ __('shop.Углеводы') }}</b>
                                {{ $product->data['attributes']['calories']['carbohydrate'] }} г
                            </p>
                        @endisset
                    </div>
                    <div class="product-info__tab-text">

                        @isset($category_data['attributes']['keeping'][$lang])
                            <p>
                                <b>{{ __('shop.Сроки и условия хранения:') }}</b>
                                {!! $category_data['attributes']['keeping'][$lang] !!}
                            </p>
                        @endisset
                    </div>
                @endisset
            </div>
        </div>
    </div>
    <div class="recblock">
        <div class="recblock__title">
            <h3>
                {{ __('shop.Добавить к заказу') }}
            </h3>
        </div>
        <div class="recblock__items">
            @include("shop.new.layouts.slider")
        </div>
    </div>
</div>

