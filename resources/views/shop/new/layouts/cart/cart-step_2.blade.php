
    <div class="pay step_{{ $step }}" id="cart">
        <div class="pay__header">
            <div class="active">01 <span>{{ __('shop-cart.КОНТАКТНАЯ ИНФОРМАЦИЯ') }}</span></div>
            <div class="active">02 <span>{{ __('shop-cart.ДОСТАВКА') }}</span></div>
            <div >03 <span>{{ __('shop-cart.ОПЛАТА') }}</span></div>
        </div>

{{--        <div class="pay-dop-info">--}}
            <label class="pickup">
                <input type="radio" name="delivery" value="delivery" checked>
                <span>{{ __('shop-cart.Доставка по Израилю') }}</span>
            </label>
            <label class="pickup">
                <input type="radio" name="delivery" value="pickup" >
                <span>{{ __('shop-cart.Самовывоз по адресу') }}</span>
            </label>
{{--        </div>--}}

        <div class="pay__form">
            <form class="form-cart{{ $step }}" action="{{ route("cart", ['lang' => $lang, 'step' => 2]) }}" method="POST">
                @csrf

                <div class="delivery">
                    <label>
                        <p>{{ __('shop-cart.Дата доставки') }}  *</p>
                        <input required placeholder="{{ __('shop-cart.Выберите дату доставки') }}" type="date" name="date">
                    </label>

                    <label>
                        <p>{{ __('shop-cart.Выбрать время доставки') }}</p>
                        <input  type="time" name="time">
                    </label>
                </div>

                <p>
                    <label>
                        <input type="checkbox" name="otherPerson" value="otherPerson">
                        {{ __('shop-cart.Заказ для другого человека') }}
                    </label>
                </p>
                <div style="display: none;">
                    <input hidden class="phone" name="phoneOtherPerson">
                    <label class="phone-mask" for="">
                        <p>
                            {{ __('shop-cart.Телефон') }} *
                        </p>
                    </label>
                    <label for="">
                        <p>
                            {{ __('shop-cart.Имя') }} *
                        </p>
                        <input type="text" name="nameOtherPerson">
                    </label>
                </div>

                <div class="delivery_address">

                    <label for="">
                        <input type="hidden" name="city_id">
                        <p>
                            {{ __('shop-cart.Город') }} *
                        </p>
                        <input class="city_name" type="text" name="city" >
                    </label>
                    <label for="">
                        <p>
                            {{ __('shop-cart.Улица') }} *
                        </p>
                        <input type="text" name="street">
                    </label>
                    <div>
                        <label for="">
                            <p>
                                {{ __('shop-cart.Дом') }} *
                            </p>
                            <input type="text" name="house">
                        </label>
                        <label for="">
                            <p>
                                {{ __('shop-cart.Квартира') }} *
                            </p>
                            <input type="text" name="flat">
                        </label>
                    </div>
                    <div>
                        <label for="">
                            <p>
                                {{ __('shop-cart.Этаж') }}
                            </p>
                            <input type="text" name="floor">
                        </label>
                        <label for="">
                            <p>
                                {{ __('shop-cart.Код подьезда') }}
                            </p>
                            <input type="text" name="house_code">
                        </label>
                    </div>
                </div>

                <div class="pay__acttion">
                    <button>
                        <a href="{{ route('cart', ['lang' => $lang]) }}">
                            {{ __('shop.Назад') }}
                        </a>
                    </button>
                    <button class="main-btn go-pay" type="submit">
                        {{ __('shop-cart.продолжить оформление') }}
                    </button>
                </div>
                <input class="order_data" type="hidden" name="order_data">
            </form>
        </div>
    </div>
