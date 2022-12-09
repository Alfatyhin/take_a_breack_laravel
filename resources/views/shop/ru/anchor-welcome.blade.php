<section class="welcome" id="anchor-welcome">
    <div class="welcome__bgShadow"></div>
    <div class="container">
        <div class="welcome__body">
            @if(!empty($banner[$lang]))
                <div class="welcome__banner">
                    {{ $banner[$lang] }}
                </div>
            @endif

            <div class="welcome__container">
                <h1 class="welcome__title">натуральные авторские десерты для вас</h1>
                <div class="welcome__subtitle">Ингредиенты самого высокого качества - вот, что является самым важным в нашей работе</div>
                <div class="welcome__btns">
                    <a class="welcome__selectDessertBtn welcome__btn blockBtn blockBtn--bgc" href="#anchor-select">выбрать десерт</a>
                    {{--                        <a class="welcome__fastOrderBtn welcome__btn blockBtn blockBtn--transparent" href="./fast_order.html">быстрый заказ</a>--}}
                </div>
            </div>
        </div>
    </div>
</section>
