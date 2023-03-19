

@if ($option['type'] == 'SIZE')

    <div class="product-info__size product_option required" data-reguired="true" data_optiontype="{{ $option['type'] }}">
        <div class="product-size open-size-table">
            <span>{{ __('shop.Выберите') }} {{ $name_lang_lower }} </span>
        </div>
        <div class="product-size__table">
            @foreach($option['choices'] as $ko => $choice)
                @php($variant = false)
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


                @if(!empty($option['nameTranslated'][$lang]))
                    @php($info = $option['nameTranslated'][$lang])
                @else
                    @php($info = $name)
                @endif

                @if(!empty($choice['textTranslated'][$lang]))
                    @php($info .= ' ' . $choice['textTranslated'][$lang])
                @else
                    @php($info .= ' ' . $choice['text'])
                @endif

                @isset($choice['metrics'])
                    @foreach($choice['metrics'] as $metric)
                        @isset($metric[$lang])
                            @php($info .= ' ' . $metric[$lang])
                        @endisset
                    @endforeach
                @endisset


                <label class="product-size-var option_value" data-infosize="{{ $info }}"
                       data-option_text="{{ $info }}"
                       data_id="{{ $product->id }}-{{ $key }}-{{ $ko }}"
                       data_url="{{ $rout }}"
                       data-option_key="{{ $key }}-{{ $ko }}"
                       data-option_value="{{ $ko }}"
                       data-price="{{ $price }}"
                       @isset($choice['variant_number'])
                       data-variant_number="{{ $choice['variant_number'] }}"
                       data-pricemodifier="{{ $variant['defaultDisplayedPrice'] }}"
                       data-pricemodifiertype="VARIANT_PRICE"
                       @else
                       data-variant_number=""
                       data-pricemodifier="{{ $choice['priceModifier'] }}"
                       data-pricemodifiertype="{{ $choice['priceModifierType'] }}"
                        @endisset
                >

                    @if($option['name'] == 'Size')
                        <img src="/assets/images/icons/{{ $name_lower }}{{ $ko+1 }}.png" alt="{{ $name_lower }}">
                    @endif
                    <div class="option-info option_text">
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

                            @isset($choice['description'][$lang])
                                <span>
                                                {{ $choice['description'][$lang] }}
                                            </span>
                            @endisset
                        </p>
                        <div>
                            @isset($choice['metrics'])
                                @foreach($choice['metrics'] as $metric)
                                    @isset($metric[$lang])
                                        <span>
                                                    <pre class="weight-params">{{ $metric[$lang] }}</pre>
                                                </span>
                                    @endisset
                                @endforeach
                            @else
                                <pre class="weight-params"> </pre>
                            @endisset
                        </div>
                    </div>
                        @isset($variant['compareToPrice'])

                            <div>
                                <p>
                                    <span class="old_price">{{ $variant['compareToPrice'] }}</span> ₪
                                </p>
                                <p>
                                    <span class="price">{{ $price }}</span> ₪
                                </p>
                            </div>
                        @else
                            <p>
                                <span class="price">{{ $price }}</span> ₪
                            </p>
                        @endisset
                </label>
            @endforeach

        </div>
    </div>
@elseif ($option['type'] == 'TEXT')


    @if($option['choices'][0]['priceModifierType'] == 'ABSOLUTE' && $option['choices'][0]['priceModifier'])
        @php($price = $option['choices'][0]['priceModifier'])
    @else
        @php($price = round($product->price * $option['choices'][0]['priceModifier'] / 100, 2))
    @endif

    @if(!empty($option['nameTranslated'][$lang]))
        @php($info = $option['nameTranslated'][$lang])
    @else
        @php($info = $name)
    @endif


    <div class="product-info__add product_option text" data_optiontype="{{ $option['type'] }}">
        <label>
            <input class="product-info-checkbox" type="checkbox" @if($size) disabled="true" @endif>
            <p>{{ __('shop.добавить') }}
                @if(!empty($option['nameTranslated'][$lang]))
                    {{ $option['nameTranslated'][$lang] }}
                @else
                    {{ $name }}
                @endif
            </p>
        </label>

        <div class="body-product-info-add">

            <label class="option_value"
                   data-option_text="{{ $info }}"
                   data-option_key="{{ $key }}-0"
                   data-price="{{ $price }}"
                   data-option_value="0"
                   data-variant_number=""
                   data-pricemodifier="{{ $option['choices'][0]['priceModifier'] }}"
                   data-pricemodifiertype="{{ $option['choices'][0]['priceModifierType'] }}">
                        <span>
                            @if(!empty($option['choices'][0]['textTranslated'][$lang]))
                                {{ $option['choices'][0]['textTranslated'][$lang] }}
                            @else
                                {{ $option['choices'][0]['text'] }}
                            @endif
                        </span>

                <input class="body-product-info-input-text option_input_text"
                       maxlength="{{ $option['max_size'] }}"
                       placeholder="@isset($option['choices'][0]['description'][$lang]){{ $option['choices'][0]['description'][$lang] }}@endisset"
                       type="text">

            </label>
            <span>
                        <span class="price-text">{{ $price }}</span>
                        ₪
                    </span>
            <button class="trans-btn" data_add="{{ __('shop.Добавить') }}" data_delete="{{ __('shop.Убрать текст') }}">{{ __('shop.Добавить') }}</button>
        </div>
    </div>
