
@if($products)
    <div class="card__items">
        @foreach($products as $product)
            @include('shop.new.layouts.components.product_item')
        @endforeach

        @if(isset($products2) && $products2)
            @foreach($products2 as $product)
                @include('shop.new.layouts.components.product_item')
            @endforeach
        @endif
    </div>

@endif


