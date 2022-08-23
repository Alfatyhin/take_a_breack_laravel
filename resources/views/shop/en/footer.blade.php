<footer class="footer">
    <div class="container">
        <div class="footer__body">
            <div class="footer__info">
                <a class="footer__logo" href="{{ route("index_$lang") }}/">
                    <img src="/img/common/logo.webp" alt="logo">
                    <span>TAKE A BREAK</span>
                </a>
                <div class="footer__infoContainer">
                    <a class="hoverUnderline hoverUnderline--white footer__phone" href="https://wa.me/9720559475812">+972 055-947-5812</a>
                    <address class="footer__address">Israel,<br>Emanuel Ringelblum 3, Holon</address>
                </div>

            </div>
            <div class="footer__menuContainer">
                <div class="footer__menu footer__menu--menu">
                    <div class="footer__menuTitle">MENU</div>
                    <div class="footer__menuBody">
                        <ul>
                            <li><a href="{{ route("index_$lang") }}/#anchor-welcome">HOME</a></li>
                            <li class="footer__menuBody--about"><a href="{{ route("index_$lang") }}#anchor-about">ABOUT US</a></li>
                            <li class="footer__menuBody--dessert"><a href="{{ route("index_$lang") }}#anchor-select">DESSERT</a></li>
                            <li><a href="{{ route("index_$lang") }}#anchor-advantage">WHY WE</a></li>
                            <li><a href="{{ route("index_$lang") }}#anchor-feedback">REVIEWS</a></li>
                            <li><a href="{{ route("index_$lang") }}#anchor-info">DELIVERY AND PAYMENT</a></li>
                            <li><a href="{{ route("index_$lang") }}#anchor-contact">CONTACTS</a></li>
                        </ul>
                    </div>
                </div>
                <div class="footer__menu footer__menu--dessert">
                    <div class="footer__menuTitle">DESSERT</div>
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
                        <div class="footer__menuTitle">SOC. NETWORKS</div>
                        <div class="footer__menuBody">
                            <ul>
                                <li><a href="https://www.instagram.com/takeabreak_desserts/">INSTAGRAM</a></li>
                                <li><a href="https://www.facebook.com/TABdesserts/">FACEBOOK</a></li>
                            </ul>
                        </div>
                    </div><a class="footer__orderBtn" href="{{ route("index_$lang") }}#anchor-select">ORDER DESSERTS</a>
                </div>
            </div>
        </div>
    </div>
</footer>
