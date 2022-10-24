
<header class="header">
    <div class="container">
        <div class="header__body">
            <div class="header__mobile-btn">
                <button class="menu-btn">
                    <span></span>
                </button>
            </div>
            <div class="header__login">
                <a href="{{ route("cart_$lang") }}"><img src="/assets/images/icons/bag.svg" alt=""></a>
{{--                <a href="#"><img src="/assets/images/icons/user.svg" alt=""></a>--}}
            </div>
            <div class="header__title">
                <a href="{{ route("index_$lang") }}">
                    <img src="/assets/images/logo.png" alt="">
                </a>
            </div>
            <nav class="header__nav">
                <ul class="header__row header-top">
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
                <ul class="header__row header-bottom">
                    <li>
                        <a class="active" href="#">
                            Все
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            В наличии
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            Под заказ
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>
