<?php
$translate = [
    'title' => [
        'ru' => 'Наше сладкое предложение дня',
        'en' => 'Our sweet offer of the day'
    ],
    'ingredients' => [
        'ru' => 'Соста',
        'en' => 'Ingredients'
    ],
    'energy' => [
        'ru' => 'Энергетическая ценность на 100г',
        'en' => 'Energy value per 100g'
    ],
    'kcal' => [
        'ru' => 'Ккал',
        'en' => 'Kcal'
    ],
    'proteins' => [
        'ru' => 'Белки',
        'en' => 'Proteins'
    ],
    'fat' => [
        'ru' => 'Жиры',
        'en' => 'Fat'
    ],
    'carbohydrates' => [
        'ru' => 'Углеводы',
        'en' => 'Carbohydrates'
    ],
    'more details' => [
        'ru' => 'подробнее',
        'en' => 'more details'
    ],
    'all desserts' => [
        'ru' => 'все десерты',
        'en' => 'all desserts'
    ],
    'from' => [
    'ru' => 'от',
    'en' => 'from'
]
];
?>

@if (!empty($dey_offer_data))
    @php($offer_id = $dey_offer_data['id'])
    @php($dey_offer = $products[$offer_id])
    <h2 class="offer__title blockTitle">{{ $translate['title'][$lang] }}</h2>
    <div class="offer__product">
        @php($dey_offer_category = $categories[$dey_offer->category_id])

        <img class="offer__productImg" src="{{ $dey_offer->image['image400pxUrl'] }}" title="{{ $dey_offer_category->name }} - {{ $dey_offer->name }}" alt="{{ $dey_offer->name }}">

        <div class="offer__productBody">
            <div class="offer__productTitle">
                @if (!empty($dey_offer->translate['nameTranslated'][$lang]))
                    {{ $dey_offer->translate['nameTranslated'][$lang] }}
                @else
                    {{ $dey_offer->name }}
                @endif
            </div>
            <div class="description__price">
                @if(!empty($dey_offer->variables))
                    @if (sizeof($dey_offer->variables) == 1)

                        @foreach($dey_offer->variables as $kv => $variant)

                            @isset($variant['compareToPrice'])
                                <div class="description__priceItem description__priceItem--old">
                                    <span class="description__priceItemNumber">{{ $variant['compareToPrice'] }}</span>
                                    <span class="description__priceItemUnit">₪</span>
                                </div>

                                <div class="description__priceItem description__priceItem--current description__priceItem--new">
                                    <span class="description__priceItemNumber">{{ $variant['defaultDisplayedPrice'] }}</span>
                                    <span class="description__priceItemUnit">₪</span>
                                </div>
                            @else
                                <div class="description__priceItem description__priceItem--current">
                                    <span class="description__priceItemNumber">{{ $dey_offer->price }}</span>
                                    <span class="description__priceItemUnit">₪</span>
                                </div>
                            @endisset

                        @endforeach
                    @else
                        <div class="description__priceItem description__priceItem--current">
                            <span class="description__priceItemText">{{ $translate['from'][$lang] }}&nbsp</span>
                            <span class="description__priceItemNumber">{{ $dey_offer->price }}</span>
                            <span class="description__priceItemUnit">₪</span>
                        </div>
                    @endif
                @else
                    @if(!empty($dey_offer->compareToPrice))
                        <div class="description__priceItem description__priceItem--old">
                            <span class="description__priceItemNumber">{{ $dey_offer->compareToPrice }}</span>
                            <span class="description__priceItemUnit">₪</span>
                        </div>

                        <div class="description__priceItem description__priceItem--current description__priceItem--new">
                            <span class="description__priceItemNumber">{{ $dey_offer->price }}</span>
                            <span class="description__priceItemUnit">₪</span>
                        </div>
                    @else
                        <div class="description__priceItem description__priceItem--current">
                            <span class="description__priceItemText">{{ $translate['from'][$lang] }}&nbsp</span>
                            <span class="description__priceItemNumber">{{ $dey_offer->price }}</span>
                            <span class="description__priceItemUnit">₪</span>
                        </div>
                    @endif
                @endif
            </div>


            @isset($dey_offer_data['title'][$lang])
                <div class="offer__productSubtitle blockText">{{ $dey_offer_data['title'][$lang] }}</div>
            @endisset
            <div class="offer__productSubtitle blockText">{!! $dey_offer->translate['descriptionTranslated'][$lang] !!}</div>
            <div class="offer__productComponents blockText">
                @isset($dey_offer->data['attributes']['composition'][$lang])
                    <p>
                        <span>{{ $translate['ingredients'][$lang] }}:</span>
                        {{ $dey_offer->data['attributes']['composition'][$lang] }}
                    </p>
                @endisset
            </div>
            @isset($dey_offer->data['attributes']['calories'])
                <div class="offer__productEnergy blockText hidden"><span class="offer__productEnergyTitle">{{ $translate['energy'][$lang] }}:</span>
                    <ul>
                        <li>{{ $translate['kcal'][$lang] }}: {{ $dey_offer->data['attributes']['calories']['calories'] }}</li>
                        <li>{{ $translate['proteins'][$lang] }}: {{ $dey_offer->data['attributes']['calories']['protein'] }} г</li>
                        <li>{{ $translate['fat'][$lang] }}: {{ $dey_offer->data['attributes']['calories']['fat'] }} г</li>
                        <li>{{ $translate['carbohydrates'][$lang] }}: {{ $dey_offer->data['attributes']['calories']['carbohydrate'] }} г</li>
                    </ul>
                </div>
            @endisset

            <div class="offer__productSize hidden">
                @if (!empty($dey_offer->variables))
                    @foreach($dey_offer->variables as $variant)
                        @if (!empty($variant['options']))
                            @foreach($variant['options'] as $option)
                                <div class="offer__productSizeItem blockText">
                                    <div class="offer__productSizeItemTitle">
                                        @php($name = $option['name'])
                                        @php($value = $option['value'])
                                        {{ $dey_offer->options['map'][$name]['nameTranslated'][$lang] }}
                                        {{ $dey_offer->options['map'][$option['name']]['choices'][$value]['textTranslated'][$lang] }}
                                    </div>
                                    <span>&nbsp;-&nbsp;</span>
                                    @if (!empty($variant['compareToPrice']))
                                        <div class="offer__productSizeItemPriceBlock offer__productSizeItemPriceBlock--old">
                                            <span class="offer__productSizeItemPrice--old">{{ $variant['compareToPrice'] }}</span>
                                            <span class="offer__productSizeItemUnit">₪</span>
                                        </div>

                                        <span>&nbsp;-&nbsp;</span>
                                    @endif
                                    <div class="offer__productSizeItemPriceBlock">
                                        <span class="offer__productSizeItemPrice">{{ $variant['defaultDisplayedPrice'] }}</span>
                                        <span class="offer__productSizeItemUnit">₪</span>
                                    </div>
                                </div>

                            @endforeach
                        @endif
                    @endforeach
                @endif
            </div>
            <div class="offer__productBtns">
                <a class="offer__productBtn offer__productBtnMore blockBtn blockBtn--bgc" href="{{ route('product_'.$lang, ['category' => $category->slag, 'product' => $dey_offer->slag]) }}">
                    {{ $translate['more details'][$lang] }}
                </a>
                <a class="offer__productBtn offer__productBtnAll blockBtn blockBtn--transparent" href="./#anchor-select">
                    {{ $translate['all desserts'][$lang] }}
                </a>
            </div>
        </div>
    </div>
@endif
