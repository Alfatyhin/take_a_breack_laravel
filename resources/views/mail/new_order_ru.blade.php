

@extends('layouts.mail')

@section('title',  'New Order')


@section('content')



    <!--Спасибо за ваш заказ-->
    <tr>
        <td align="center">
            <table align="center" cellpadding="0" cellspacing="0" width="100%" style="max-width: 660px; min-width: 320px; background-color: #ffffff">
                <tr>
                    <td align="center" height="40">
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <table align="center" cellpadding="0" cellspacing="0" width="95%" style="max-width: 600px; min-width: 300px;">
                            <tr>
                                <td align="center">
                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 32px;line-height: 38px;color: #000000; text-align: center">Спасибо за ваш заказ!</p>
                                    <p style="margin: 0; padding: 0; margin-top: 15px; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;color: #000000; text-align: center">
                                        Здравствуйте, {{ $order->orderData['clientName'] }},
                                        мы получили ваш заказ <nobr>#{{ $order->order_id }}</nobr>
                                        и уже начали работу над ним.
                                    </p>
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>
                <tr>
                    <td align="center" height="40">
                    </td>
                </tr>
            </table>

        </td>
    </tr>

    <!--Информация о доставке-->



    <tr>
        <td align="center">
            <table align="center" cellpadding="0" cellspacing="0" width="100%" style="max-width: 660px; min-width: 320px; background-color: #FEEDD6">
                <tr>
                    <td align="center" height="17">
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <table align="center" cellpadding="0" cellspacing="0" width="95%" style="max-width: 600px; min-width: 300px; background-color: #FEEDD6">
                            <tr>
                                <td align="center"><p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 16px;text-align: center;text-transform: uppercase;color: #000000; text-align: left">Информация о заказе и доставке</p></td>
                            </tr>
                        </table>

                    </td>
                </tr>
                <tr>
                    <td align="center" height="17">
                    </td>
                </tr>
            </table>

        </td>
    </tr>

    <tr>
        <td align="center">
            <table align="center" cellpadding="0" cellspacing="0" width="95%" style="max-width: 600px; min-width: 300px;">
                <tr>
                    <td align="center">

                        <table align="left" cellpadding="0" cellspacing="0" width="100%" style="max-width: 299px; min-width: 299px;">
                            <tr>
                                <td align="center">
                                    <p style="margin: 0; padding: 0; margin-top: 25px; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;color: #000000;text-align: left; text-transform: uppercase;">Детали заказа</p>
                                    <p style="margin: 0; padding: 0; margin-top: 15px; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;color: #000000;text-align: left;"><strong>Заказ:</strong> #{{ $order->order_id }} </p>
                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;color: #000000;text-align: left; margin-top: 10px;"><strong>Телефон:</strong> <a href="#" style="font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;text-align: center;color: #000000;text-align: left;text-decoration: none;">{{ $order->orderData['phone'] }}</a> </p>
                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;color: #000000;text-align: left; margin-top: 10px;"><strong>Email:</strong> <a href="#" style="font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;text-align: center;color: #000000;text-align: left;text-decoration: none;">{{ $order->orderData['email'] }}</a></p>
                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;color: #000000;text-align: left; margin-top: 10px;"><strong>Способ оплаты:</strong> {{ $order->paymentMethod }} </p>
                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;color: #000000;text-align: left; margin-top: 10px;"><strong>Статус оплаты:</strong> {{ $order->paymentStatus }}</p>
                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;color: #000000;text-align: left; margin-top: 10px;"><strong>Статус заказа:</strong> В обработке</p>
                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;color: #000000;text-align: left; margin-top: 10px;"><strong>Способ доставки:</strong>
                                        @if($order->orderData['delivery'] == 'delivery')
                                            Служба доставки
                                        @else
                                            Самовывоз
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <table align="left" cellpadding="0" cellspacing="0" width="100%" style="max-width: 299px; min-width: 299px;">
                            <tr>
                                <td align="center">
                                    <p style="margin: 0; padding: 0; margin-top: 25px; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;text-align: center;color: #000000;text-align: left; text-transform: uppercase;">Детали доставки</p>
                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;text-align: center;color: #000000;text-align: left;margin-top: 25px;">
                                        @if($order->orderData['delivery'] == 'delivery')
                                            <strong>Адрес:</strong>
                                            г. {{ $order->orderData['city'] }}, ул. {{ $order->orderData['street'] }},
                                            дом  {{ $order->orderData['house'] }}
                                            @if(!empty($order->orderData['floor'])),
                                            {{ $order->orderData['floor'] }} этаж,
                                            @endif

                                            @if(!empty($order->orderData['flat']))
                                                квартира {{ $order->orderData['flat'] }}
                                            @endif
                                        @else

                                            Самовывоз по адресу Holon, Emanuel Ringelblum 3
                                        @endif
                                    </p>
                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;text-align: center;color: #000000;text-align: left;margin-top: 10px;"><strong>Время:</strong> {{ $order->orderData['date'] }}, {{ $order->orderData['time'] }} </p>

                                    @if(!empty($order->orderData['client_comment']))
                                        <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;text-align: center;color: #000000;text-align: left;margin-top: 25px;">Комментарий</p>
                                        <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;text-align: center;color: #000000;text-align: left;margin-top: 15px;">{{ $order->orderData['client_comment'] }}</p>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
    <tr>
        <td height="40"></td>
    </tr>

    <!--Ваш Заказ ---------------->

    <tr>
        <td align="center">
            <table align="center" cellpadding="0" cellspacing="0" width="100%" style="max-width: 660px; min-width: 320px; background-color: #FEEDD6">
                <tr>
                    <td align="center" height="17">
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <table align="center" cellpadding="0" cellspacing="0" width="95%" style="max-width: 600px; min-width: 300px; background-color: #FEEDD6">
                            <tr>
                                <td align="center"><p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 16px;text-align: center;text-transform: uppercase;color: #000000; text-align: left">Ваш заказ</p></td>
                            </tr>
                        </table>

                    </td>
                </tr>
                <tr>
                    <td align="center" height="17">
                    </td>
                </tr>
            </table>

        </td>
    </tr>

    <tr>
        <td align="center">
            <table align="center" cellpadding="0" cellspacing="0" width="100%" style="max-width: 660px; min-width: 320px; ">
                <tr>
                    <td align="center" height="20">
                    </td>
                </tr>
                @foreach($order->orderData['order_data']['products'] as $item)

                    <tr>
                        <td align="center">
                            <table align="center" cellpadding="0" cellspacing="0" width="95%" style="max-width: 600px; min-width: 300px;">
                                <tr>
                                    <td align="left" style="max-width: 170px; min-width: 140px">
                                        <img src="https://takeabreak.co.il{{ $item['img_url'] }}" width="130" height="130" style="display: block" alt="">
                                    </td>
                                    <td align="left" style="max-width: 430px">
                                        <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 500;font-size: 16px;line-height: 16px;text-align: left;color: #000000; text-align: left">
                                            @if (!empty($item['name'][$order->orderData['lang']]))
                                                {{ $item['name'][$order->orderData['lang']] }}
                                            @else
                                                {{ $item['name']['en'] }}
                                            @endif
                                            {{ $item['info'] }}
                                        </p>

                                        <table align="left" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td height="20"></td>
                                            </tr>
                                            <tr>
                                                <td align="left">
                                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 16px;text-align: left;color: #000000; text-align: left">Цена</p>
                                                    <p style="margin: 0; padding: 0;margin-top: 10px; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 300;font-size: 16px;line-height: 16px;text-align: left;color: #000000; text-align: left">{{ $item['price'] }}₪</p>
                                                </td>
                                                <td align="center">
                                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 16px;text-align: left;color: #000000; text-align: center">Количество</p>
                                                    <p style="margin: 0; padding: 0;margin-top: 10px; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 300;font-size: 16px;line-height: 16px;text-align: left;color: #000000; text-align: center">{{ $item['count'] }}</p>
                                                </td>
                                                <td align="right">
                                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 16px;text-align: left;color: #000000; text-align: right">Сумма</p>
                                                    <p style="margin: 0; padding: 0;margin-top: 10px; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 300;font-size: 16px;line-height: 16px;text-align: left;color: #000000; text-align: right">{{ $item['price'] * $item['count'] }}₪</p>
                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" height="20">
                        </td>
                    </tr>
                @endforeach
            </table>
        </td>
    </tr>



    <!--Стоимость заказа ---------------->

    <tr>
        <td align="center">
            <table align="center" cellpadding="0" cellspacing="0" width="100%" style="max-width: 660px; min-width: 320px; background-color: #FEEDD6">
                <tr>
                    <td align="center" height="17">
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <table align="center" cellpadding="0" cellspacing="0" width="95%" style="max-width: 600px; min-width: 300px; background-color: #FEEDD6">
                            <tr>
                                <td align="center"><p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 16px;text-align: center;text-transform: uppercase;color: #000000; text-align: left">Стоимость заказа</p></td>
                            </tr>
                        </table>

                    </td>
                </tr>
                <tr>
                    <td align="center" height="17">
                    </td>
                </tr>
            </table>

        </td>
    </tr>

    <tr>
        <td align="center">
            <table align="center" cellpadding="0" cellspacing="0" width="100%" style="max-width: 660px; min-width: 320px; ">
                <tr>
                    <td align="center" height="20">
                    </td>
                </tr>


                <tr>
                    <td align="center">
                        <table align="center" cellpadding="0" cellspacing="0" width="95%" style="max-width: 600px; min-width: 300px;">
                            <tr>
                                <td align="right">
                                    <table align="right" cellpadding="0" cellspacing="0" width="100%" style="max-width: 430px; min-width: 300px;">
                                        <tr>
                                            <td align="left" >
                                                <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 500;font-size: 16px;line-height: 16px;color: #000000; text-align: left">Сумма</p>
                                            </td>
                                            <td align="left" style="max-width: 430px">
                                                <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 300;font-size: 16px;line-height: 16px;color: #000000; text-align: right">{{ $order->orderData['order_data']['products_total'] }}₪</p>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="10"></td>
                                        </tr>

                                        @isset($order->orderData['order_data']['discount'])
                                            <tr>
                                                <td align="left" >
                                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 500;font-size: 16px;line-height: 16px;color: #000000; text-align: left">Промокод</p>
                                                </td>
                                                <td align="left" style="max-width: 430px">
                                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 300;font-size: 16px;line-height: 16px;color: #000000; text-align: right">{{ $order->orderData['order_data']['discount']['code'] }}</p>

                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="10"></td>
                                            </tr>
                                            <tr>
                                                <td align="left" >
                                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 500;font-size: 16px;line-height: 16px;color: #000000; text-align: left">Скидка</p>
                                                </td>
                                                <td align="left" style="max-width: 430px">
                                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 300;font-size: 16px;line-height: 16px;color: #000000; text-align: right">{{ $order->orderData['order_data']['discount']['text'] }}</p>

                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="10"></td>
                                            </tr>
                                        @endisset


                                        @isset($order->orderData['order_data']['tips'])
                                            <tr>
                                                <td align="left" >
                                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 500;font-size: 16px;line-height: 16px;color: #000000; text-align: left">Чаевые</p>
                                                </td>
                                                <td align="left" style="max-width: 430px">
                                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 300;font-size: 16px;line-height: 16px;color: #000000; text-align: right">{{ $order->orderData['order_data']['tips'] }}₪</p>

                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="10"></td>
                                            </tr>
                                        @endisset

                                        @isset($order->orderData['order_data']['delivery_price'])

                                            <tr>
                                                <td align="left" >
                                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 500;font-size: 16px;line-height: 16px;color: #000000; text-align: left">Доставка</p>
                                                </td>
                                                <td align="left" style="max-width: 430px">
                                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 300;font-size: 16px;line-height: 16px;color: #000000; text-align: right">{{ $order->orderData['order_data']['delivery_price'] }}₪</p>

                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="20"></td>
                                            </tr>
                                        @endisset

                                        @isset($order->orderData['order_data']['delivery_discount'])

                                            <tr>
                                                <td align="left" >
                                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 500;font-size: 16px;line-height: 16px;color: #000000; text-align: left">Самовывоз скидка</p>
                                                </td>
                                                <td align="left" style="max-width: 430px">
                                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 300;font-size: 16px;line-height: 16px;color: #000000; text-align: right">{{ $order->orderData['order_data']['delivery_discount'] }}₪</p>

                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="20"></td>
                                            </tr>
                                        @endisset

                                    </table>
                                </td>
                            </tr>
                        </table>

                        <table align="center" cellpadding="0" cellspacing="0" width="95%" style="max-width: 600px; min-width: 300px;">
                            <tr>
                                <td align="right">
                                    <table align="right" cellpadding="0" cellspacing="0" width="100%" style="max-width: 430px; min-width: 300px;">
                                        <tr>
                                            <td align="left" >
                                                <hr style="margin: 0; padding: 0;" width="99%" size="1px" color="#AD7D80">
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <table align="center" cellpadding="0" cellspacing="0" width="95%" style="max-width: 600px; min-width: 300px;">
                            <tr>
                                <td align="right">
                                    <table align="right" cellpadding="0" cellspacing="0" width="100%" style="max-width: 430px; min-width: 300px;">
                                        <tr>
                                            <td height="20"></td>
                                        </tr>
                                        <tr>
                                            <td align="left" >
                                                <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 500;font-size: 16px;line-height: 16px;color: #000000; text-align: left; text-transform: uppercase">Итоговая сумма</p>
                                            </td>
                                            <td align="left" style="max-width: 430px">
                                                <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 500;font-size: 16px;line-height: 16px;color: #000000; text-align: right">{{ $order->orderPrice }}₪</p>

                                            </td>

                                            <div class="border"></div>

                                        </tr>
                                        <tr>
                                            <td height="10"></td>
                                        </tr>

                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>


                <tr>
                    <td align="center" height="40">
                    </td>
                </tr>
            </table>
        </td>
    </tr>


@stop

