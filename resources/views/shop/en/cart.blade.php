

@extends('shop.cart-master')

@section('title')
    @parent
@stop

@section('head')

    @parent

@stop

@section('content')
    @parent
    <div class="container">
        <div class="main__body">
            <section class="listProduct"><a class="listProduct__btnBack btnBack" href="./"></a>
                <h1 class="listProduct__title blockTitle">
                    Your order
                    @if($order_number)
                        #{{ $order_number }}
                    @endif
                </h1>
                <div class="listProduct__tableWrapper">
                    <table class="listProduct__table">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Sum</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <td></td>
                            <td colspan="2"><span class='listProduct__itemFooterText'>Order cost:</span></td>
                            <td><span class='listProduct__itemAllTotal listProduct__itemFooterText'>0</span><span class='listProduct__itemAllTotalUnit listProduct__itemFooterText'>₪</span></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="2"><span class='listProduct__itemFooterText'>Total Order Value:</span></td>
                            <td><span class='listProduct__itemAllTotalPromo listProduct__itemFooterText'>0</span><span class='listProduct__itemAllTotalPromoUnit listProduct__itemFooterText'>₪</span></td>
                            <td></td>
                        </tr>
                        </tfoot>
                        <tbody>
                        </tbody>
                    </table>
                    <form class="listProduct__promo" method="POST">
                        <div class="listProduct__promoContainer">
                            <div class="listProduct__promoFail warningMessage blockText">The entered promo code does not exist!</div>
                            <div class="listProduct__promoWrapper">
                                <input class="listProduct__promoCode blockTextField" type="text" placeholder="Enter promo code here">
                                <button class="listProduct__promoBtn blockBtn blockBtn--bgc">apply promo code</button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>


            <section class="additional">
                <h2 class="additional__title">With this product also buy:</h2>
                <div class="additional__slider">
                    @include('shop.layouts.slider')
                </div>
                <div class="additional__sliderPagWrapper">
                    <div class="additional__sliderPagination sliderPagination"></div>
                </div>
                <div class="additional__text">If you are ready to place your order right now, then go to the section below or continue shopping</div>
                <div class="additional__btns"><a class="additional__goPayBtn additional__btn blockBtn blockBtn--transparent" href="#anchor-order">proceed to checkout</a><a class="additional__goCatalogBtn additional__btn blockBtn blockBtn--bgc" href="./#anchor-select">continue choosing products</a></div>
            </section>
            <section class="order" id="anchor-order">
                <h2 class="order__title blockTitle">Checkout</h2>
                <form class="order__form" action="{{ route("new_order") }}" method="POST">
                    @csrf
                    @if($order_number)
                        <input type="hidden" name="order_id" value="{{ $order_number }}">
                    @endif
                    <input type="hidden" name="gClientId" value="">

                    <input type="hidden" name="lang" value="{{ $lang }}">
                    <table class="order__table">
                        <tfoot>
                        <tr>
                            <td>
                                <div class="order__totalTitle">Total amount due:</div>
                            </td>
                            <td>
                                <div class="order__total"><span class="order__totalPrice"></span><span class="order__totalUnit">₪</span></div>
                            </td>
                        </tr>
                        </tfoot>
                        <tbody>
                        <tr>
                            <td><span class="order__tableTitle">Choose a shipping method:</span></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="order__deliveryMethod">
                                    <div class="order__deliveryMethodField blockRadio">
                                        <input id="idDelivery" type="radio" name="delivery" value="delivery" data-text="Доставка по Израилю" checked>
                                        <label for="idDelivery">Delivery within Israel</label>
                                    </div>
                                    <div class="order__deliveryMethodField blockRadio">
                                        <input id="idSelf" type="radio" name="delivery" value="pickup" data-text="Самовывоз по адресу Holon, Emanuel Ringelblum 3" data-block="self">
                                        <label for="idSelf">Pickup at address Holon, Emanuel Ringelblum 3</label>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="order__deliveryMethod">
                                    <div class="order__deliveryMethodNote" data-input="idDelivery">Delivery cost depends on the city</div>
                                    <div class="order__deliveryMethodNote" data-input="idSelf">When picking up you get a <span class='order__selfPercent'>2</span>% discount on the order value</div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="order__tableTitle">Fill in the data for delivery:</span></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="order__clientInfo">
                                    <label class="order__clientInfoInputWrapper showBlock">
                                        <input class="blockTextField showBlock validateText @if($errors->has('clientName')) errorSend @endif" type="text"
                                               name="clientName" placeholder="Your name *" data-text="Имя"
                                               value="{{ old('clientName') }}">
                                    </label>
                                    <label class="order__clientInfoInputWrapper">
                                        <input class="city_id" name="city_id" type="hidden">
                                        <input class="blockTextField city_input @if($errors->has('city')) errorSend @endif" type="text"
                                               name="city" placeholder="Your city *" data-text="Город"
                                               value="{{ old('city') }}">
                                        <div class="city_out hidden">
                                            <p>Select city</p>
                                            <div class="select_сity"></div>
                                        </div>
                                    </label>
                                    <label class="order__clientInfoInputWrapper">
                                        <input class="blockTextField @if($errors->has('street')) errorSend @endif" type="text"
                                               name="street" placeholder="Street *" data-text="Улица"
                                               value="{{ old('street') }}">
                                    </label>
                                    <label class="order__clientInfoInputWrapper">
                                        <input class="blockTextField @if($errors->has('house')) errorSend @endif" type="text"
                                               name="house" placeholder="House *" data-text="Дом"
                                               value="{{ old('house') }}">
                                    </label>
                                    <div class="order__clientInfoFields order__clientInfoFields--flat">
                                        <label class="order__clientInfoInputWrapper">
                                            <input class="blockTextField" type="text"
                                                   name="flat" placeholder="Flat " data-text="Квартира "
                                                   value="{{ old('flat') }}">
                                        </label>
                                        <label class="order__clientInfoInputWrapper">
                                            <input class="blockTextField" type="text"
                                                   name="floor" placeholder="Floor " data-text="Этаж "
                                                   value="{{ old('floor') }}">
                                        </label>
                                    </div>
                                    <label class="order__clientInfoInputWrapper showBlock">
                                        <input class="blockTextField inputCall validatePhone showBlock @if($errors->has('phone')) errorSend @endif" type="text"
                                               name="phone" placeholder="Phone number *" data-text="Номер телефона"
                                               value="{{ old('phone') }}">
                                    </label>
                                    <div class="order__clientInfoField order__clientInfoField--otherPerson blockRadio">

                                        <input id="otherPerson" type="checkbox"
                                               name="otherPerson" data-text="Заказываю другому человеку"
                                               @if(old('otherPerson')) checked @endif >

                                        <label for="otherPerson">I order to another person</label>
                                        <div class="order__clientInfoInputWrapper @if(old('otherPerson')) showBlock @endif">

                                            <input class="blockTextField @if($errors->has('nameOtherPerson')) errorSend @endif" type="text"
                                                   name="nameOtherPerson"
                                                   placeholder="Recipient's name *" data-text="Имя получателя"
                                                   value="{{ old('nameOtherPerson') }}">

                                            <input class="blockTextField @if($errors->has('phoneOtherPerson')) errorSend @endif" type="text"
                                                   name="phoneOtherPerson"
                                                   placeholder="Recipient's phone number *" data-text="Телефон получателя"
                                                   value="{{ old('phoneOtherPerson') }}">
                                        </div>
                                    </div>
                                    <label class="order__clientInfoInputWrapper showBlock">
                                        <input class="blockTextField showBlock validateText @if($errors->has('email')) errorSend @endif" type="text"
                                               name="email" placeholder="Email (for a check) *" data-text="Email"
                                               value="{{ old('email') }}">
                                    </label>
                                    <label class="order__clientInfoInputWrapper showBlock">
                                        <input class="blockTextField dateMask showBlock @if($errors->has('clientBirthDay')) errorSend @endif" type="text"
                                               name="clientBirthDay" placeholder="Birthday"
                                               value="{{ old('clientBirthDay') }}">
                                    </label>

                                    <div class="order__clientInfoFields order__clientInfoFields--datetime showBlock">
                                        <div class="calendar_box">
                                            <div class="calendar_table hidden"></div>
                                        </div>
                                        <label class="order__clientInfoInputWrapper showBlock">
                                            <input class="blockTextField showBlock show_calendar date @if($errors->has('date')) errorSend @endif" type="text" autocomplete="off"
                                                   name="date" placeholder="Delivery date *" data-text="Дата доставки"
                                                   value="{{ old('date') }}">

                                        </label>
                                        <label class="order__clientInfoInputWrapper showBlock">
                                            <select class="blockTextField showBlock @if($errors->has('time')) errorSend @endif" name="time">
                                                <option value="{{ old('time') }}">
                                                    @if(old('time'))
                                                        {{ old('time') }}
                                                    @else
                                                        Delivery time
                                                    @endif
                                                </option>
                                            </select>
                                        </label>
                                    </div>
                                    <div class="order__clientInfoNote">
                                        <p>Find out the cost of delivery to your city on <button class='areaAndPriceBtn blockText' type='button'>the link</button></p>
                                    </div>
                                </div>
                            </td>
                            <td class="order__clientInfoPrice">
                                <div class="order__price"><span class="order__clientInfoCarPrice"></span><span class="order__clientInfoCarUnit">₪</span></div>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="order__tableTitle">Select a Payment Method:</span></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="order__methodPay">
                                    <div class="order__methodPayField blockRadio">
                                        <input id="methodCredit" type="radio" name="methodPay" value="1" data-text="Кредитная карта"
                                               @if (old('methodPay') == 1)
                                               checked
                                            @endif
                                        >
                                        <label for="methodCredit">Credit card</label>
                                    </div>
                                    <div class="order__methodPayField blockRadio">
                                        <input id="methodPaypal" type="radio" name="methodPay" value="3" data-text="PayPal"
                                               @if (old('methodPay') == 3)
                                               checked
                                            @endif
                                        >
                                        <label for="methodPaypal">PayPal</label>
                                    </div>
                                    <div class="order__methodPayField blockRadio">
                                        <input id="methodBit" type="radio" name="methodPay" value="4" data-text="Bit"
                                               @if (old('methodPay') == 4)
                                               checked
                                            @endif
                                        >
                                        <label for="methodBit">Bit</label>
                                    </div>
                                    <div class="order__methodPayField blockRadio">
                                        <input id="methodMoney" type="radio" name="methodPay" value="2" data-text="Наличные"
                                               @if (old('methodPay') == 2)
                                               checked
                                            @endif
                                        >
                                        <label for="methodMoney">Cash</label>
                                    </div>
                                </div>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><span class="order__tableTitle">Promo code discount:</span></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="order__promoItems">
                                    <div class="order__promoItem order__blockTitle">
                                        <p>Promo code not activated</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="order__promoPrices"></div>
                            </td>
                        </tr>
                        <tr data-block="self">
                            <td><span class="order__tableTitle">Discount for pickup:</span></td>
                            <td></td>
                        </tr>
                        <tr data-block="self">
                            <td>
                                <div class="order__selfTitle order__blockTitle">
                                    <p>Your <span class='order__selfPercent'>2</span>% discount on the order value</p>
                                </div>
                            </td>
                            <td>
                                <div class="order__price"><span class="order__selfPrice"></span><span class="order__selfUnit">₪</span></div>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="order__tableTitle">Comment to the order:</span></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th colspan="2">
                                <textarea name="client_comment" class="order__comment blockTextField" placeholder="Write your comment here"
                                          data-text="Комментарий">{{ old('client_comment') }}</textarea>
                            </th>
                        </tr>
                        <tr>
                            <td><span class="order__tableTitle">Support our project with a tip:</span></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="order__premium">
                                    <div class="order__premiumField blockRadio">
                                        <input id="premium_0" type="radio" name="premium" value="0" data-text="0%">
                                        <label for="premium_0">0%</label>
                                    </div>
                                    {{--                                        <div class="order__premiumField blockRadio">--}}
                                    {{--                                            <input id="premium_1" type="radio" name="premium" value="5" data-text="5%">--}}
                                    {{--                                            <label for="premium_1">5%</label>--}}
                                    {{--                                        </div>--}}
                                    <div class="order__premiumField blockRadio">
                                        <input id="premium_2" type="radio" name="premium" value="10" data-text="10%">
                                        <label for="premium_2">10%</label>
                                    </div>
                                    <div class="order__premiumField blockRadio">
                                        <input id="premium_3" type="radio" name="premium" value="15" data-text="15%">
                                        <label for="premium_3">15%</label>
                                    </div>
                                    <div class="order__premiumField blockRadio">
                                        <input id="premium_4" type="radio" name="premium" value="20" data-text="20%">
                                        <label for="premium_4">20%</label>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="order__price"><span class="order__premiumPrice"></span><span class="order__premiumUnit">₪</span></div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="order__message blockText">
                        <span class="warningMessage warningMessage--valid">Please complete all required fields to continue!</span>
                        <span class="warningMessage warningMessage--noDeliveryCity @if($errors->has('city')) showBlock @endif ">Write to us to clarify the possibility of delivery to this city.</span>
                        <span class="warningMessage warningMessage--minSum">
                            The amount of the order is less, please read the terms of delivery
                            <button class='areaAndPriceBtn blockText' type='button'>here</button>.
                        </span>
                    </div>
                    <button class="order__sendForm blockBtn blockBtn--bgc">checkout</button>

                    <input class="order_data" type="hidden" name="order_data">
                </form>
                <div class="order__listAreasPrices" hidden>
                    @include('shop.layouts.cart.city_price')
                </div>
            </section>
        </div>
    </div>

@stop

