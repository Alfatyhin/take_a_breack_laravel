
<div class="card-label">
    @isset($product->variables)
        @foreach($product->variables as $kv => $variant)
            @if($variant['unlimited'] == 0 && $variant['quantity'] > 0)
                @foreach($variant['options'] as $item)
                    @if(preg_match('/size/i', $item['name']))
                        <img src="/assets/images/icons/white-size{{ $kv + 1 }}.png" alt="">
                    @endif
                @endforeach
            @endif
        @endforeach
    @endisset
    <div class="text">
        @isset($product->variables)
            @php($flag = false)
            @foreach($product->variables as $kv => $variant)

                @if($variant['unlimited'] == 0 && $variant['quantity'] > 0)
                    @if(!$loop->first && $flag)
                        &
                    @endif
                    @foreach($variant['options'] as $item)
                        @isset($item['nameTranslated'][$lang])
                            {{ $item['nameTranslated'][$lang] }}
                        @else
                            {{ $item['nameTranslated']['en'] }}
                        @endisset
                        @isset($item['textTranslated'][$lang])
                            {{ $item['textTranslated'][$lang] }}
                        @else
                            {{ $item['textTranslated']['en'] }}
                        @endisset

                    @endforeach

                    @php($flag = true)
                @endif


            @endforeach
        @endisset

        {{ __('shop.доступен сегодня') }}

    </div>
</div>