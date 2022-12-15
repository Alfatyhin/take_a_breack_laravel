

<div class="pay short_order">

    <div class="pay__title">
        <h2>
            форма оплаты заказа
        </h2>
    </div>


    @error('order_data')
    <p class="errors">{{ $message }}</p><p></p>
    @enderror


    <div class="pay__form">

        <form class="form-car_short_order" action="{{ route("new_short_order", ['lang' => $lang]) }}" method="POST">
            @csrf

            <input hidden name="lang" value="{{ $lang }}">
            <input hidden name="gClientId" value="">

            <label>
                <p>Введите сумму оплаты:</p>
                <input type="number" name="order_price" required>
            </label>
            <p>
                Заполните данные для оплаты:
            </p>

            <div>
                <label class="" for="" class="@error('phone') error @enderror">
                    <p>
                        {{ __('shop-cart.Телефон') }} *
                    </p>
                    <input type="tel" class="phone" name="phone" value="+972"  pattern="(\s*)?(\+)?([- _():=+]?\d[- _():=+]?){11,14}(\s*)?">
                </label>
                <label for="" class="@error('clientName') error @enderror">
                    <p>
                        {{ __('shop-cart.Имя') }} *
                    </p>
                    <input required type="text" name="clientName" value="">
                </label>
            </div>
            <label for="" class="@error('email') error @enderror">
                <p>
                    {{ __('shop-cart.Email') }} *
                </p>
                <input required type="email" name="email" value="">
            </label>
            <label for="" style="display: none;" class="@error('clientBirthDay') error @enderror">
                <p>
                    {{ __('shop-cart.Дата Рождения') }}
                </p>
                <input type="hidden" name="clientBirthDay">
            </label>

            <p>
                {{ __('shop-cart.Выберите способ оплаты') }}
            </p>
            <div class="pay-types">
                <label>
                    <input checked name="methodPay" type="radio" value="1">
                    <span></span>
                    <p class="text">{{ __('shop-cart.Кредитная карта') }}</p>
                </label>
                <label>
                    <input name="methodPay" type="radio" value="3">
                    <span></span>
                    <p class="text">PayPal</p>
                </label>
                <label><input name="methodPay" type="radio" value="4">
                    <span></span>
                    <p class="text">BIT</p>
                </label>
                <label>
                    <input name="methodPay" type="radio" value="2">
                    <span></span>
                    <p class="text">{{ __('shop-cart.Наличные') }}</p>
                </label>
            </div>
            <p>
                {{ __('shop-cart.Поддержите наш проект чаевыми') }}
            </p>
            <div class="pay-tips">
                <label><input value="0" name="premium" type="radio"><span></span><p>0%</p></label>
                <label><input value="10" checked name="premium" type="radio"><span></span><p>10%</p></label>
                <label><input value="12" name="premium" type="radio"><span></span><p>12%</p></label>
                <label><input value="15" name="premium" type="radio"><span></span><p>15%</p></label>
            </div>
            <p class="total-pay"><span class="order_price" style="display: none;"></span>
                {{ __('shop-cart.Общая сумма к оплате') }}
                <span class="total_order_price"></span> ₪
            </p>


            <div class="pay__acttion">

                <button class="main-btn go-pay" type="submit"
                        data-text_pay="{{ __('shop-cart.Оплатить') }}"
                        data-text_checkout="{{ __('shop-cart.оформить заказ') }}">
                    {{ __('shop-cart.Оплатить') }}
                </button>
            </div>
        </form>
    </div>
</div>
