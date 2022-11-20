
    <div class="pay step_{{ $step }}" id="cart">
        <div class="pay__header">
            <div class="active">01 <span>{{ __('shop-cart.КОНТАКТНАЯ ИНФОРМАЦИЯ') }}</span></div>
            <div >02 <span>{{ __('shop-cart.ДОСТАВКА') }}</span></div>
            <div >03 <span>{{ __('shop-cart.ОПЛАТА') }}</span></div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger" style="color: brown;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="pay__form">
            <form class="form-cart{{ $step }}" action="{{ route("cart", ['lang' => $lang, 'step' => 2]) }}" method="POST">
                @csrf
                <input hidden name="lang" value="{{ $lang }}">
                <input hidden name="gClientId" value="{{ $lang }}">
                <label class="phone-mask" for="" class="@error('phone') error @enderror">
                    <input class="phone" hidden name="phone" value="">
                    <p>
                        {{ __('shop-cart.Телефон') }} *
                    </p>
                </label>
                <div>
                    <label for="" class="@error('clientName') error @enderror">
                        <p>
                            {{ __('shop-cart.Имя') }} *
                        </p>
                        <input required type="text" name="clientName" value="firstname">
                    </label>
                    <label for="" class="@error('clientLastName') error @enderror">
                        <p>
                            {{ __('shop-cart.Фамилия') }} *
                        </p>
                        <input required type="text" name="clientLastName" value="lastname">
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
                    <input required type="email" name="email" value="test@mail.ru">
                </label>

                <span>{{ __('shop-cart.Согласен с') }} <a href="#">{{ __('shop-cart.политикой конфиденциальности') }}</a></span>
                <div class="pay__acttion">
                    <button>{{ __('shop-cart.Вернуться к товарам') }}</button>
                    <button class="main-btn go-pay" type="submit">
                        {{ __('shop-cart.продолжить оформление') }}
                    </button>
                </div>
                <input class="order_data" type="hidden" name="order_data">
            </form>
        </div>
    </div>
