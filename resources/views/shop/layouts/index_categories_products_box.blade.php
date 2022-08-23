<?php
$translite = [
    'in_stock' => [
        'en' => 'In stock',
        'ru' => 'В наличии'
    ]
]
?>

{{--@if ($category_active == 'all')--}}
{{--    <button class="select__headerItem hoverUnderline active hidden" data-type="all">all</button>--}}
{{--@else--}}
{{--    <button class="select__headerItem hoverUnderline hidden" data-type="all">all</button>--}}
{{--@endif--}}
@if ($category_active == 'have')
    <button class="select__headerItem hoverUnderline hidden active" data-type="have">{{ $translite['in_stock'][$lang] }}</button>
@else
    <button class="select__headerItem hoverUnderline hidden" data-type="have">{{ $translite['in_stock'][$lang] }}</button>
@endif


@foreach($categories as $category)
    @php($translate = json_decode($category->translate, true))

    <a class="select__headerItem hoverUnderline
        @if ($category_active == 'all' && $loop->first)
            active
        @elseif ($category_active == $category->slag)
            active
        @endif
        "  data-type="{{ $category->name }}" href="{{ route("category_$lang", ['category' => $category->slag]) }}" >
        @if (!empty($translate['nameTranslated'][$lang]))
            {{ $translate['nameTranslated'][$lang] }}
        @else
            {{ $category->name }}
        @endif
    </a>

@endforeach
