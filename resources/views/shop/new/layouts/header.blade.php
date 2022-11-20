
<header class="header">
    <div class="container">
        <div class="header__body">
            <div class="header__mobile-btn">
                <button class="menu-btn">
                    <span></span>
                </button>
            </div>
            <ul class="lang_select mark_lang">
                @include('shop.new.layouts.components.lang-select')
            </ul>
            <div class="header__login">

                <a href="{{ route("cart", ['lang' => $lang]) }}" class="mark-link">
                    <img src="/assets/images/icons/bag.svg" alt="">
                    @include('shop.new.layouts.components.bag-badge')
                </a>
                <a href="#" class="mark-link"><img src="/assets/images/icons/user.svg" alt=""></a>

            </div>
            <div class="header__title">
                @if ($lang == 'en')

                    <a href="{{ route("index") }}">
                        @else

                            <a href="{{ route("index", ['lang' => $lang]) }}">
                                @endif
                    <img src="/assets/images/logo.png" alt="">
                </a>
            </div>
            <nav class="header__nav">
                <ul class="header__row header-top">
                   @include('shop.new.layouts.nav-menu')
                </ul>
                @section('product_filter')

                @show
            </nav>
        </div>
    </div>
</header>
