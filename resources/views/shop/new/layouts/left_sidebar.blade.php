
<div class="category">
    <button class="close-menu-btn"></button>
    <p>
        <img src="/assets/images/icons/category.svg" alt="">
    </p>
    <ul>
        <li>
            <a href="">
                {{ __('shop-left_sidebar.все') }}
            </a>
        </li>
        @foreach($categories as $category)
            @php($translate = json_decode($category->translate, true))
            <li>
                @if($lang == 'en')
                    @php($rout = route("category_index", ['category' => $category->slag]))
                @else
                    @php($rout = route("category", ['lang' => $lang, 'category' => $category->slag]))
                @endif
                <a href="{{ $rout }}"
                   data-type="{{ $category->slag }}">
                    @isset($translate['nameTranslated'][$lang])
                        {{ $translate['nameTranslated'][$lang] }}
                    @else
                        {{ $category->name }}
                    @endisset
                </a>
            </li>
        @endforeach
    </ul>
    <ul class="for-mobile">
        @include('shop.new.layouts.nav-menu')
    </ul>
</div>
