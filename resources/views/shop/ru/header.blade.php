<header class="header">
    <div class="header__bg"></div>
    <div class="container">
        <div class="header__body"><a class="header__logo" href="{{ route("index_$lang") }}"><img src="/img/common/logo.webp" alt="logo"><span>TAKE A BREAK</span></a>
            <div class="header__middleBlock">
                <nav class="header__nav">
                    <ul>
{{--                        <li><a href="{{ route("index_$lang") }}">ГЛАВНАЯ </a></li>--}}
                        <li class="header__navItem--dessert"><a href="#">ДЕСЕРТЫ</a></li>
                        <li><a href="{{ route("index_$lang") }}/#anchor-about">О НАС</a></li>
                        <li><a href="{{ route("index_$lang") }}/#anchor-info">ДОСТАВКА И ОПЛАТА</a></li>
                        <li><a href="{{ route("index_$lang") }}/#anchor-feedback">ОТЗЫВЫ</a></li>
                        <li><a href="{{ route("index_$lang") }}/#anchor-contact">КОНТАКТЫ</a></li>
                    </ul>
                    <div class="header__navSelectList header__navSelectList--dessert openingBlock">
                        <ul>
{{--                            <li><a href="{{ route("index_$lang", ['category' => 'have']) }}#anchor-select" data-type="have">В наличии</a></li>--}}
                            @foreach($categories as $category)
                                @php($translate = json_decode($category->translate, true))
                                <li><a href="{{ route("category_$lang", ['category' => $category->name]) }}" data-type="{{ $category->slag }}">{{ $translate['nameTranslated'][$lang] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </nav>
                <div class="header__bottomBlock">
                    <address class="header__address">Израиль, Emanuel Ringelblum 3, Holon</address>
                    <a class="header__phone" href="https://wa.me/9720559475812" target="_blank">
                        <img src="/img/common/whatsapp.png" alt="whatsapp"><span>+972 55-947-5812</span>
                    </a>
{{--                    <button class="header__btnEnter">войти</button>--}}
                    <div class="header__contacts">
                        <a class="hoverIcon" href="https://www.instagram.com/takeabreak_desserts/" target="_blank"><img src="/img/common/inst.png" alt="instagram"></a>
                        <a class="hoverIcon" href="https://www.facebook.com/TABdesserts/" target="_blank"><img src="/img/common/facebook.png" alt="facebook"></a></div>
                </div>
            </div>
            <div class="header__rightBlock">
                <div class="header__language">
                    <a href="{{ route('index_en') }}" >EN</a>
                </div>
                <div class="header__cart">
                    <span class="count"></span>
                    <a class="cartBtnOpen" href="{{ route("cart_$lang") }}"><img src="/img/header/cart.png" alt="cart"></a>
                </div>
            </div><a class="header__burgerIcon burgerIcon" href=""><span></span></a>
        </div>
    </div>
    <div class="header__burgerContainer"><a class="header__burgerClose" href=""><span></span></a>
        <div class="header__burgerBody">
            <div class="header__burgerMenu menu">
                <ul>
{{--                    <li><a href="{{ route("index_$lang") }}/#anchor-welcome">ГЛАВНАЯ </a></li>--}}
                    <li><a href="{{ route("index_$lang") }}/#anchor-select">ДЕСЕРТЫ</a></li>
                    <li><a href="{{ route("index_$lang") }}/#anchor-about">О НАС</a></li>
                    <li><a href="{{ route("index_$lang") }}/#anchor-info">ДОСТАВКА И ОПЛАТА</a></li>
                    <li><a href="{{ route("index_$lang") }}/#anchor-feedback">ОТЗЫВЫ</a></li>
                    <li><a href="{{ route("index_$lang") }}/#anchor-contact">КОНТАКТЫ</a></li>
                </ul>
            </div>
            <div class="header__socIcons">
                <a href="https://www.instagram.com/takeabreak_desserts/" target="_blank"><img src="/img/common/inst-white.png" alt="instagram"></a>
                <a href="https://www.facebook.com/TABdesserts/" target="_blank"><img src="/img/common/facebook-white.png" alt="facebook"></a>
            </div>
        </div>
    </div>
</header>
