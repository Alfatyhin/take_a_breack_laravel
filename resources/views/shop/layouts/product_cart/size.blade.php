@isset($product->options)
    @foreach($product->options as $key => $option)
        @if ($key != 'map' && $option['type'] == 'SIZE')


                @php($name = $option['name'])

                @isset($product->variables)
                    <div class="description__sizeTitle description__sizeTitle--many showBlock">{{ $translate['Select size'][$lang] }}</div>
                    <div class="description__sizeList openingBlock showBlock">
                    @foreach($product->variables as $kv => $variant)
                        @if ($variant['unlimited'] == 0)
                            @php($stock_count = $variant['quantity'])
                        @else
                            @php($stock_count = 0)
                        @endif
                        @foreach($variant['options'] as $var_opt)
                            @if ($var_opt['name'] == $name)
                                @php($value = $var_opt['value'])
                                <div class="description__sizeListItem">
                                    <input id="sizeItem{{ $kv }}" type="radio" value="{{ $variant['defaultDisplayedPrice'] }}" name="sizeItem">
                                    <label for="sizeItem{{ $kv }}">
                                        <div class="description__sizeListItemTitle" data-id="{{ $kv }}"
                                             data-stock_count="{{ $stock_count }}"
                                             data-variant_id="{{ $kv }}"
                                             data-option_key="{{ $key }}"
                                             data-option_value="{{ $product->options['map'][$name]['choices'][$value]['key'] }}">

                                            @if(!empty($product->options['map'][$name]['nameTranslated'][$lang]))
                                                {{ $product->options['map'][$name]['nameTranslated'][$lang] }}
                                            @else
                                                {{ $name }}
                                            @endif
                                            @if(!empty($product->options['map'][$name]['choices'][$value]['textTranslated'][$lang]))
                                                {{ $product->options['map'][$name]['choices'][$value]['textTranslated'][$lang] }}
                                            @else
                                                {{ $value }}
                                            @endif


                                            @isset($variant['compareToPrice'])
                                                ({{ $translate['benefit'][$lang] }} {{ $variant['compareToPrice'] - $variant['defaultDisplayedPrice']  }}₪)
                                            @endisset
                                        </div>
                                        <div class="description__sizeListItemPrice">{{ $variant['defaultDisplayedPrice'] }}₪</div>
                                    </label>
                                </div>
                            @endif
                        @endforeach
                    @endforeach

                    </div>
                @else
                    <div class="description__sizeTitle"></div>
{{--                    @php(dd($option))--}}

                @endisset
        @elseif ($key != 'map' && $option['type'] == 'SELECT')
            @php($name = $option['name'])
            <div class="description__sizeTitle description__sizeTitle--many">
                @isset($option['nameTranslated'][$lang])
                    {{ $option['nameTranslated'][$lang] }}
                @else
                    {{ $option['name'] }}
                @endisset
            </div>
            <div class="description__sizeList openingBlock">
                @php($name = $option['name'])

                @isset($product->variables)
                    @foreach($product->variables as $kv => $variant)
                        @if ($variant['unlimited'] == 0)
                            @php($stock_count = $variant['quantity'])
                        @else
                            @php($stock_count = 0)
                        @endif
                        @foreach($variant['options'] as $var_opt)
                            @if ($var_opt['name'] == $name)
                                @php($value = $var_opt['value'])
                                <div class="description__sizeListItem">
                                    <input id="sizeItem{{ $kv }}" type="radio" value="{{ $variant['defaultDisplayedPrice'] }}" name="sizeItem">
                                    <label for="sizeItem{{ $kv }}">
                                        <div class="description__sizeListItemTitle" data-id="{{ $kv }}"
                                             data-stock_count="{{ $stock_count }}"
                                             data-variant_id="{{ $kv }}"
                                             data-option_key="{{ $key }}"
                                             data-option_value="{{ $product->options['map'][$name]['choices'][$value]['key'] }}">

                                            @if(!empty($product->options['map'][$name]['nameTranslated'][$lang]))
                                                {{ $product->options['map'][$name]['nameTranslated'][$lang] }}
                                            @else
                                                {{ $name }}
                                            @endif
                                            @if(!empty($product->options['map'][$name]['choices'][$value]['textTranslated'][$lang]))
                                                {{ $product->options['map'][$name]['choices'][$value]['textTranslated'][$lang] }}
                                            @else
                                                {{ $value }}
                                            @endif


                                            @isset($variant['compareToPrice'])
                                                ({{ $translate['benefit'][$lang] }} {{ $variant['compareToPrice'] - $variant['defaultDisplayedPrice']  }}₪)
                                            @endisset
                                        </div>
                                        <div class="description__sizeListItemPrice">{{ $variant['defaultDisplayedPrice'] }}₪</div>
                                    </label>
                                </div>
                            @endif
                        @endforeach
                    @endforeach
                @else

                    @if ($product->unlimited == 0)
                        @php($stock_count = $product->count)
                    @else
                        @php($stock_count = 0)
                    @endif

                    @foreach($option['choices'] as $kc => $choice)

                            @if ($choice['priceModifier'] != 0)
                                @if ($choice['priceModifierType'] == 'ABSOLUTE')
                                    @php($price =  $product->price + $choice['priceModifier'] / 1)
                                @else
                                    @php($price =  $product->price + ($product->price / 100 * $choice['priceModifier']))
                                @endif
                            @else

                                @php($price = $product->price)
                            @endif

                            <div class="description__sizeListItem">
                                <input id="sizeItem{{ $kc }}" type="radio" value="{{ $price }}" name="sizeItem">
                                <label for="sizeItem{{ $kc }}">
                                    <div class="description__sizeListItemTitle" data-id="{{ $kc }}"
                                         data-stock_count="{{ $stock_count }}"
                                         data-option_key="{{ $key }}"
                                         data-option_value="{{ $kc }}">

                                        @if(!empty($choice['textTranslated'][$lang]))
                                            {{ $choice['textTranslated'][$lang] }}
                                        @else
                                            {{ $name }}
                                        @endif
                                    </div>
                                    <div class="description__sizeListItemPrice">{{ $price }}₪</div>
                                </label>
                            </div>

                    @endforeach
                @endisset

            </div>
        @else
            @if ($key != 'map')
                <div class="description__sizeTitle"></div>
            @endif
        @endif
    @endforeach
@else

    @if ($product->unlimited == 0)
        @php($stock_count = $product->count)
    @else
        @php($stock_count = 0)
    @endif
    <div class="description__sizeTitle"  data-stock_count="{{ $stock_count }}"></div>
@endisset
