
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

                @include('shop.new.layouts.cart.input_hidden')

                <label class="@if (!$lost_order)phone-mask @endif" for="" class="@error('phone') error @enderror">
                    <p>
                        {{ __('shop-cart.Телефон') }} *
                    </p>

                    @if ($lost_order)
                        <input type="text" class="phone" name="phone" value="{{ isset($order_data['phone']) ? $order_data['phone'] : '' }}">
                    @else
                        <input hidden class="phone" name="phone" value="">
                    @endif


                </label>
                <div>
                    <label for="" class="@error('clientName') error @enderror">
                        <p>
                            {{ __('shop-cart.Имя') }} *
                        </p>

                        @if ($lost_order)
                            <input required type="text" name="clientName" value="{{ isset($order_data['clientName']) ? $order_data['clientName'] : '' }}">
                        @else
                            <input required type="text" name="clientName" value="">
                        @endif

                    </label>
                    <label for="" class="@error('clientLastName') error @enderror">
                        <p>
                            {{ __('shop-cart.Фамилия') }} *
                        </p>
                        @if ($lost_order)
                            <input required type="text" name="clientLastName" value="{{ isset($order_data['clientLastName']) ? $order_data['clientLastName'] : '' }}">
                        @else
                            <input required type="text" name="clientLastName" value="">
                        @endif
                    </label>
                </div>

                <label for="" class="@error('clientBirthDay') error @enderror">
                    <p>
                        {{ __('shop-cart.Дата Рождения') }}
                    </p>

                    @if ($lost_order)
                        <input type="text" class="phone" name="clientBirthDay" value="{{ isset($order_data['clientBirthDay']) ? $order_data['clientBirthDay'] : '' }}">
                    @else
                        <input type="date" name="clientBirthDay">
                    @endif

                </label>
                <label for="" class="@error('email') error @enderror">
                    <p>
                        {{ __('shop-cart.Email') }} *
                    </p>

                    @if ($lost_order)
                        <input  required type="email" value="{{ isset($order_data['email']) ? $order_data['email'] : '' }}">
                    @else
                        <input required type="email" name="email" value="">
                    @endif
                </label>

                <span>{{ __('shop-cart.Согласен с') }} <a href="#">{{ __('shop-cart.политикой конфиденциальности') }}</a></span>

                <div>
                    @include('shop.new.layouts.cart.order_data')
                </div>

                <div class="pay__acttion">
                    <button>
                        <a href="{{ route('index', ['lang' => $lang]) }}">
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
