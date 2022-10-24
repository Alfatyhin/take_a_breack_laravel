
<div class="category">
    <button class="close-menu-btn"></button>
    <p>
        <img src="/assets/images/icons/category.svg" alt="">
    </p>
    <ul>
        @foreach($categories as $category)
            @php($translate = json_decode($category->translate, true))
            <li>
                <a href="{{ route("category_$lang", ['category' => $category->slag]) }}"
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
        <li>
            <a href="#">
                О нас
            </a>
        </li>
        <li>
            <a href="#">
                Доставка и Оплата
            </a>
        </li>
        <li>
            <a href="#">
                Отзывы
            </a>
        </li>
        <li>
            <a href="#">
                Контакты
            </a>
        </li>
    </ul>
</div>
