@isset($product->options)
    @foreach($product->options as $key => $option)
        @if ($key != 'map' && $option['type'] == 'SIZE')


            @php($name = $option['name'])
            @php($name_lang = $option['nameTranslated'][$lang])
            @php($name_lang_lower = strtolower($name_lang))
            @php($name_lower = strtolower($name))

            <div class="product-info__size">
                <div class="product-size open-size-table">
                    <span>Выберите {{ $name_lang_lower }} </span>
                </div>
                <div class="product-size__table">
                    @foreach($option['choices'] as $ko => $choice)
                        @isset($choice['variant_number'])
                            @php($variant = $product->variables[$choice['variant_number']])
                        @endisset
                        <label class="product-size-var" data-infosize=" Размер Мини на 4-5 персон 199 ₪" value="{{ $ko }}">
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
                                    ({{ $translate['benefit'][$lang] }} {{ $variant['compareToPrice'] - $variant['defaultDisplayedPrice']  }}₪)
                                @endisset

                                {{ $variant['defaultDisplayedPrice'] }} ₪
                            </p>
                        </label>
                    @endforeach

                </div>
            </div>
        @elseif ($key != 'map' && $option['type'] == 'SELECT')

        @else

        @endif
    @endforeach
@else

@endisset
