<?php

$translite['aviable'] = [
    'ru' => 'доступен сегодня',
    'en' => 'available today'
];
?>


<div class="card-label">
    @foreach($product->variables as $variant)
        @if($variant['unlimited'] == 0 && $variant['quantity'] > 0)
            @foreach($variant['options'] as $item)
                @if(preg_match('/size/i', $item['name']))
                    @php($name_lower = strtolower($item['value']))
                    <img src="/assets/images/icons/size_{{ $name_lower }}.png" alt="">
                @endif
            @endforeach
        @endif
    @endforeach
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

        {{ $translite['aviable'][$lang] }}

    </div>
</div>