@elseif ($option['type'] == 'RADIO' && isset($option['choices']))

    <div class="product-info__add product_option radio" data_optiontype="{{ $option['type'] }}">

        <p>
            <span>{{ __('shop.Выберите') }} {{ $name_lang_lower }} </span>
        </p>
        @foreach($option['choices'] as $ko => $choice)
            @isset($choice['variant_number'])
                @php($variant = $product->variables[$choice['variant_number']])
                @php($price = $variant['defaultDisplayedPrice'])
            @else
                @if($choice['priceModifierType'] == 'ABSOLUTE' && !isset($choice['variant_number']))
                    @php($price = $choice['priceModifier'])
                @elseif($choice['priceModifierType'] == 'PERCENT' && !isset($choice['variant_number']))
                    @php($price = round(($product->price * $choice['priceModifier'] / 100), 2))
                @else
                    @php($price = $product->price)
                @endif
            @endisset


            @if(!empty($option['nameTranslated'][$lang]))
                @php($info = $option['nameTranslated'][$lang])
            @else
                @php($info = $name)
            @endif

            @if(!empty($choice['textTranslated'][$lang]))
                @php($info .= ' ' . $choice['textTranslated'][$lang])
            @else
                @php($info .= ' ' . $choice['text'])
            @endif

            @isset($choice['metrics'])
                @foreach($choice['metrics'] as $metric)
                    @isset($metric[$lang])
                        @php($info .= ' ' . $metric[$lang])
                    @endisset
                @endforeach
            @endisset

            <label class=" option_value" data-infosize="{{ $info }}"
                   data-option_text="{{ $info }}"
                   data_id="{{ $product->id }}-{{ $key }}-{{ $ko }}"
                   data-price="{{ $price }}"
                   data_url="{{ $rout }}"
                   data-option_key="{{ $key }}-{{ $ko }}"
                   data-option_value="{{ $ko }}"
                   @isset($choice['variant_number'])
                   data-variant_number="{{ $choice['variant_number'] }}"
                   data-pricemodifier="{{ $variant['defaultDisplayedPrice'] }}"
                   data-pricemodifiertype="VARIANT_PRICE"
                   @else
                   data-variant_number=""
                   data-pricemodifier="{{ $choice['priceModifier'] }}"
                   data-pricemodifiertype="{{ $choice['priceModifierType'] }}"
                    @endisset

            >
                <div class="option-info option_text">
                    <p>
                        <input type="radio" name="{{ $option['name'] }}" @if($size) disabled="true" @endif>
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

                        @isset($choice['description'][$lang])
                            <span>
                                                {{ $choice['description'][$lang] }}
                                            </span>
                        @endisset
                        @isset($choice['metrics'])
                            @foreach($choice['metrics'] as $metric)
                                @isset($metric[$lang])
                                    <span>
                                                    <pre class="weight-params">{{ $metric[$lang] }}</pre>
                                                </span>
                    @endisset
                    @endforeach
                    @else
                        <pre class="weight-params"> </pre>
                    @endisset

                    +<span class="price">{{ $price }}</span> ₪
                    </p>
                </div>

            </label>
        @endforeach

    </div>
