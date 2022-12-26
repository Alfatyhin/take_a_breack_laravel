@foreach($rand_keys as $prod_id)
    @isset($products[$prod_id])
        @php($product = $products[$prod_id])

        @isset($categories[$product->category_id])
            @if($lang == 'en')
                @php($rout = route("product", ['category' => $category->slag, 'product' => $product->slag]))
            @else
                @php($rout = route("product", ['lang' => $lang, 'category' => $category->slag, 'product' => $product->slag]))
            @endif


            @if (!empty($product->image))
                @php($prod_category = $categories[$product->category_id])
                <a href="{{ $rout }}"
                   class="recblock__item">
                    <div class="item-img">
                        <img src="{{ $product->image['image400pxUrl'] }}" title="{{ $product->name }}" alt="{{ $product->name }}">
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
        @endisset
    @endisset
@endforeach
