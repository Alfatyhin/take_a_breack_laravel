

@extends('shop.new.cart-master')

@section('title')
    @parent
@stop

@section('head')

   @parent

@stop

@section('content')

    @parent
    <div class="pay">
        <div class="pay__header">
            <div class="active">01 <span>КОНТАКТНАЯ ИНФОРМАЦИЯ</span></div>
            <div >02 <span>ДОСТАВКА</span></div>
            <div >03 <span>ОПЛАТА</span></div>
        </div>
        <!-- <div class="pay__title">
            <h2>
                форма оплаты заказа
            </h2>
        </div> -->
        <div class="pay__form">
            <form action="#">
                <!-- <label>
                    <p>Введите сумму оплаты:</p>
                    <input type="number">
                </label> -->
                <!-- <p>
                    Заполните данные для оплаты:
                </p> -->
                <label class="phone-mask" for="">
                    <select name="phone-code">
                        <option value="+49">+49</option>
                        <option value="+47">+47</option>
                        <option value="+412">+412</option>
                    </select>
                    <p>
                        Телефон *
                    </p>
                    <input class="phone-mask-input" placeholder="00-00-000 (000)" type="tel" name="user-phone">
                </label>
                <div>
                    <label for="">
                        <p>
                            Имя *
                        </p>
                        <input type="text">
                    </label>
                    <label for="">
                        <p>
                            Фамилия *
                        </p>
                        <input type="text">
                    </label>
                </div>

                <label for="">
                    <p>
                        Дата Рождения
                    </p>
                    <input type="date">
                </label>
                <label for="">
                    <p>
                        Email *
                    </p>
                    <input type="email">
                </label>
                <!-- <p>
                    Выберите способ оплаты:
                </p>
                <div class="pay-type">
                    <label><input checked name="pay-type" type="radio"><span></span><p>Кредитная карта</p></label>
                    <label><input name="pay-type" type="radio"><span></span><p>Pay Pal</p></label>
                    <label><input name="pay-type" type="radio"><span></span><p>BIT</p></label>
                    <label><input name="pay-type" type="radio"><span></span><p>Наличные</p></label>
                </div>
                <p>
                    Поддержите наш проект чаевыми:
                </p>
                <div class="pay-tips">
                    <label><input value="0" name="pay-tips" type="radio"><span></span><p>0%</p></label>
                    <label><input value="10" checked name="pay-tips" type="radio"><span></span><p>10%</p></label>
                    <label><input value="12" name="pay-tips" type="radio"><span></span><p>12%</p></label>
                    <label><input value="15" name="pay-tips" type="radio"><span></span><p>15%</p></label>
                </div>
                <p class="total-pay">
                    Общая сумма к оплате:  <span class="total-num">199</span> ₪
                </p> -->
                <span>Согласен с <a href="#">политикой конфиденциальности</a></span>
                <span>Согласен с <a href="#">политикой конфиденциальности</a></span>
                <div class="pay__acttion">
                    <button>Вернуться к товарам</button>
                    <button class="main-btn go-pay">
                        Оплатить
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="pay-cart">
        <div class="pay-cart__box">
            <div class="pay-cart__title">
                                <span>
                                    Ваш заказ
                                </span>
            </div>
            <div class="pay-cart__items">
            </div>
            <div class="pay-cart__promo">
                <input placeholder="ВВЕДИТЕ ПРОМОКОД" type="text">
                <button class="submit-promo">
                    <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M32.6202 11.5476C32.5342 11.3912 32.4053 11.2626 32.2486 11.177C32.092 11.0914 31.9142 11.0522 31.7361 11.0642L9.83474 12.4687C9.64508 12.4835 9.4644 12.5556 9.31671 12.6755C9.16902 12.7954 9.0613 12.9574 9.00786 13.14C8.95442 13.3226 8.9578 13.5171 9.01753 13.6977C9.07726 13.8783 9.19053 14.0365 9.34229 14.1512L16.1679 19.4995L23.5472 15.4466L24.4446 17.0866L17.0356 21.1405L17.8687 29.7753C17.8863 29.9623 17.9597 30.1396 18.0794 30.2843C18.199 30.429 18.3593 30.5345 18.5395 30.5871C18.7225 30.6366 18.9161 30.6295 19.0949 30.5667C19.2737 30.5039 19.4293 30.3885 19.5411 30.2355L32.5327 12.5477C32.6406 12.4064 32.7061 12.2374 32.7216 12.0604C32.7371 11.8833 32.7019 11.7055 32.6202 11.5476Z" fill="#222222"/>
                    </svg>
                </button>
            </div>
            <div class="pay-cart__title">
                                <span>
                                    добавить к заказу
                                </span>
            </div>
            <div class="pay-cart__items">
                <div class="pay-cart__item">
                    <img src="assets/images/cart-image.png" alt="">
                    <div class="pay-cart__item-info">
                        <p>
                            Малиновый торт с нежным бисквитом
                        </p>
                        <span>
                                            Вес: 600г
                                        </span>
                        <div class="product-info__count">
                            <button class="product-info-decrement">-</button>
                            <input class="product-info-count-input" value="1" type="number" name="product-count">
                            <button class="product-info-increment">+</button>
                        </div>
                    </div>
                    <div class="pay-cart__item-price">
                                        <span>
                                            19 ₪
                                        </span>
                    </div>
                </div>
            </div>
            <div class="pay-cart__item-add">
                <button class="trans-btn">добавить к заказу</button>
            </div>
            <div class="pay-cart__info">
                <p>
                    <span>Сумма</span>
                    <span>199 ₪</span>
                </p>
                <p>
                    <span>скидка</span>
                    <span>рассчитывается на следующем шаге</span>
                </p>
                <p>
                    <span>Доставка</span>
                    <span>рассчитывается на следующем шаге</span>
                </p>
            </div>
        </div>
        <div class="pay-cart__total-sum">
                            <span>
                                сумма к оплате
                            </span>
            <span>
                                199 ₪
                            </span>
        </div>
    </div>
@stop
