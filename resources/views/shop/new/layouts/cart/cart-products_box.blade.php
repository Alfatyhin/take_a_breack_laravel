
<div class="pay-cart step_{{ $step }}">
    <div class="pay-cart__box">

        <div class="pay-cart__title">
            <span>
                {{ __('shop-cart.Ваш заказ') }}
            </span>
            <span class="order_number_data" style="display: none;">#<span class="order_number">{{ $order_number }}</span></span>
        </div>
        <div class="pay-cart__items">
        </div>
        <div class="pay-cart__promo">
            <input placeholder="{{ __('shop-cart.ВВЕДИТЕ ПРОМОКОД') }}" type="text">
            <button class="submit-promo" data_url="{{ route('check_promo_code', ['promoCode' => 'promocode']) }}">
                <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M32.6202 11.5476C32.5342 11.3912 32.4053 11.2626 32.2486 11.177C32.092 11.0914 31.9142 11.0522 31.7361 11.0642L9.83474 12.4687C9.64508 12.4835 9.4644 12.5556 9.31671 12.6755C9.16902 12.7954 9.0613 12.9574 9.00786 13.14C8.95442 13.3226 8.9578 13.5171 9.01753 13.6977C9.07726 13.8783 9.19053 14.0365 9.34229 14.1512L16.1679 19.4995L23.5472 15.4466L24.4446 17.0866L17.0356 21.1405L17.8687 29.7753C17.8863 29.9623 17.9597 30.1396 18.0794 30.2843C18.199 30.429 18.3593 30.5345 18.5395 30.5871C18.7225 30.6366 18.9161 30.6295 19.0949 30.5667C19.2737 30.5039 19.4293 30.3885 19.5411 30.2355L32.5327 12.5477C32.6406 12.4064 32.7061 12.2374 32.7216 12.0604C32.7371 11.8833 32.7019 11.7055 32.6202 11.5476Z" fill="#222222"/>
                </svg>
            </button>
        </div>
        <div class="pay-cart__promo_info">
            <p class="sugess_promo hide">
                {{ __('shop-cart.промокод') }} <span class="promo_code_text"></span> {{ __('shop-cart.применен') }}
            </p>
            <p class="error_promo hide">
                {{ __('shop-cart.промокод') }}  <span class="promo_code_text"></span> {{ __('shop-cart.не найден') }}
            </p>
        </div>
        <div class="pay-cart__info">
            <p>
                <span> {{ __('shop-cart.сумма') }}</span>
                <span ><span id="total-ammount"></span>  ₪</span>
            </p>
            <p>
                <span>{{ __('shop-cart.скидка') }}</span>
                <span><span class="discount">0</span> ₪</span>
            </p>
            <p>
                <span>{{ __('shop-cart.доставка') }}</span>
                <span class="delivery_price">{{ __('shop-cart.рассчитывается на следующем шаге') }}</span>
            </p>
        </div>
    </div>
    <div class="pay-cart__total-sum">
        <span>
            {{ __('shop-cart.сумма к оплате') }}
        </span>
        <span><span id="summ-for-payment"></span> ₪</span>
    </div>
</div>
