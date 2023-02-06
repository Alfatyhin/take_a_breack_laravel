@extends('layouts.master')

@section('title', 'Заказы по сегментам')

@section('head')


@stop

@section('sidebar')
    @parent
@stop

@section('content')
    <form method="get" action="{{ route('shop_settings_orders_segments') }}" >
        start date: <input type="date" name="date-from" value="{{ $date_from->format('Y-m-d') }}">
        end date: <input type="date" name="date-to" value="{{ $date_to->format('Y-m-d') }}">
        <input class="button" type="submit" name="date_filter" value="изменить даты">
    </form>

    <table>
        <caption>
            {{ $date_from->format('Y-m-d') }}
            -
            {{ $date_to->format('Y-m-d') }}
        </caption>
        @if (!empty($segments))
            @foreach($segments as $ks => $item_data)
                <tr>
                    <th colspan="3">
                        <h2 style="color: brown">{{ $ks }}</h2>
                    </th>
                </tr>


                @foreach($item_data as $name => $value)
                    <tr>
                        <th>
                            {{ $name }}
                        </th>
                        <th >
                            <form method="get" action="{{ route('shop_settings_orders_segments') }}" >
                                <input type="hidden" name="date-from" value="{{ $date_from->format('Y-m-d') }}">
                                <input type="hidden" name="date-to" value="{{ $date_to->format('Y-m-d') }}">
                                <input type="hidden" name="type" value="{{ $ks }}">
                                <input type="hidden" name="download" value="{{ $name }}">
                                <button class="button" type="submit" name="" value="">
                                    <span class="fa fa-download"></span>
                                </button>
                            </form>
                        </th>
                        <th>
                        </th>
                    </tr>

                    @foreach($value as $phone => $v)
                        @php($size = sizeof($v))
                        <tr>
                            <td>
                                {{ $phone }}
                            </td>
                            <td>
                                {{ $size }}
                            </td>

                            <td>
                                @if ($name == 'Больше 1 позиции в чеке')
                                    @foreach($v as $pr_name)
                                        {{ $pr_name }}
                                        @if(!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endforeach

            @endforeach
        @endif

    </table>
@stop

