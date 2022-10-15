@foreach($rand_keys as $prod_id)
    @php($product = $products[$prod_id])
    @if (!empty($product->image))
        @php($prod_category = $categories[$product->category_id])
        <a href="{{ route('product_'.$lang, ['category' => $prod_category->slag, 'product' => $product->slag]) }}"
           class="recblock__item">
            <div class="item-img">
                <img src="{{ $product->image['image160pxUrl'] }}" title="{{ $product->name }}" alt="{{ $product->name }}">
            </div>
            <div class="item__text">
                <p>
                    {{ $product->translate['nameTranslated'][$lang] }}
                </p>
                <span>
                    {{ $product->price }} ₪
                </span>
            </div>
        </a>
    @endif
@endforeach
