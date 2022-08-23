<div class="description__generalImg">
    @if (!empty($product->image))
        <img src="{{ $product->image['image800pxUrl'] }}"  alt="{{ $category->name }} - {{ $product->name }}" title="{{ $product->name }}">
    @endif
</div>
<div class="description__listImages">
    @if (!empty($product->galery))
        @foreach($product->galery as $k => $item)
            <a href="#"><img src="{{ $item['image800pxUrl'] }}"  alt="{{ $category->name }} - {{ $product->name }} image {{ $k }}" title="{{ $product->name }} image {{ $k }}"></a>
        @endforeach
    @endif
</div>
