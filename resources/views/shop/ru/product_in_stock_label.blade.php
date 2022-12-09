
Доступно на сегодня
@if ($product->stok_label != false)
    @include("shop.layouts.in_stock_label")
@else
    {{--    , доставим за 1 день--}}
@endif