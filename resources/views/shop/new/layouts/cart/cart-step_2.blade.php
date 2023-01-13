
    <div class="pay step_{{ $step }}" id="cart">
        <div class="pay__header">
            <div class="active">01 <span>{{ __('shop-cart.КОНТАКТНАЯ ИНФОРМАЦИЯ') }}</span></div>
            <div class="active">02 <span>{{ __('shop-cart.ДОСТАВКА') }}</span></div>
            <div >03 <span>{{ __('shop-cart.ОПЛАТА') }}</span></div>
        </div>


        @error('order_data')
        <p class="errors">{{ $message }}</p><p></p>
        @enderror

        @error('min_summ_order')
        <p class="errors">{{ $message }}</p><p></p>
        @enderror

        <label class="delivery">
            <input type="radio" name="delivery" value="delivery" checked form="form">
            <span>{{ __('shop-cart.Доставка по Израилю') }}</span>
        </label>
        <label class="pickup">
            <input type="radio" name="delivery" value="pickup"  form="form">
            <span>{{ __('shop-cart.Самовывоз по адресу') }}</span>
        </label>

        <div class="pay__form">
            <form id="form" class="form-cart{{ $step }}" action="{{ route("cart", ['lang' => $lang, 'step' => 3]) }}" method="POST">
                @csrf

                <input hidden name="lang" value="{{ $lang }}">
                @if($lost_order)
                    <input hidden name="gClientId" value="{{ $order_data['gClientId'] }}">
                @else
                    <input hidden name="gClientId" value="">
                @endif

                @if(!empty($order_number) && $order_number != 'undefined')
                    <input hidden name="order_id" value="{{ $order_number }}">
                @else
                    <input hidden name="order_id" >
                @endif

                @if($lost_order)
                    <input hidden name="delivery_method" value="{{ $order_data['delivery_method'] }}">
                @else
                    <input hidden name="delivery_method" value="{{ old('delivery_method') }}">
                @endif

                <div class="delivery">
                    <div class="calendar-wrapper calendar_box">
                        <div class="calendar_table hidden"></div>
                    </div>
                    <label class="@if($errors->has('date')) errors @endif">
                        <p>{{ __('shop-cart.Дата доставки') }}  *</p>
                        <input class="show_calendar date"
                               required autocomplete="off"
                               placeholder="{{ __('shop-cart.Выберите дату доставки') }}"
                               data-text_delivery="{{ __('shop-cart.Выберите дату доставки') }}"
                               data-text_pickup="{{ __('shop-cart.Выберите дату самовывоза') }}"
                               type="text" name="date" readonly value="{{ old('date') }}">
                        @error('date')
                        <p class="errors">{{ $message }}</p>
                        @enderror
                    </label>


                    <label class="@if($errors->has('time')) errors @endif">
                        <p class="delivery">{{ __('shop-cart.Выбрать время доставки') }}</p>
                        <p class="pickup" style="display: none;">{{ __('shop-cart.Выбрать время самовывоза') }}</p>

                        <input class="delivery_time" type="text" name="time" value="{{ old('time') }}" placeholder="{{ __('shop-cart.Укажите удобное вам время') }}" readonly>
                        <ul class="delivery_time city-lis">
                            <li class="default" data-time="">
{{--                                {{ __('shop-cart.любое время') }}--}}
                            </li>
                        </ul>


                        @error('time')
                        <p class="errors">{{ $message }}</p>
                        @enderror
                    </label>

                </div>

                <p class="other-man">
                    <label>
                        <input type="checkbox" name="otherPerson" value="otherPerson" @if(old('otherPerson')) checked @endif>
                        {{ __('shop-cart.Заказ для другого человека') }}
                    </label>
                </p>
                <div @if(!old('otherPerson')) style="display: none; @endif ">
                    <input hidden class="phone" name="phoneOtherPerson">
                    <label class="phone-mask @if($errors->has('phone')) errors @endif" for="">
                        <p>
                            {{ __('shop-cart.Телефон') }} *
                        </p>
                    </label>

                    @error('phoneOtherPerson')
                    <p class="errors">{{ $message }}</p>
                    @enderror

                    <label class="@if($errors->has('nameOtherPerson')) errors @endif" for="">
                        <p>
                            {{ __('shop-cart.Имя') }} *
                        </p>
                        <input type="text" name="nameOtherPerson">
                    </label>

                    @error('nameOtherPerson')
                    <p class="errors">{{ $message }}</p>
                    @enderror
                </div>

                <div class="delivery_address">

                    <label class="@if($errors->has('city')) errors @endif @if($errors->has('city_id')) errors @endif" for="">
                        <input type="hidden" name="city_id" value="{{ old('city_id') }}">
                        <p>
                            {{ __('shop-cart.Город') }} *
                        </p>
                        <input class="city_name" type="text" name="city"  value="{{ old('city') }}">

                        @error('city')
                        <p class="errors">{{ $message }}</p>
                        @enderror
                    </label>

                    @error('city_id')
                        <p class="errors">{{ $message }}</p>
                    @enderror
                    <label class="@if($errors->has('street')) errors @endif" for="">
                        <p>
                            {{ __('shop-cart.Улица') }} *
                        </p>
                        <input type="text" name="street"  value="{{ old('street') }}">

                        @error('street')
                        <p class="errors">{{ $message }}</p>
                        @enderror
                    </label>
                    <div>
                        <label class="@if($errors->has('house')) errors @endif" for="">
                            <p>
                                {{ __('shop-cart.Дом') }} *
                            </p>
                            <input type="text" name="house"  value="{{ old('house') }}">

                            @error('house')
                            <p class="errors">{{ $message }}</p>
                            @enderror
                        </label>
                        <label for="">
                            <p>
                                {{ __('shop-cart.Квартира') }}
                            </p>
                            <input type="text" name="flat"  value="{{ old('flat') }}">
                            @error('flat')
                            <p class="errors">{{ $message }}</p>
                            @enderror
                        </label>
                    </div>
                    <div>
                        <label for="">
                            <p>
                                {{ __('shop-cart.Этаж') }}
                            </p>
                            <input type="text" name="floor"  value="{{ old('floor') }}">
                        </label>
                        <label for="">
                            <p>
                                {{ __('shop-cart.Код подьезда') }}
                            </p>
                            <input type="text" name="house_code"  value="{{ old('house_code') }}">
                        </label>
                    </div>
                </div>

                <div>
                    @include('shop.new.layouts.cart.order_data')
                </div>

                <div class="pay__acttion">
                    <button>
                        <a href="{{ route('cart', ['lang' => $lang, 'step' => 1, $lost_order]) }}">
                        </a>
                        {{ __('shop.Назад') }}
                    </button>
                    <button class="main-btn go-pay" type="submit">
                        {{ __('shop-cart.продолжить оформление') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>

    </script>