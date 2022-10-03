
<div class="product__imgs">
    <div class="product-slider swiper">
        <div class="product-slider__list swiper-wrapper">
            @if (!empty($product->galery))
                @foreach($product->galery as $k => $item)
                    <div class="product-slider__slide swiper-slide">
                        <img src="{{ $item['image800pxUrl'] }}"  alt="{{ $category->name }} - {{ $product->name }} image {{ $k }}" title="{{ $product->name }} image {{ $k }}">
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <div class="product-preview swiper">
        <div class="product-preview__list swiper-wrapper">
            @if (!empty($product->galery))
                @foreach($product->galery as $k => $item)
                    <div class="product-preview__item swiper-slide">
                        <img src="{{ $item['image800pxUrl'] }}"  alt="{{ $category->name }} - {{ $product->name }} image {{ $k }}" title="{{ $product->name }} image {{ $k }}">
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>