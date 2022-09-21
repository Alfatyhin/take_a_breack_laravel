

@extends('api.order-view')

@section('title',  'Order')


@section('content')




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
                            <td>
                                <input type="submit" value="Печатать" class="print-button"
                                       onClick="window.print()">
                            </td>
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
                                    <p style="margin: 0; padding: 0; margin-top: 15px; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;color: #000000;text-align: left;"><strong>Клиент:</strong> {{ $order->orderData['clientName'] }} </p>
                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;color: #000000;text-align: left; margin-top: 10px;"><strong>Телефон:</strong> <a href="#" style="font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;text-align: center;color: #000000;text-align: left;text-decoration: none;">{{ $order->orderData['phone'] }}</a> </p>
                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;color: #000000;text-align: left; margin-top: 10px;"><strong>Email:</strong> <a href="#" style="font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;text-align: center;color: #000000;text-align: left;text-decoration: none;">{{ $order->orderData['email'] }}</a></p>
                                </td>
                            </tr>
                        </table>

                        <table align="left" cellpadding="0" cellspacing="0" width="100%" style="max-width: 299px; min-width: 299px;">
                            <tr>
                                <td align="center">
                                    <p style="margin: 0; padding: 0; margin-top: 25px; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;text-align: center;color: #000000;text-align: left; text-transform: uppercase;">Детали доставки</p>
                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;text-align: center;color: #000000;text-align: left;margin-top: 25px;">
                                        @if(isset($order->orderData['delivery']) && $order->orderData['delivery'] == 'delivery')
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
                                    @isset($order->orderData['date'])
                                        <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 19px;text-align: center;color: #000000;text-align: left;margin-top: 10px;"><strong>Время:</strong> {{ $order->orderData['date'] }}, {{ $order->orderData['time'] }} </p>

                                    @endisset

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
                                <td align="center"><p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 16px;text-align: center;text-transform: uppercase;color: #000000; text-align: left">Заказ</p></td>
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
                                    <td align="left" style="max-width: 430px">


                                        <table align="left" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td height="20"></td>
                                            </tr>
                                            <tr>
                                                <td align="left">
                                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 500;font-size: 16px;line-height: 16px;text-align: left;color: #000000; text-align: left">
                                                        @if (!empty($item['name'][$order->orderData['lang']]))
                                                            {{ $item['name'][$order->orderData['lang']] }}
                                                        @else
                                                            {{ $item['name']['en'] }}
                                                        @endif
                                                        {{ $item['info'] }}
                                                    </p>
                                                </td>
                                                <td align="center">
                                                    <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 16px;text-align: left;color: #000000; text-align: center">Количество</p>
                                                    <p style="margin: 0; padding: 0;margin-top: 10px; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 300;font-size: 16px;line-height: 16px;text-align: left;color: #000000; text-align: center">{{ $item['count'] }}</p>
                                                </td>
                                                <td align="right">

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

    @if($order->paymentMethod == 2)
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
                                    <td align="center"><p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 16px;text-align: center;text-transform: uppercase;color: #000000; text-align: left">Стоимость заказа  </p></td>
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

    @endif

@stop

