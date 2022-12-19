
    <div class="pay step_{{ $step }}" id="cart">
        <div class="pay__header">
            <div class="active">01 <span>{{ __('shop-cart.КОНТАКТНАЯ ИНФОРМАЦИЯ') }}</span></div>
            <div class="active">02 <span>{{ __('shop-cart.ДОСТАВКА') }}</span></div>
            <div class="active">03 <span>{{ __('shop-cart.ОПЛАТА') }}</span></div>
        </div>

        @if(env('APP_NAME') == 'Take a Break Server')
            <h3>Order {{ $order_number }}</h3>
        @endif

        @error('order_data')
        <p class="errors">{{ $message }}</p><p></p>
        @enderror

        <h3>{{ __('shop-cart.Ваш заказ') }} #{{ $order_number }}
        </h3>

        <div class="pay__form">

            <form class="form-cart{{ $step }}" action="{{ route("new_order", ['lang' => $lang]) }}" method="POST">
                @csrf

                <input hidden name="lang" value="{{ $lang }}">
                <input hidden name="gClientId" value="">
                @if(!empty($order_number) && $order_number != 'undefined')
                    <input hidden name="order_id" value="{{ $order_number }}">
                @else
                    <input hidden name="order_id" >
                @endif

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
                <p class="total-pay"><span class="order_price" style="display: none;">{{ $order_data['order_data']['order_total'] }}</span>
                    {{ __('shop-cart.Общая сумма к оплате') }}
                    <span class="total_order_price">{{ round($order_data['order_data']['order_total'] + $order_data['order_data']['order_total'] * 0.1, 1) }}</span> ₪
                </p>

                <div>
                    <p>{{ __('shop-cart.Комментарий к заказу') }}</p>
                    <textarea name="client_comment" class="order__comment blockTextField" ></textarea>
                </div>

                <div>
                  @include('shop.new.layouts.cart.order_data')
                </div>

                <div class="pay__acttion">
                    <button>
                        <a href="{{ route('cart', ['lang' => $lang, 'step' => 2, $lost_order]) }}">
                        </a>
                        {{ __('shop.Назад') }}
                    </button>
                    <button class="main-btn go-pay" type="submit"
                            data-text_pay="{{ __('shop-cart.Оплатить') }}"
                            data-text_checkout="{{ __('shop-cart.оформить заказ') }}">
                        {{ __('shop-cart.Оплатить') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
