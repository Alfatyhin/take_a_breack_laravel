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
                <h1 class="welcome__title">natural author's desserts for you</h1>
                <div class="welcome__subtitle">The highest quality ingredients are the most important thing in our work</div>
                <div class="welcome__btns">
                    <a class="welcome__selectDessertBtn welcome__btn blockBtn blockBtn--bgc" href="#anchor-select">choose a dessert</a>
                    {{--                        <a class="welcome__fastOrderBtn welcome__btn blockBtn blockBtn--transparent" href="./fast_order.html">quick order</a>--}}
                </div>
            </div>
        </div>
    </div>
</section>
