@php($item_category = $categories[$product->category_id])

@if($lang == 'en')
    @php($rout = route("product_index", ['category' => $item_category->slag, 'product' => $product->slag]))
@else
    @php($rout = route("product", ['lang' => $lang, 'category' => $item_category->slag, 'product' => $product->slag]))
@endif

<a class="card__item" href="{{ $rout }}">
    @if ($product->in_stock)
        @include("shop.new.layouts.product_in_stock_label")
    @endif
    <div class="card-img">
        @if (!empty($product->image))
            <img src="{{ $product->image['image400pxUrl'] }}" alt="{{ $item_category->name }} - {{ $product->name }}" title="{{ $product->name }}">

            @isset($product->galery[1]['image400pxUrl'])
                <img src="{{ $product->galery[1]['image400pxUrl'] }}" alt="{{ $item_category->name }} - {{ $product->name }}" title="{{ $product->name }}">
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
            @if(!empty($product->compareToPrice))
                <span>{{ $product->compareToPrice }} ₪</span>
            @endif
            <p>
                @if(!empty($product->variables)) {{ __('shop.от') }} @endif {{ $product->price }} ₪
            </p>
        </div>
    </div>
</a>