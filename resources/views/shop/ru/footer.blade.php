<footer class="footer">
    <div class="container">
        <div class="footer__body">
            <div class="footer__info"><a class="footer__logo" href="{{ route('index_'.$lang) }}"><img src="/img/common/logo.webp" alt="logo"><span>TAKE A BREAK</span></a>
                <div class="footer__infoContainer"><a class="hoverUnderline hoverUnderline--white footer__phone" href="https://wa.me/9720559475812">+972 055-947-5812</a>
                    <address class="footer__address">Израиль,<br>Emanuel Ringelblum 3, Holon</address>
                </div>
            </div>
            <div class="footer__menuContainer">
                <div class="footer__menu footer__menu--menu">
                    <div class="footer__menuTitle">МЕНЮ</div>
                    <div class="footer__menuBody">
                        <ul>
                            <li><a href="{{ route('index_'.$lang) }}">ГЛАВНАЯ</a></li>
                            <li class="footer__menuBody--about"><a href="{{ route('index_'.$lang) }}#anchor-about">О НАС</a></li>
                            <li class="footer__menuBody--dessert"><a href="{{ route('index_'.$lang) }}#anchor-select">ДЕСЕРТЫ</a></li>
                            <li><a href="{{ route('index_'.$lang) }}#anchor-advantage">ПОЧЕМУ МЫ</a></li>
                            <li><a href="{{ route('index_'.$lang) }}#anchor-feedback">ОТЗЫВЫ</a></li>
                            <li><a href="{{ route('index_'.$lang) }}#anchor-info">ДОСТАВКА И ОПЛАТА</a></li>
                            <li><a href="{{ route('index_'.$lang) }}#anchor-contact">КОНТАКТЫ</a></li>
                        </ul>
                    </div>
                </div>
                <div class="footer__menu footer__menu--dessert">
                    <div class="footer__menuTitle">ДЕСЕРТЫ</div>
                    <div class="footer__menuBody">
                        <ul>
                            @foreach($categories as $category)
                                @php($translate = json_decode($category->translate, true))
                                <li><a href="{{ route("category_$lang", ['category' => $category->slag]) }}" data-type="{{ $category->name }}">{{ $translate['nameTranslated'][$lang] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="footer__social">
                    <div class="footer__menu footer__menu--social">
                        <div class="footer__menuTitle">СОЦ. СЕТИ</div>
                        <div class="footer__menuBody">
                            <ul>
                                <li><a href="https://www.instagram.com/takeabreak_desserts/">INSTAGRAM</a></li>
                                <li><a href="https://www.facebook.com/TABdesserts/">FACEBOOK</a></li>
                            </ul>
                        </div>
                    </div><a class="footer__orderBtn" href="{{ route('index_'.$lang) }}#anchor-select">ЗАКАЗАТЬ ДЕСЕРТЫ</a>
                </div>
            </div>
        </div>
    </div>
</footer>
