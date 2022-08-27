
@foreach ($delivery['delivery'] as $key => $item)
    @php($rates[] = $item['rate_delivery'])
    @php($delivery_rates[] = $key)
@endforeach

@php(array_multisort($rates, $delivery_rates))

@foreach($delivery_rates as $item_key)
    @php($item = $delivery['delivery'][$item_key])
<tr>
    <td><span>{{ $item['name'][$lang] }}</span>
        @if (sizeof($item['cityes']) > 1)
            @foreach($item['cityes'] as $city_id)
                @if($loop->last)
                    {{ $cityes['citys_all'][$city_id][$lang] }}
                @else
                    {{ $cityes['citys_all'][$city_id][$lang] }},
                @endif
            @endforeach

        @else
            @foreach($item['cityes'] as $city_id)
                @if ($cityes['citys_all'][$city_id][$lang] != 'all')
                    {{ $cityes['citys_all'][$city_id][$lang] }}
                @endif
            @endforeach
        @endif
    </td>
    <td>{{ $item['min_sum_order'] }}₪</td>
    <td>{{ $item['rate_delivery'] }}₪</td>
</tr>
@endforeach
