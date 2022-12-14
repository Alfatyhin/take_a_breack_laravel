
@if($products)
    <div class="card__items">
        @foreach($category->products as $product_id)
            @isset($products[$product_id])
                @php($product = $products[$product_id])
                @include('shop.new.layouts.components.product_item')
            @endisset
        @endforeach

        @if(isset($products2) && $products2)
            @foreach($products2 as $product)
                @include('shop.new.layouts.components.product_item')
            @endforeach
        @endif
    </div>

@endif


