
@if($products)
    <div class="card__items">
        @foreach($category->products as $product_id)
            @isset($products[$product_id])
                @php($product = $products[$product_id])

                @if($lang == 'en')
                    @php($rout = route("product_index", ['category' => $category->slag, 'product' => $product->slag]))
                @else
                    @php($rout = route("product", ['lang' => $lang, 'category' => $category->slag, 'product' => $product->slag]))
                @endif

                <a class="card__item" href="{{ $rout }}">
                    @if ($product->in_stock)
                        @include("shop.new.layouts.product_in_stock_label")
                    @endif
                    <div class="card-img">
                        @if (!empty($product->image))
                            <img src="{{ $product->image['image400pxUrl'] }}" alt="{{ $category_active }} - {{ $product->name }}" title="{{ $product->name }}">

                            @isset($product->galery[1]['image400pxUrl'])
                                <img src="{{ $product->galery[1]['image400pxUrl'] }}" alt="{{ $category_active }} - {{ $product->name }}" title="{{ $product->name }}">
                            @endisset
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
                                    {{ __('shop.от') }}
                                @endif
                                {{ $product->price }} ₪
                            </p>
                        </div>
                    </div>
                </a>
            @endisset
        @endforeach

    </div>

@endif


