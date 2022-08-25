<!-- оболочка корзины -->
<script>
        @if (old('methodPay'))
    var methodPay = true;
        @else
    var methodPay = false;
    @endif
    var deivery_data = @json($delivery);
    // console.log(deivery_data);
</script>

<div class="popup-wrap">
    <button class="close-popup"></button>
    <!-- затемняющий фон -->
    <div class="popup__back"></div>
    <!-- затемняющий фон -->

    <!-- Корзина -->
    <div class="popup__item popup-cart">
        <div class="popup__title">
            <p>
                Ваш заказ
            </p>
        </div>

        <!-- Список покупок -->
        <div class="popup__product-list">


            <!-- Пример вывода -->

            <div class="popup__product-item">
                <img src="img/common/products/cakes/crazy_berry_cake/1.jpg" alt="">
                <div class="product-item__info">
                    <p class="prod-name">
                        Торт Клубничное Сердце
                    </p>
                    <span class="prod-size">
                        размер: 1шт.
                    </span>
                </div>
                <div class="product-item__count">
                    <button>-</button>
                    <span class="prod-count">2</span>
                    <button>+</button>
                </div>
                <div class="product-item__price">
                    <span>300₪</span>
                </div>
                <div class="product-item__delete">
                    <button>X</button>
                </div>
            </div>

        </div>
        <!-- итоговая сумму -->
        <div class="popup__total">
            <p>
                Сумма: <span class="listProduct__itemAllTotal listProduct__itemFooterText">544₪</span>
            </p>
        </div>
        <div class="popup__subtitle">
            <p>
                Добавить к заказу
            </p>
        </div>
        <div class="popup__product-item">
            <img src="img/common/products/cakes/crazy_berry_cake/1.jpg" alt="">
            <div class="product-item__info">
                <p class="prod-name">
                    Торт Клубничное Сердце
                </p>
                <span class="prod-size">
                    размер: 1шт.
                </span>
            </div>
            <div class="product-item__count">
                <button>+</button>
            </div>
            <div class="product-item__price">
                <span>300₪</span>
            </div>
            <div class="product-item__delete">
                <button>X</button>
            </div>
        </div>
        <div class="popup__subtitle">
            <p>
                Оформить заказ
            </p>
        </div>
        <div class="popup__form">
            <form action="#">
                <div class="form-row">
                    <label class="order__clientInfoInputWrapper showBlock">
                        <p>Телефон заказчика *</p>
                        <input required class="blockTextField new-phone  validatePhone showBlock" type="tel" name="phone" data-text="Номер телефона">
                    </label>
                </div>
                <div class="form-row">
                    <label class="order__clientInfoInputWrapper showBlock">
                        <p>Ваше имя*</p>
                        <input required class="blockTextField showBlock validateText" type="text" name="clientName"  data-text="Имя">
                    </label>
                    <label class="order__clientInfoInputWrapper showBlock">
                        <p>Ваша Фамилия*</p>
                        <input required class="blockTextField showBlock validateText" type="text" name="clientName" data-text="Имя">
                    </label>
                </div>
                <div class="form-row">
                    <label class="order__clientInfoInputWrapper showBlock">
                        <p>Ваш Email</p>
                        <input  class="blockTextField showBlock validateText" type="email" name="email"  data-text="Email">
                    </label>
                </div>
                <div class="ppblock">
                    <label class="pp-info">
                        <input required checked type="checkbox" name="pp-conf" id="">
                        <span></span>
                        <p>согласие с <a href="#">Политикой конфиденциальности</a></p>
                    </label>
                    <label class="pp-info">
                        <input checked type="checkbox" name="pp-conf" id="">
                        <span></span>
                        <p>согласие на получение рассылки</p>
                    </label>
                </div>
                <label class="circle-input">
                    <input  type="checkbox" name="other-man" id="">
                    <span></span>
                    <p>Заказываю другому человеку</p>
                </label>
                <div class="form-row">
                    <label class="circle-input">
                        <input checked class="adress-form" type="radio" value="1" name="type-delievry">
                        <span></span>
                        <p>Доставка</p>
                    </label>
                    <label class="circle-input">
                        <input class="adress-form" type="radio" value="0" name="type-delievry">
                        <span></span>
                        <p>Самовывоз</p>
                    </label>
                </div>
                <div class="form-row">
                    <label >
                        <p>Дата*</p>
                        <input required type="date">
                    </label>
                </div>
                <div style="flex-direction: column;" class="form-row">
                    <label class="circle-input">
                        <input class="time-to-del" type="checkbox" name="other-man" id="">
                        <span></span>
                        <p>Выбрать время доставки (+30% к стоимости доставки)</p>
                    </label>
                    <label class="hidden-input">
                        <p>Время доставки *</p>
                        <select name="time-delivery">
                            <option value="none time"></option>
                            <option value="10">10:00</option>
                            <option value="11">11:00</option>
                            <option value="12">12:00</option>
                            <option value="13">13:00</option>
                            <option value="14">14:00</option>
                            <option value="15">15:00</option>
                            <option value="16">16:00</option>
                            <option value="17">17:00</option>
                            <option value="18">18:00</option>
                            <option value="19">19:00</option>
                            <option value="20">20:00</option>
                            <option value="21">21:00</option>
                        </select>
                    </label>
                </div>
                <div class="adress-block">
                    <div class="form-row">
                        <label >
                            <p>Город *</p>
                            <input required name="city" list="cityname" type="text">
                            <datalist id="cityname">
                                <!-- сюда вывести города -->
                                <option value="Город1"></option>
                                <option value="Город2"></option>
                                <option value="Город3"></option>
                            </datalist>
                        </label>
                    </div>
                    <div class="form-row">
                        <label >
                            <p>Улица *</p>
                            <input class="only-text-num" required name="street" type="text">
                        </label>
                    </div>
                    <div class="form-row">
                        <label >
                            <p>Дом *</p>
                            <input class="only-text-num" required name="home-nubmer" type="text">
                        </label>
                        <label >
                            <p>Квартира</p>
                            <input class="only-text-num" name="city" type="text">
                        </label>
                    </div>
                    <div class="form-row">
                        <label >
                            <p>Этаж</p>
                            <input name="city" type="text">
                        </label>
                        <label >
                            <p>Код от подъезда</p>
                            <input name="city" type="text">
                        </label>
                    </div>
                </div>
                <div class="form__pay-method">
                    <label class="circle-input small-text">
                        <input type="radio" name="paymethod">
                        <span></span>
                        <p>Кредитная карта</p>
                    </label>
                    <label class="circle-input small-text">
                        <input type="radio" name="paymethod">
                        <span></span>
                        <p>paypal</p>
                    </label>
                    <label class="circle-input small-text">
                        <input type="radio" name="paymethod">
                        <span></span>
                        <p>bit</p>
                    </label>
                    <label class="circle-input small-text">
                        <input type="radio" name="paymethod">
                        <span></span>
                        <p>Наличные</p>
                    </label>
                </div>
                <div class="tipsblock">
                    <p>
                        Поддержите наш проект чаевыми
                    </p>
                    <div>
                        <label class="circle-input">
                            <input type="radio" name="tips-size">
                            <span></span>
                            <p>0%</p>
                        </label>
                        <label class="circle-input">
                            <input type="radio" name="tips-size">
                            <span></span>
                            <p>10%</p>
                        </label>
                        <label class="circle-input">
                            <input type="radio" name="tips-size">
                            <span></span>
                            <p>15%</p>
                        </label>
                        <label class="circle-input">
                            <input type="radio" name="tips-size">
                            <span></span>
                            <p>20%</p>
                        </label>

                        <span class="total">
                        54.4₪
                      </span>
                    </div>
                </div>
                <div class="promocode">
                    <input class="listProduct__promoCode blockTextField" type="text" placeholder="Введите промокод" value="promocode">
                    <button class="listProduct__promoBtn blockBtn blockBtn--bgc">Применить промокод</button>
                </div>
                <div class="popup__comment">
                    <p>
                        Комментарий к заказу
                    </p>
                    <textarea type="text" name="comment" placeholder="Напишите сюда ваш комментарий"></textarea>
                </div>
                <div class="popup__action">
                    <div>
                        <p>
                            Итоговая сумма к оплате:
                        </p>
                        <span class="total-price-cart">662.4₪</span>
                    </div>
                    <button type="submit">ОФОРМИТЬ ЗАКАЗ</button>
                </div>
            </form>
            <div class="order__listAreasPrices" hidden>
                @include('shop.layouts.cart.city_price')
            </div>
        </div>
    </div>
</div>
