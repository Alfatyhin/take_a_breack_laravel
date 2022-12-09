@extends('layouts.master')

@section('title', 'webhoocks')

@section('sidebar')
    @parent
@stop

@section('content')
    <style>
        form {
            display: inline-block;
            margin: 5px 10px;
        }
    </style>
    <div class="max-w-10xl mx-auto sm:px-10 lg:px-10">

        <table>
            <tr>
                <th> id </th>
                <th> name </th>
                <th> data </th>
            </tr>
            @if(isset($webhoocks))
                @foreach($webhoocks as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>
                            {{ $item->name }}
                            <br>
                            {{ $item->created_at->format('Y-m-d H-i-s') }}
                        </td>
                        <td>
                            @if ($item->name == 'OrderThanksView ')
                                @php($data = json_decode($item->data, true))

                                @if (is_string($data['orderData']))
                                    @php($orderData = json_decode($data['orderData'], true))
                                @else

                                    @php($orderData = $data['orderData'])
                                @endif
                                <p>
                                    <b>#{{ $data['order_id'] }}</b> <br>
                                    <b>имя:</b> {{ $orderData['clientName'] }} <br>
                                    <b>email:</b> {{ $orderData['email'] }} <br>
                                    @isset($orderData['phone'])
                                        <b>tel:</b> {{ $orderData['phone'] }}
                                    @endisset
                                </p>

                                <hr>
                                @isset($orderData['order_data']['products'])
                                    @foreach($orderData['order_data']['products'] as $product)
                                        <b>
                                            @if (!empty($product['name']['ru']))
                                                {{ $product['name']['ru'] }}

                                            @else
                                                {{ $product['name']['en'] }}
                                            @endif
                                        </b>

                                        @if (isset($product['options']))
                                            @foreach($product['options'] as $option)
                                                {{ $option['name']['ru'] }} -
                                                {{ $option['value']['text'] }}
                                                ( {{ $option['value']['textTranslated']['ru'] }} )
                                            @endforeach
                                        @endif
                                        - {{ $product['count'] }} шт

                                        <br>
                                    @endforeach
                                @endisset
                                @isset($orderData['otherPerson'])
                                    <hr>
                                    <b>заказ для друга:</b> <br>
                                    <b>имя:</b> {{ $orderData['nameOtherPerson'] }} <br>
                                    <b>tel:</b> {{ $orderData['phoneOtherPerson'] }}
                                @endisset
                                @isset($orderData['client_comment'])
                                    <hr>
                                    <b>комментарий клиента:</b> <br>
                                    <div class="text_block">
                                        {{ $orderData['client_comment'] }}
                                    </div>
                                @endisset
                                @isset($orderData['order_data']['tips'])
                                    <hr>
                                    <b>чаевые:</b>
                                    {{ $orderData['order_data']['tips'] }}
                                @endisset

                                @isset($orderData['order_data']['discount'])
                                    <hr>
                                    <b>Скидка купон:</b>
                                @endisset
                                @isset($orderData['delivery'])
                                    @if($orderData['delivery'] == 'delivery')
                                        <hr>
                                        <b>Доставка:</b>
                                        г-{{ $orderData['city'] }} <br>
                                        ул-{{ $orderData['street'] }}
                                        {{ $orderData['house'] }}
                                        кв-{{ $orderData['flat'] }}
                                        @isset($orderData['floor'])
                                            эт-{{ $orderData['floor'] }}
                                        @endisset
                                    @else
                                        <hr>
                                        <b>Самовывоз</b>
                                        @isset($orderData['order_data']['delivery_discount'])
                                            <br> <b>скидка:</b>
                                            {{ $orderData['order_data']['delivery_discount'] }}
                                        @endisset
                                    @endif
                                    <hr>
                                    <b>Дата:</b>
                                    {{ $orderData['date'] }} {{ $orderData['time'] }}
                                @endisset
                            @else
                                @php(print_r($item->data))
                            @endif
                            <br>

                        </td>
                    </tr>
                @endforeach
            @endif
        </table>
    </div>
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 paginate">
        {{ $webhoocks->links() }}
    </div>

@stop

