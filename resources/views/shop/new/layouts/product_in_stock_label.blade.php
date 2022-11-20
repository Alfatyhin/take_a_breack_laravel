
<div class="card-label">
    @isset($product->variables)
        @foreach($product->variables as $kv => $variant)
            @if($variant['unlimited'] == 0 && $variant['quantity'] > 0)
                @foreach($variant['options'] as $item)
                    @if(preg_match('/size/i', $item['name']))
                        @php($name_lower = strtolower($item['value']))
                        <img src="/assets/images/icons/white-size{{ $kv + 1 }}.png" alt="">
                    @endif
                @endforeach
            @endif
        @endforeach
    @endisset
    <div class="text">
        @if ($product->stok_label != false)
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

        @else

        @endif

        {{ __('shop.доступен сегодня') }}

    </div>
</div>