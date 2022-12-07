
    <div class="pay step_{{ $step }}" id="cart">
        <div class="pay__header">
            <div class="active">01 <span>{{ __('shop-cart.КОНТАКТНАЯ ИНФОРМАЦИЯ') }}</span></div>
            <div >02 <span>{{ __('shop-cart.ДОСТАВКА') }}</span></div>
            <div >03 <span>{{ __('shop-cart.ОПЛАТА') }}</span></div>
        </div>

        @include("shop.new.layouts.cart.errors")

        <div class="pay__form">
            <form class="form-cart{{ $step }}" action="{{ route("cart", ['lang' => $lang, 'step' => 2]) }}" method="POST">
                @csrf
                <input hidden name="lang" value="{{ $lang }}">
                <input hidden name="gClientId" value="">
                <input hidden name="order_id" value="{{ $order_number }}">
                <label class="phone-mask" for="" class="@error('phone') error @enderror">
                    <input hidden class="phone" name="phone" value="">
                    <p>
                        {{ __('shop-cart.Телефон') }} *
                    </p>
                </label>
                <div>
                    <label for="" class="@error('clientName') error @enderror">
                        <p>
                            {{ __('shop-cart.Имя') }} *
                        </p>
                        <input required type="text" name="clientName" value="">
                    </label>
                    <label for="" class="@error('clientLastName') error @enderror">
                        <p>
                            {{ __('shop-cart.Фамилия') }} *
                        </p>
                        <input required type="text" name="clientLastName" value="">
                    </label>
                </div>

                <label for="" class="@error('clientBirthDay') error @enderror">
                    <p>
                        {{ __('shop-cart.Дата Рождения') }}
                    </p>
                    <input type="date" name="clientBirthDay">
                </label>
                <label for="" class="@error('email') error @enderror">
                    <p>
                        {{ __('shop-cart.Email') }} *
                    </p>
                    <input required type="email" name="email" value="">
                </label>

                <span>{{ __('shop-cart.Согласен с') }} <a href="#">{{ __('shop-cart.политикой конфиденциальности') }}</a></span>

                <div>

                    <input class="order_data" type="hidden" name="order_data">
                    @error('order_data')
                    <p class="errors">error get products data</p>
                    @enderror
                </div>
                <div class="pay__acttion">
                    <button>
                        <a href="{{ url()->previous() }}">
                        </a>
                        {{ __('shop-cart.Вернуться к товарам') }}
                    </button>
                    <button class="main-btn go-pay" type="submit">
                        {{ __('shop-cart.продолжить оформление') }}
                    </button>
                </div>
            </form>
        </div>
    </div>