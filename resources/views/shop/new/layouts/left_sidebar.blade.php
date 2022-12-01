
<div class="category">
    <button class="close-menu-btn"></button>
    <p>
        <img src="/assets/images/icons/category.svg" alt="">
    </p>
    <ul>
        @foreach($categories as $item)
            @php($translate = json_decode($item->translate, true))
            <li>
                @if($lang == 'en')
                    @php($rout = route("category_index", ['category' => $item->slag]))
                @else
                    @php($rout = route("category", ['lang' => $lang, 'category' => $item->slag]))
                @endif
                <a class="@if(isset($category) && $category->slag == $item->slag) active @endif" href="{{ $rout }}"
                   data-type="{{ $item->slag }}">
                    @isset($translate['nameTranslated'][$lang])
                        {{ $translate['nameTranslated'][$lang] }}
                    @else
                        {{ $item->name }}
                    @endisset
                </a>
            </li>
        @endforeach
    </ul>
    <ul class="for-mobile">
        @include('shop.new.layouts.nav-menu')
    </ul>
</div>
