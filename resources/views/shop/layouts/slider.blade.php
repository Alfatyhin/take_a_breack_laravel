@foreach($rand_keys as $prod_id)
    @php($product = $products[$prod_id])
    @if (!empty($product->image))
        @php($prod_category = $categories[$product->category_id])
        <a class="additional__sliderItem" href="{{ route('product_'.$lang, ['category' => $prod_category->slag, 'product' => $product->slag]) }}">
            <img src="{{ $product->image['image400pxUrl'] }}" title="{{ $product->name }}" alt="{{ $product->name }}">
            <div class="additional__sliderItemText">{{ $product->translate['nameTranslated'][$lang] }}</div>
            <div class="additional__sliderPrice">{{ $product->price }}₪</div>
        </a>
    @endif
@endforeach
