
@extends('shop.shop-master')

@section('title', "Natural author's desserts")

@section('head')

    <link rel="stylesheet" href="{{ asset('css/areasAndPrices.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/areasAndPrices_adaptation.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/index_adaptation.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/index-0.css') }}?{{ $v }}">

    <link rel="stylesheet" href="{{ asset('css/areasAndPrices.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/areasAndPrices_adaptation.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/cart-2.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/cart_adaptation.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/cart_adaptation-2.css') }}?{{ $v }}">

    <style>
        .order__price input {
            width: 94% !important;
            text-align: right;
        }
        .clientBirtDay {
            font-size: 20px;
        }
        .order__clientInfo .order__clientInfoInputWrapper.showBlock.order_price::after {
            display: none;
        }
        .order__tableTitle {
            padding-left: 10px;
        }
        @media (max-width: 768px) {
            .order__price input {
                width: 84% !important;
            }
        }
    </style>
@stop

@section('content')


    <div class="container">
        <div class="main__body">
            <section class="select" id="anchor-select_2">


                <form class="order__form" action="{{ route("new_short_order") }}" method="POST">
                    @csrf
                    <input type="hidden" name="lang" value="{{ $lang }}">
                    <table class="order__table">

                        <tbody>

                        <tr>
                            <td><span class="order__tableTitle">Fill in the data for payment:</span></td>
                            <td><span class="order__tableTitle">Enter amount:</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="order__clientInfo">
                                    <label class="order__clientInfoInputWrapper showBlock">
                                        <input class="blockTextField showBlock validateText @if($errors->has('clientName')) errorSend @endif" type="text"
                                               name="clientName" placeholder="Your name *" data-text="Имя"
                                               value="{{ old('clientName') }}">
                                    </label>

                                    <label class="order__clientInfoInputWrapper showBlock">
                                        <input class="blockTextField inputCall showBlock @if($errors->has('phone')) errorSend @endif" type="text"
                                               name="phone" placeholder="Phone number" data-text="Номер телефона"
                                               value="{{ old('phone') }}">
                                    </label>
                                    <label class="order__clientInfoInputWrapper showBlock">
                                        <input class="blockTextField showBlock validateText @if($errors->has('email')) errorSend @endif" type="text"
                                               name="email" placeholder="Email (for check) *" data-text="Email"
                                               value="{{ old('email') }}">
                                    </label>

                                    <label class="order__clientInfoInputWrapper showBlock">
                                        <input class="blockTextField dateMask showBlock @if($errors->has('clientBirthDay')) errorSend @endif" type="text"
                                               name="clientBirthDay" placeholder="Birthday"
                                               value="{{ old('clientBirthDay') }}">
                                    </label>

                                </div>
                            </td>
                            <td class="order__clientInfoPrice">
                                <div class="order__price order__clientInfo">
                                    <label class="order__clientInfoInputWrapper showBlock order_price">
                                        <input class="blockTextField order_price price_change showBlock @if($errors->has('order_price')) errorSend @endif" type="text"
                                               name="order_price"
                                               value="{{ old('order_price') }}">
                                        <span class="order__clientInfoCarUnit">₪</span>
                                    </label>
                                </div>
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
                                               @if(old('methodPay') != null)
                                               @if (old('methodPay') == 1)
                                               checked
                                               @endif
                                               @else
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
                            <td>
                            </td>
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
                                        <label class="price_click" for="premium_0">0%</label>
                                    </div>
                                    {{--                                        <div class="order__premiumField blockRadio">--}}
                                    {{--                                            <input id="premium_1" type="radio" name="premium" value="5" data-text="5%">--}}
                                    {{--                                            <label for="premium_1">5%</label>--}}
                                    {{--                                        </div>--}}
                                    <div class="order__premiumField blockRadio">
                                        <input id="premium_2" type="radio" name="premium" value="10" data-text="10%" checked>
                                        <label class="price_click" for="premium_2">10%</label>
                                    </div>
                                    <div class="order__premiumField blockRadio">
                                        <input id="premium_3" type="radio" name="premium" value="15" data-text="15%">
                                        <label class="price_click" for="premium_3">15%</label>
                                    </div>
                                    <div class="order__premiumField blockRadio">
                                        <input id="premium_4" type="radio" name="premium" value="20" data-text="20%">
                                        <label class="price_click" for="premium_4">20%</label>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="order__price"><span class="order__premiumPrice"></span><span class="order__premiumUnit">₪</span></div>
                            </td>
                        </tr>
                        </tbody>


                        <tfoot>
                        <tr>
                            <td>
                                <div class="order__totalTitle">Total amount due:</div>

                            </td>
                            <td>
                                <div class="order__total">
                                    <span class="order__totalPrice" contenteditable="true"></span>
                                    <span class="order__totalUnit">₪</span>
                                </div>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                    <div class="order__message blockText">
                    </div>
                    <button class="order__sendForm blockBtn blockBtn--bgc">checkout</button>
                </form>

            </section>

        </div>
    </div>

@stop


@section('scripts')
    <script src="{{ asset('js/short-shop.js') }}?{{ $v }}" defer></script>

@stop

