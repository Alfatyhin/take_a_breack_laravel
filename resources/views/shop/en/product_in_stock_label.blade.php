Available for today
@if ($product->stok_label != false)
    @foreach($product->stok_label as $key => $val)
        {{ $val['nameTranslated'][$lang] }}
        @foreach($val['values'] as $item)
            {{ $item }}
        @endforeach
    @endforeach
@else
{{--    , will ship within 1 day--}}
@endif
