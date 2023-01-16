
    <div class="pay step_{{ $step }}" id="cart">
        <div class="pay__header">
            <div class="active">01 <span>{{ __('shop-cart.КОНТАКТНАЯ ИНФОРМАЦИЯ') }}</span></div>
            <div class="active">02 <span>{{ __('shop-cart.ДОСТАВКА') }}</span></div>
            <div >03 <span>{{ __('shop-cart.ОПЛАТА') }}</span></div>
        </div>


        @include("shop.new.layouts.cart.errors")

        <label class="delivery">
            @if ($lost_order)
                <input type="radio" name="delivery" value="delivery" @if($order_data['delivery'] == 'delivery') checked @endif form="form">
            @else
                <input type="radio" name="delivery" value="delivery" checked form="form">
            @endif
            <span>{{ __('shop-cart.Доставка по Израилю') }}</span>
        </label>
        <label class="pickup">
            @if ($lost_order)
                <input type="radio" name="delivery" value="pickup" @if($order_data['delivery'] == 'pickup') checked @endif   form="form">
            @else
                <input type="radio" name="delivery" value="pickup"  form="form">
            @endif
            <span>{{ __('shop-cart.Самовывоз по адресу') }}</span>
        </label>

        <div class="pay__form">
            <form id="form" class="form-cart{{ $step }}" action="{{ route("cart", ['lang' => $lang, 'step' => 3]) }}" method="POST">
                @csrf

                @include('shop.new.layouts.cart.input_hidden')


                <input hidden name="delivery_method" value="{{ old('delivery_method') }}">

                <div class="delivery">
                    <div class="calendar-wrapper calendar_box">
                        <div class="calendar_table hidden"></div>
                    </div>
                    <label class="@if($errors->has('date')) errors @endif">
                        <p>{{ __('shop-cart.Дата доставки') }}  *</p>

                        @if ($lost_order)
                            <input class="show_calendar date"
                                   required autocomplete="off"
                                   placeholder="{{ __('shop-cart.Выберите дату доставки') }}"
                                   data-text_delivery="{{ __('shop-cart.Выберите дату доставки') }}"
                                   data-text_pickup="{{ __('shop-cart.Выберите дату самовывоза') }}"
                                   type="text" name="date" readonly
                                   value="{{ isset($order_data['date']) ? $order_data['date'] : '' }}">
                        @else
                            <input class="show_calendar date"
                                   required autocomplete="off"
                                   placeholder="{{ __('shop-cart.Выберите дату доставки') }}"
                                   data-text_delivery="{{ __('shop-cart.Выберите дату доставки') }}"
                                   data-text_pickup="{{ __('shop-cart.Выберите дату самовывоза') }}"
                                   type="text" name="date" readonly value="{{ old('date') }}">
                        @endif


                        @error('date')
                        <p class="errors">{{ $message }}</p>
                        @enderror
                    </label>


                    <label class="@if($errors->has('time')) errors @endif">
                        <p class="delivery">{{ __('shop-cart.Выбрать время доставки') }}</p>
                        <p class="pickup" style="display: none;">{{ __('shop-cart.Выбрать время самовывоза') }}</p>

                        @if ($lost_order)
                            <input class="delivery_time" type="text" name="time"  value="{{ isset($order_data['time']) ? $order_data['time'] : '' }}">

                        @else
                            <input class="delivery_time" type="text" name="time" value="{{ old('time') }}" placeholder="{{ __('shop-cart.Укажите удобное вам время') }}" readonly>

                        @endif

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

                        @if ($lost_order)
                            <input type="checkbox" name="otherPerson" value="otherPerson" @isset($order_data['otherPerson']) checked @endisset >

                        @else
                            <input type="checkbox" name="otherPerson" value="otherPerson" @if(old('otherPerson')) checked @endif>
                        @endif

                        {{ __('shop-cart.Заказ для другого человека') }}
                    </label>
                </p>
                <div @if(!old('otherPerson') && !isset($order_data['otherPerson'])) style="display: none; " @endif >

                    @if ($lost_order)

                        <label>
                            <p>
                                {{ __('shop-cart.Телефон') }} *
                            </p>
                            <input type="text" name="phoneOtherPerson" value="{{ isset($order_data['phoneOtherPerson']) ? $order_data['phoneOtherPerson'] : '' }}">

                        </label>
                    @else
                        <input hidden class="phone" name="phoneOtherPerson">


                        <label class="phone-mask @if($errors->has('phone')) errors @endif" for="">
                            <p>
                                {{ __('shop-cart.Телефон') }} *
                            </p>
                        </label>
                    @endif


                    @error('phoneOtherPerson')
                    <p class="errors">{{ $message }}</p>
                    @enderror

                    <label class="@if($errors->has('nameOtherPerson')) errors @endif" for="">
                        <p>
                            {{ __('shop-cart.Имя') }} *
                        </p>

                        @if ($lost_order)
                            <input type="text" name="nameOtherPerson" value="{{ isset($order_data['nameOtherPerson']) ? $order_data['nameOtherPerson'] : '' }}">
                        @else
                            <input type="text" name="nameOtherPerson">
                        @endif


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

                        @if ($lost_order)
                            <input class="city_name" type="text" name="city"  value="{{ isset($order_data['city']) ? $order_data['city'] : '' }}">
                        @else
                            <input class="city_name" type="text" name="city"  value="{{ old('city') }}">
                        @endif



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

                        @if ($lost_order)
                            <input type="text" name="street" value="{{ isset($order_data['street']) ? $order_data['street'] : '' }}">
                        @else
                            <input type="text" name="street"  value="{{ old('street') }}">
                        @endif



                        @error('street')
                        <p class="errors">{{ $message }}</p>
                        @enderror
                    </label>
                    <div>
                        <label class="@if($errors->has('house')) errors @endif" for="">
                            <p>
                                {{ __('shop-cart.Дом') }} *
                            </p>

                            @if ($lost_order)
                                <input type="text" name="house" value="{{ isset($order_data['house']) ? $order_data['house'] : '' }}">
                            @else

                                <input type="text" name="house"  value="{{ old('house') }}">
                            @endif


                            @error('house')
                            <p class="errors">{{ $message }}</p>
                            @enderror
                        </label>
                        <label for="">
                            <p>
                                {{ __('shop-cart.Квартира') }}
                            </p>

                            @if ($lost_order)
                                <input type="text" name="flat" value="{{ isset($order_data['flat']) ? $order_data['flat'] : '' }}">
                            @else

                                <input type="text" name="flat"  value="{{ old('flat') }}">
                            @endif

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

                            @if ($lost_order)
                                <input type="text" name="floor" value="{{ isset($order_data['floor']) ? $order_data['floor'] : '' }}">
                            @else
                                <input type="text" name="floor"  value="{{ old('floor') }}">
                            @endif


                        </label>
                        <label for="">
                            <p>
                                {{ __('shop-cart.Код подьезда') }}
                            </p>

                            @if ($lost_order)
                                <input type="text" name="house_code" value="{{ isset($order_data['house_code']) ? $order_data['house_code'] : '' }}">
                            @else
                                <input type="text" name="house_code"  value="{{ old('house_code') }}">
                            @endif


                        </label>
                    </div>
                </div>

                <div>
                    @include('shop.new.layouts.cart.order_data')
                </div>

                <div class="pay__acttion">
                    <button>
                        @if(!$lost_order)
                            <a href="{{ route('cart', ['lang' => $lang, 'step' => 1, $lost_order]) }}">
                            </a>
                        @else

                            <a href="{{ route('crm_lost_cart', ['lang' => $lang, 'step' => 1, $lost_order]) }}">
                            </a>
                        @endif
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