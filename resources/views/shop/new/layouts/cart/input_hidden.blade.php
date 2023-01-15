
@if ($lost_order)
    <input hidden name="lang" value="{{ isset($order_data['lang']) ? $order_data['lang'] : '' }}">
@else
    <input hidden name="lang" value="{{ $lang }}">
@endif
@if ($lost_order)
    <input hidden name="gClientId" value="{{ isset($order_data['gClientId']) ? $order_data['gClientId'] : '' }}">
@else
    <input hidden name="gClientId" value="">
@endif
<input hidden name="gClientId" value="">
@if(!empty($order_number))
    <input hidden name="order_id" value="{{ $order_number }}">
@else
    <input hidden name="order_id" >
@endif