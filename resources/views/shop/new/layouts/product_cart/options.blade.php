@isset($product->options)
    @foreach($product->options as $key => $option)

        @php($name = $option['name'])
        @php($name_lang = $option['nameTranslated'][$lang])
        @php($name_lang_lower = strtolower($name_lang))
        @php($name_lower = strtolower($name))

        @if ($option['type'] == 'SIZE')

            <div class="product-info__size">
                <div class="product-size open-size-table">
                    <span>Выберите {{ $name_lang_lower }} </span>
                </div>
                <div class="product-size__table">
                    @foreach($option['choices'] as $ko => $choice)
                        @isset($choice['variant_number'])
                            @php($variant = $product->variables[$choice['variant_number']])
                            @php($price = $variant['defaultDisplayedPrice'])
                        @else
                            @if($choice['priceModifierType'] == 'ABSOLUTE' && $choice['priceModifier'] != 0)
                                @php($price = $product->price + $choice['priceModifier'])
                            @elseif($choice['priceModifierType'] == 'PERCENT' && $choice['priceModifier'] != 0)
                                @php($price = round($product->price + ($product->price * $choice['priceModifier'] / 100), 2))
                            @else
                                @php($price = $product->price)
                            @endif
                        @endisset
                        <label class="product-size-var" data-infosize="@if(!empty($option['nameTranslated'][$lang]))
                        {{ $option['nameTranslated'][$lang] }}
                        @else
                        {{ $name }}
                        @endif
                        @if(!empty($choice['textTranslated'][$lang]))
                        {{ $choice['textTranslated'][$lang] }}
                        @else
                        {{ $choice['text'] }}
                        @endif
                        @isset($choice['variant_number'])
                            {{ $variant['defaultDisplayedPrice'] }}
                        @endisset ₪"
                               data-option_key="{{ $key }}"
                               data-option_value="{{ $ko }}"
                               @isset($choice['variant_number'])
                                    data-variant_number="{{ $choice['variant_number'] }}"
                                    data-pricemodifier="{{ $variant['defaultDisplayedPrice'] }}"
                                    data-pricemodifiertype="PRICE"
                               @else
                                    data-pricemodifier="{{ $choice['priceModifier'] }}"
                                    data-pricemodifiertype="{{ $choice['priceModifierType'] }}"
                               @endisset

                        >

                            <img src="/assets/images/icons/{{ $name_lower }}{{ $ko+1 }}.png" alt="">
                            <div class="option-info">
                                <p>
                                    @if(!empty($option['nameTranslated'][$lang]))
                                        {{ $option['nameTranslated'][$lang] }}
                                    @else
                                        {{ $name }}
                                    @endif
                                    @if(!empty($choice['textTranslated'][$lang]))
                                        {{ $choice['textTranslated'][$lang] }}
                                    @else
                                        {{ $choice['text'] }}
                                    @endif
                                </p>
                                <div>
                                    @isset($choice['description'][$lang])
                                        <span>
                                              <pre>{{ $choice['description'][$lang] }}</pre>
                                        </span>
                                    @endisset
                                </div>
                            </div>
                            <p>
                                @isset($variant['compareToPrice'])
{{--                                    ({{ $translate['benefit'][$lang] }} {{ $variant['compareToPrice'] - $variant['defaultDisplayedPrice']  }}₪)--}}
                                @endisset
                                <span class="price">{{ $price }}</span> ₪
                            </p>
                        </label>
                    @endforeach

                </div>
            </div>
        @elseif ($option['type'] == 'TEXT')

            <div class="product-info__add">
                <input class="product-info-checkbox" type="checkbox" disabled="true">
                <p>добавить
                    @if(!empty($option['nameTranslated'][$lang]))
                        {{ $option['nameTranslated'][$lang] }}
                    @else
                        {{ $name }}
                    @endif</p>
                <div>
                    <label

                            data-option_key="{{ $key }}"
                            data-pricemodifier="{{ $option['choices'][0]['priceModifier'] }}"
                            data-pricemodifiertype="{{ $option['choices'][0]['priceModifierType'] }}">
                        <span>
                            @if(!empty($option['choices'][0]['textTranslated'][$lang]))
                                {{ $option['choices'][0]['textTranslated'][$lang] }}
                            @else
                                {{ $option['choices'][0]['text'] }}
                            @endif
                        </span>

                        <input class="body-product-info-input-text"
                               maxlength="{{ $option['max_size'] }}"
                               placeholder="@isset($option['choices'][0]['description'][$lang]){{ $option['choices'][0]['description'][$lang] }}@endisset"
                               type="text">

                    </label>
                    <span>
                        <span class="price-text">{{ $option['choices'][0]['priceModifier'] }}</span>
                        ₪
                    </span>
                    <button class="trans-btn">Добавить</button>
                </div>
            </div>
        @else

        @endif
    @endforeach
@else

@endisset