@elseif ($option['type'] == 'CHECKBOX' && isset($option['choices']))

    <div class="product-info__add product_option checkbox" data_optiontype="{{ $option['type'] }}">

        <p>
            <span>{{ __('shop.Выберите') }} {{ $name_lang_lower }} </span>
        </p>
        @foreach($option['choices'] as $ko => $choice)
            @isset($choice['variant_number'])
                @php($variant = $product->variables[$choice['variant_number']])
                @php($price = $variant['defaultDisplayedPrice'])
            @else
                @if($choice['priceModifierType'] == 'ABSOLUTE' && !isset($choice['variant_number']))
                    @php($price = $choice['priceModifier'])
                @elseif($choice['priceModifierType'] == 'PERCENT' && !isset($choice['variant_number']))
                    @php($price = round(($product->price * $choice['priceModifier'] / 100), 2))
                @else
                    @php($price = $product->price)
                @endif
            @endisset


            @if(!empty($option['nameTranslated'][$lang]))
                @php($info = $option['nameTranslated'][$lang])
            @else
                @php($info = $name)
            @endif

            @if(!empty($choice['textTranslated'][$lang]))
                @php($info .= ' ' . $choice['textTranslated'][$lang])
            @else
                @php($info .= ' ' . $choice['text'])
            @endif

            @isset($choice['metrics'])
                @foreach($choice['metrics'] as $metric)
                    @isset($metric[$lang])
                        @php($info .= ' ' . $metric[$lang])
                    @endisset
                @endforeach
            @endisset

            <label class=" option_value" data-infosize="{{ $info }}"
                   data-option_text="{{ $info }}"
                   data_id="{{ $product->id }}-{{ $key }}-{{ $ko }}"
                   data-price="{{ $price }}"
                   data_url="{{ $rout }}"
                   data-option_key="{{ $key }}-{{ $ko }}"
                   data-option_value="{{ $ko }}"
                   @isset($choice['variant_number'])
                   data-variant_number="{{ $choice['variant_number'] }}"
                   data-pricemodifier="{{ $variant['defaultDisplayedPrice'] }}"
                   data-pricemodifiertype="VARIANT_PRICE"
                   @else
                   data-variant_number=""
                   data-pricemodifier="{{ $choice['priceModifier'] }}"
                   data-pricemodifiertype="{{ $choice['priceModifierType'] }}"
                    @endisset

            >
                <div class="option-info option_text">
                    <p>
                        <input type="checkbox" name="{{ $option['name'] }}" @if($size) disabled="true" @endif>
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

                        @isset($choice['description'][$lang])
                            <span>
                                                {{ $choice['description'][$lang] }}
                                            </span>
                        @endisset
                        @isset($choice['metrics'])
                            @foreach($choice['metrics'] as $metric)
                                @isset($metric[$lang])
                                    <span>
                                                    <pre class="weight-params">{{ $metric[$lang] }}</pre>
                                                </span>
                    @endisset
                    @endforeach
                    @else
                        <pre class="weight-params"> </pre>
                    @endisset

                    +<span class="price">{{ $price }}</span> ₪
                    </p>
                </div>

            </label>
        @endforeach

    </div>
@elseif ($option['type'] == 'SELECT')

    @isset($option['choices'])
        <div class="product-info__size product-info__add select product_option" data_optiontype="{{ $option['type'] }}">
            <div class="product-size open-size-table">
                <span>{{ __('shop.Выберите') }} {{ $name_lang_lower }} </span>
            </div>
            <div class="product-size__table">
                @foreach($option['choices'] as $ko => $choice)
                    @php($variant = false)
                    @isset($choice['variant_number'])
                        @php($variant = $product->variables[$choice['variant_number']])
                        @php($price = $variant['defaultDisplayedPrice'])
                    @else
                        @if($choice['priceModifierType'] == 'ABSOLUTE' && !isset($choice['variant_number']))
                            @php($price = $choice['priceModifier'])
                        @elseif($choice['priceModifierType'] == 'PERCENT' && !isset($choice['variant_number']))
                            @php($price = round(($product->price * $choice['priceModifier'] / 100), 2))
                        @else
                            @php($price = $product->price)
                        @endif
                    @endisset

                    @if(!empty($option['nameTranslated'][$lang]))
                        @php($info = $option['nameTranslated'][$lang])
                    @else
                        @php($info = $name)
                    @endif

                    @if(!empty($choice['textTranslated'][$lang]))
                        @php($info .= ' ' . $choice['textTranslated'][$lang])
                    @else
                        @php($info .= ' ' . $choice['text'])
                    @endif

                    @isset($choice['metrics'])
                        @foreach($choice['metrics'] as $metric)
                            @isset($metric[$lang])
                                @php($info .= ' ' . $metric[$lang])
                            @endisset
                        @endforeach
                    @endisset

                    <label class="product-size-var option_value" data-infosize="{{ $info }}"
                           data-option_text="{{ $info }}"
                           data_id="{{ $product->id }}-{{ $key }}-{{ $ko }}"
                           data-price="{{ $price }}"
                           data_url="{{ $rout }}"
                           data-option_key="{{ $key }}-{{ $ko }}"
                           data-option_value="{{ $ko }}"
                           @isset($choice['variant_number'])
                           data-variant_number="{{ $choice['variant_number'] }}"
                           data-pricemodifier="{{ $variant['defaultDisplayedPrice'] }}"
                           data-pricemodifiertype="VARIANT_PRICE"
                           @else
                           data-variant_number=""
                           data-pricemodifier="{{ $choice['priceModifier'] }}"
                           data-pricemodifiertype="{{ $choice['priceModifierType'] }}"
                            @endisset

                    >


                        <img src="/assets/images/icons/{{ $name_lower }}{{ $ko+1 }}.png" alt="">
                        <div class="option-info option_text">
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

                                @isset($choice['description'][$lang])
                                    <span>
                                                {{ $choice['description'][$lang] }}
                                            </span>
                                @endisset
                            </p>
                            <div>
                                @isset($choice['metrics'])
                                    @foreach($choice['metrics'] as $metric)
                                        @isset($metric[$lang])
                                            <span>
                                                    <pre class="weight-params">{{ $metric[$lang] }}</pre>
                                                </span>
                                        @endisset
                                    @endforeach
                                @else
                                    <pre class="weight-params"> </pre>
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
    @endisset
@else
    {{--            <p>--}}
    {{--                {{ $option['type'] }}--}}
    {{--            </p>--}}
@endif
