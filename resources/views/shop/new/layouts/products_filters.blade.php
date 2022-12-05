@php($rout_name = Route::currentRouteName())

<ul class="header__row header-bottom">
    <li>
        @if ($lang == 'en')
            @php($url = route('index'))
        @else
            @php($url = route('index', ['lang' => $lang]))
        @endif
        <a class="@if($filter == 'all') active @endif" href="{{ $url }}">
            {{ __('shop-header.все десерты') }}
        </a>
    </li>
    <li>
        @if ($lang == 'en')
            @php($url = route('index_filter_en', ['filter' => 'in_stock']))
        @else
            @php($url = route('index_filter', ['lang' => $lang, 'filter' => 'in_stock']))
        @endif
        <a class="@if($filter == 'in_stock') active @endif" href="{{ $url }}">
            {{ __('shop-header.в наличии сегодня') }}
        </a>
    </li>
    <li>
        @if ($lang == 'en')
            @php($url = route('index_filter_en', ['filter' => 'sale']))
        @else
            @php($url = route('index_filter', ['lang' => $lang, 'filter' => 'sale']))
        @endif
        <a class="@if($filter == 'sale') active @endif" href="{{ $url }}">
            {{ __('shop-header.товары по акции') }}
        </a>
    </li>
</ul>

