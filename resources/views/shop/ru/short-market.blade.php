
@extends('shop.shop_master_new')

@section('title', "Natural author's desserts")

@section('head')




@stop

@section('content')



    <div class="pay">
        <div class="pay__title">
            <h2>
                форма оплаты заказа
            </h2>
        </div>
        <div class="pay__form">
            <form action="{{ route("new_short_order") }}" method="POST">
                @csrf
                <input type="hidden" name="lang" value="{{ $lang }}">
                <label>
                    <p>Введите сумму оплаты:</p>
                    <input required class="@if($errors->has('order_price')) errorSend @endif"
                           type="number"
                           name="order_price"
                           value="{{ old('order_price') }}">
                </label>
                <p>
                    Заполните данные для оплаты:
                </p>
                <div>
                    <label class="phone-mask" for="">
                        <select name="phone-code">
                            <option value="+972">+972</option>
                            <option value="+49">+49</option>
                        </select>
                        <p>
                            Телефон *
                        </p>
                        <input required class="phone-mask-input" placeholder="00-00-000 (000)" type="tel" name="user-phone">
                    </label>
                    <label for="">
                        <p>
                            Имя *
                        </p>
                        <input required class="@if($errors->has('clientName')) errorSend @endif"
                               type="text"
                               name="clientName"
                               value="{{ old('clientName') }}">
                    </label>
                </div>
                <label for="">
                    <p>
                        Email *
                    </p>
                    <input required class="@if($errors->has('email')) errorSend @endif"
                           type="email"
                           name="email"
                           value="{{ old('email') }}">
                </label>
                <label for="">
                    <p>
                        Дата Рождения
                    </p>
                    <input class="@if($errors->has('clientBirthDay')) errorSend @endif"
                           type="date"
                           name="clientBirthDay"
                           value="{{ old('clientBirthDay') }}">
                </label>
                <p>
                    Выберите способ оплаты:
                </p>
                <div class="pay-type">
                    <label>
                        <input name="methodPay" value="1" type="radio"
                                  @if(old('methodPay') != null && old('methodPay') == 1)
                                  checked
                                  @else
                                  checked
                                @endif
                        >
                        <span></span><p>Кредитная карта</p>
                    </label>
                    <label>
                        <input name="methodPay" value="3" type="radio"
                               @if (old('methodPay') == 3)
                               checked
                                @endif
                        >
                        <span></span><p>Pay Pal</p>
                    </label>
                    <label>
                        <input name="methodPay" value="4" type="radio"
                               @if (old('methodPay') == 4)
                               checked
                                @endif
                        >
                        <span></span><p>BIT</p>
                    </label>
                    <label>
                        <input name="methodPay" value="2" type="radio"
                               @if (old('methodPay') == 2)
                               checked
                                @endif
                        >
                        <span></span><p>Наличные</p>
                    </label>
                </div>
                <p>
                    Поддержите наш проект чаевыми:
                </p>
                <div class="pay-tips">
                    <label><input name="premium" type="radio"><span></span><p>0%</p></label>
                    <label><input checked name="premium" type="radio"><span></span><p>10%</p></label>
                    <label><input name="premium" type="radio"><span></span><p>12%</p></label>
                    <label><input name="premium" type="radio"><span></span><p>15%</p></label>
                </div>
                <p class="total-pay">
                    Общая сумма к оплате:  199 ₪
                </p>
                <div class="pay__acttion">
                    <button class="main-btn go-pay">
                        Оплатить
                    </button>
                </div>
            </form>
        </div>
    </div>

@stop


@section('scripts')
    <script src="{{ asset('/assets/libs/mask-lib.js') }}?{{ $v }}" defer></script>
@stop

