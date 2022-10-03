
<div class="product">
    <div class="product__body">
        @include("shop.new.layouts.product_cart.galery")
        <div class="product__info product-info">
            <div class="product-info__title">
                <h1>
                    @if (!empty($product->translate['nameTranslated'][$lang]))
                        {{ $product->translate['nameTranslated'][$lang] }}
                    @else
                        {{ $product->name }}
                    @endif
                </h1>
            </div>
            <div class="product-info__price">
                <span>
                    @if(!empty($product->variables) && sizeof($product->variables) > 1)
                        от
                    @endif
                    {{ $product->price }}
                </span>

            </div>
            @include("shop.new.layouts.product_cart.options")
            <div class="product-info__add">
                <p>добавить Надпись для торта</p>
                <div>
                    <label>
                                            <span>
                                                Текст для торта
                                            </span>
                        <input placeholder="Happy Birthday" type="text">
                    </label>
                    <span>
                                            5 ₪
                                        </span>
                    <button class="trans-btn">Добавить</button>
                </div>
            </div>
            <div class="product-info__action">
                <div class="product-info__count">
                    <button class="product-info-decrement">-</button>
                    <input class="product-info-count-input" value="1" type="number" name="product-count">
                    <button class="product-info-increment">+</button>
                </div>
                <button class="main-btn go-to-cart">Добавить в корзину</button>
            </div>
            <div class="product-info__edge swiper">
                <div class="product-info__edge-wrapper swiper-wrapper">
                    <div class="product-info__edge-item swiper-slide">
                        <img src="assets/images/icons/include.png" alt="">
                    </div>
                    <div class="product-info__edge-item swiper-slide">
                        <img src="assets/images/icons/milk.png" alt="">
                    </div>
                    <div class="product-info__edge-item swiper-slide">
                        <img src="assets/images/icons/sugar.png" alt="">
                    </div>
                    <div class="product-info__edge-item swiper-slide">
                        <img src="assets/images/icons/vegan.png" alt="">
                    </div>
                </div>
                <div class="product-info__edge-nav">
                    <div class="edge-prev">
                        <svg width="6" height="9" viewBox="0 0 6 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.63895 7.68324C4.66283 7.68324 4.71058 7.68324 4.73445 7.65936C4.7822 7.61161 4.7822 7.53999 4.73445 7.49223L1.46355 4.22133L4.73445 0.974302C4.7822 0.926551 4.7822 0.854926 4.73445 0.807175C4.6867 0.759425 4.61507 0.759425 4.56732 0.807175L1.22479 4.1497C1.17704 4.19746 1.17704 4.26908 1.22479 4.31683L4.56732 7.65936C4.56732 7.68324 4.61507 7.68324 4.63895 7.68324Z" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <div class="edge-next">
                        <svg width="6" height="9" viewBox="0 0 6 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.63895 7.68324C4.66283 7.68324 4.71058 7.68324 4.73445 7.65936C4.7822 7.61161 4.7822 7.53999 4.73445 7.49223L1.46355 4.22133L4.73445 0.974302C4.7822 0.926551 4.7822 0.854926 4.73445 0.807175C4.6867 0.759425 4.61507 0.759425 4.56732 0.807175L1.22479 4.1497C1.17704 4.19746 1.17704 4.26908 1.22479 4.31683L4.56732 7.65936C4.56732 7.68324 4.61507 7.68324 4.63895 7.68324Z" stroke-width="1.5"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="product-info__text">
                <p>
                    Итальянская классика в веганском варианте. Это торт потрясающе нежный и воздушный. Многие любят его за кофейную пропитку. Неудивительно! Ведь  вкус эспрессо кроется в нежных кремовых прослойках, которые тают во рту и напоминают мороженое.
                    <br> Тирамису подходит для тех, кто любит не слишком сладкие десерты, но при этом с легкой горчинкой какао и вкусом кофе.
                </p>
            </div>
            <div class="product-info__tabs">
                <div class="product-info__tabs-btns">
                    <button class="product-info__tabs-btn">Состав</button>
                    <button class="product-info__tabs-btn">Калорийность</button>
                    <button class="product-info__tabs-btn">Хранение</button>
                </div>
                <div class="product-info__tab-text">
                    <p>
                        <b>В составе десерта следующие ингридиенты:</b> грецкие орехи, финики, эспрессо, органическое кокосовое масло, чистый экстракт ванили, морская соль, кешью, органический кленовый сироп, темный кленовый сироп, какао-порошок, какао-масло, миндальное молоко.
                    </p>
                </div>
                <div class="product-info__tab-text">
                    <p>
                        <b>В составе десерта следующие ингридиенты:</b> грецкие орехи, финики, эспрессо, органическое кокосовое масло, чистый экстракт ванили, морская соль, кешью, органический кленовый сироп, темный кленовый сироп, какао-порошок, какао-масло, миндальное молоко.
                    </p>
                </div>
                <div class="product-info__tab-text">
                    <p>
                        <b>В составе десерта следующие ингридиенты:</b> грецкие орехи, финики, эспрессо, органическое кокосовое масло, чистый экстракт ванили, морская соль, кешью, органический кленовый сироп, темный кленовый сироп, какао-порошок, какао-масло, миндальное молоко.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="recblock">
        <div class="recblock__title">
            <h3>
                Добавить к заказу
            </h3>
        </div>
        <div class="recblock__items">
            <a href="#" class="recblock__item">
                <div class="item-img">
                    <img src="assets/images/rec.jpg" alt="">
                </div>
                <div class="item__text">
                    <p>
                        Свеча для торта
                    </p>
                    <span>
                                            3 ₪
                                        </span>
                </div>
            </a>
            <a href="#" class="recblock__item">
                <div class="item-img">
                    <img src="assets/images/rec.jpg" alt="">
                </div>
                <div class="item__text">
                    <p>
                        Свеча для торта
                    </p>
                    <span>
                                            3 ₪
                                        </span>
                </div>
            </a>
            <a href="#" class="recblock__item">
                <div class="item-img">
                    <img src="assets/images/rec.jpg" alt="">
                </div>
                <div class="item__text">
                    <p>
                        Свеча для торта
                    </p>
                    <span>
                                            3 ₪
                                        </span>
                </div>
            </a>
            <a href="#" class="recblock__item">
                <div class="item-img">
                    <img src="assets/images/rec.jpg" alt="">
                </div>
                <div class="item__text">
                    <p>
                        Свеча для торта
                    </p>
                    <span>
                                            3 ₪
                                        </span>
                </div>
            </a>
        </div>
    </div>
</div>

