Available for today
@if ($product->stok_label != false)
    @include("shop.layouts.in_stock_label")
@else
{{--    , will ship within 1 day--}}
@endif
