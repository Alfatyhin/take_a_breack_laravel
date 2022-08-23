@extends('layouts.master')

@section('title', 'Скидки')

@section('head')


@stop

@section('sidebar')
    @parent
@stop

@section('content')


    @include('shop-settings.layouts.popapp_message')

    <table>
        <tr>
            <th>id</th>
            <th>название</th>
            <th>код</th>
            <th>скидка</th>
            <th>период</th>
            <th>статус</th>
            <th>число применений</th>
            <th>ограничение применений</th>
            <th>действует для</th>
            <th></th>
        </tr>
        @if (!empty($coupons))
            @foreach($coupons as $item)
                @php($discount = json_decode($item->discount, true))
                @php($data = json_decode($item->data, true))
                <tr>
                    <td>
                        {{ $item->id }}
                    </td>
                    <td>
                        {{ $item->name }}
                    </td>
                    <td>
                        {{ $item->code }}
                    </td>
                    <td>
                        {{ $discount['value'] }}
                        @if ($discount['mod'] == "ABS")
                            $
                        @else
                            %
                        @endif
                    </td>
                    <td></td>
                    <td>
                        <form class="ajax" action="{{ route('coupon_status_save') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $item->id }}">
                            @if ($item->status == 'active')
                                <input class="submit" type="checkbox" name="enabled" value="disable" checked />
                            @else
                                <input class="submit" type="checkbox" name="enabled" value="active" />
                            @endif
                        </form>
                    </td>
                    <td>
                        {{ $item->count }}
                    </td>
                    <td>

                        <form action="{{ route('coupon_data_change') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $item->id }}">
                            @isset($data['count_limit'])
                                <input type="number" name="data[count_limit]" value="{{ $data['count_limit'] }}">
                            @else
                                <input type="number" name="data[count_limit]" value="0">
                            @endif
                            <input type="submit" name="save" value="изменить">
                        </form>
                    </td>
                    <td>
                        всех товаров
                    </td>
                    <td>
                        <a class="fa fa-trash" href="{{ route('coupons_discount', ['delete' => $item->id ]) }}"></a>
                        <br>
                        <span class="fa fa-pencil" data_id="{{ $item->id }}"></span>
                        <div class="hidden float-box">
                            <form  action="" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $item->id }}">
                                <p>
                                    скидка
                                    <input type="number" name="discount[value]" value="{{ $discount['value'] }}"> <br>
                                    тип скидки: <br>
                                    @if ($discount['mod'] == "ABS")
                                        <input type="radio" name="discount[mod]" value="ABS" checked>$
                                        <input type="radio" name="discount[mod]" value="PERSENT" >%
                                    @else
                                        <input type="radio" name="discount[mod]" value="ABS" >$
                                        <input type="radio" name="discount[mod]" value="PERSENT" checked>%
                                    @endif
                                </p>
                                <p>
                                    ограничение применений:
                                    <input type="number" name="data[count_limit]" value="">
                                </p>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        @endif

        <tr>
            <form action="{{ route('coupons_discount') }}" method="POST">
                @csrf
                <td>
                </td>
                <td>

                    <input type="text" name="name">
                </td>
                <td>
                    <input type="text" name="code">
                </td>
                <td>
                    скидка
                    <input type="number" name="discount[value]"> <br>
                    тип скидки: <br>
                    <input type="radio" name="discount[mod]" value="ABS" checked>$
                    <input type="radio" name="discount[mod]" value="PERSENT" >%
                </td>
                <td></td>
                <td>
                    <input type="radio" name="status" value="active" checked> вкл
                </td>
                <td></td>
                <td>
                    <input type="number" name="data[count_limit]" value="1">
                </td>
                <td></td>
                <td><input type="submit" name="add" value="+"></td>
            </form>
        </tr>
    </table>
@stop

