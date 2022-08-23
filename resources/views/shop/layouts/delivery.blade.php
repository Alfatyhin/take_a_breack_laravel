@foreach($delivery['delivery'] as $item)
<tr>
    <td><span>{{ $item['name'][$lang] }}</span>
        @if (sizeof($item['cityes']) > 1)
            <b>:</b>
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
