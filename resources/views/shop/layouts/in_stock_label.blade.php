
@foreach($product->stok_label as $key => $val)
    {{ $val['nameTranslated'][$lang] }}
    @foreach($val['values'] as $item)
        @if ($loop->first)
            {{ $item }}
        @else
            , {{ $item }}
        @endif
    @endforeach
@endforeach
