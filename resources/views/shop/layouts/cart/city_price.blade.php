<ul>
    @foreach($delivery['delivery'] as $delivery_variant)
        @foreach($delivery_variant['cityes'] as $ciy_key)

            <li data-price="{{ $delivery_variant['rate_delivery'] }}"
                data-order="{{ $delivery_variant['min_sum_order'] }}"
                data_name="{{ $cityes['citys_all'][$ciy_key]['ru'] }}"
                data_city_name="{{ $cityes['citys_all'][$ciy_key]['en'] }}">{{ $cityes['citys_all'][$ciy_key]['ru'] }}</li>

            <li data-price="{{ $delivery_variant['rate_delivery'] }}"
                data-order="{{ $delivery_variant['min_sum_order'] }}"
                data_name="{{ $cityes['citys_all'][$ciy_key]['en'] }}"
                data_city_name="{{ $cityes['citys_all'][$ciy_key]['en'] }}">{{ $cityes['citys_all'][$ciy_key]['en'] }}</li>

            <li data-price="{{ $delivery_variant['rate_delivery'] }}"
                data-order="{{ $delivery_variant['min_sum_order'] }}"
                data_name="{{ $cityes['citys_all'][$ciy_key]['he'] }}"
                data_city_name="{{ $cityes['citys_all'][$ciy_key]['en'] }}">{{ $cityes['citys_all'][$ciy_key]['he'] }}</li>


            @if (!empty($delivery_variant['rate_delivery_to_summ_order']))
                @foreach($delivery_variant['rate_delivery_to_summ_order'] as $variant_rate)

{{--                    <li data-price="{{ $variant_rate['rate_delivery'] }}"--}}
{{--                        data_delivery = "rate_delivery_to_summ_order"--}}
{{--                        data-order = "{{ $variant_rate['sum_order']['min'] }}"--}}
{{--                        data_max = "{{ $variant_rate['sum_order']['max'] }}"--}}
{{--                        data_name="{{ $cityes['citys_all'][$ciy_key]['ru'] }}"--}}
{{--                        data_city_name="{{ $cityes['citys_all'][$ciy_key]['en'] }}" >--}}
{{--                        {{ $cityes['citys_all'][$ciy_key]['ru'] }}--}}
{{--                    </li>--}}

{{--                    <li data-price="{{ $variant_rate['rate_delivery'] }}"--}}
{{--                        data_delivery = "rate_delivery_to_summ_order"--}}
{{--                        data-order = "{{ $variant_rate['sum_order']['min'] }}"--}}
{{--                        data_max = "{{ $variant_rate['sum_order']['max'] }}"--}}
{{--                        data_name="{{ $cityes['citys_all'][$ciy_key]['en'] }}"--}}
{{--                        data_city_name="{{ $cityes['citys_all'][$ciy_key]['en'] }}" >--}}
{{--                        {{ $cityes['citys_all'][$ciy_key]['en'] }}--}}
{{--                    </li>--}}

{{--                    <li data-price="{{ $variant_rate['rate_delivery'] }}"--}}
{{--                        data_delivery = "rate_delivery_to_summ_order"--}}
{{--                        data-order = "{{ $variant_rate['sum_order']['min'] }}"--}}
{{--                        data_max = "{{ $variant_rate['sum_order']['max'] }}"--}}
{{--                        data_name="{{ $cityes['citys_all'][$ciy_key]['he'] }}"--}}
{{--                        data_city_name="{{ $cityes['citys_all'][$ciy_key]['en'] }}" >--}}
{{--                        {{ $cityes['citys_all'][$ciy_key]['he'] }}--}}
{{--                    </li>--}}
                @endforeach
            @endif

        @endforeach
    @endforeach
</ul>
