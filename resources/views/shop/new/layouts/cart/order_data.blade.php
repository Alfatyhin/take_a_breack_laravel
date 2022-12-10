@if ($lost_order)
    <input class="order_data" type="hidden" name="order_data" value="@json($order_data_jsonform)">
@else
    <input class="order_data" type="hidden" name="order_data" value="">
@endif
@error('order_data')
<p class="errors">error get products data</p>
@enderror