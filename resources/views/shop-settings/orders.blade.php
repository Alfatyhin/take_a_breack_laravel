@extends('layouts.master')

@section('title', 'Заказы')


@section('head')


@stop

@section('sidebar')
    @parent
@stop

@section('content')

    <p>
        общая сумма текущий месяц - ( {{ $priceMonth }} )
    </p>

    <p>
        общая сумма за весь год - ( {{ $priceYear }} )
    </p>
    <hr>
    <form method="get" action="{{ route('shop_settings_orders') }}" >
        start date: <input type="date" name="date-from" value="{{ $date_from->format('Y-m-d') }}">
        end date: <input type="date" name="date-to" value="{{ $date_to->format('Y-m-d') }}">
        <input class="button" type="submit" name="date_filter" value="установить даты">
    </form>
    <a class="button" href="{{ route('shop_settings_orders', ['dates' => 'today']) }}"> за сегодня </a>
    <a class="button" href="{{ route('shop_settings_orders', ['dates' => 'month']) }}"> за месяц </a>
    <hr>
    <div class="statistic">
        <p>
            отчет за период с {{ $date_from->format('Y-m-d') }} по {{ $date_to->format('Y-m-d') }} <br>
            заказов - {{ $paydPeriodInfo['заказов'] }} <br>
            @isset($paydPeriodInfo['orders'])
                @foreach($paydPeriodInfo['orders'] as $keypm => $item)
                    &nbsp; &nbsp; &nbsp; <b>{{ $paymentMethod[$keypm] }}</b>  <br>
                    @foreach($item as $kps => $value)
                        <label>
                            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <input type="checkbox" name="allsumm" @if($paymentStatus[$kps] == 'PAID') checked @endif value="{{ $value['summ'] }}">
                            <span>{{ $paymentStatus[$kps] }} - </span>
                            <span class="order_summ">{{ $value['summ'] }}</span> <span>(count {{ $value['count'] }}) </span>
                        </label>
                        <a class="button" href="{{ route('shop_settings_orders', ['filter[method]' => $keypm, 'filter[status]' => $kps]) }}">
                            filter
                        </a>
                        <br>
                    @endforeach
                @endforeach
            @endisset
        </p>
        <hr>
        <p>&nbsp; &nbsp; &nbsp; <b>TOTALL - </b>
            <span class="summ_out"></span>
        </p>
    </div>
    <br>


    <form action="{{ route('shop_settings_orders') }}" method="get">
        <p>
            Search bu order id <input name="order_id" type="text"
                                      @if ($order_id)
                                      value="{{ $order_id }}"
                    @endif
            >

            <input type="submit" value="search">

            @if ($order_id && $orderSearch == false)
                <span>
                        order not found
                    </span>
            @endif
        </p>
    </form>


    <table>
        @if ($orderSearch)
            @php($item = $orderSearch)
            @include('shop-settings.layouts.order_item')
        @endif


        @if(isset($orders))
            @foreach($orders as $item)
              @include('shop-settings.layouts.order_item')
            @endforeach
        @endif
    </table>

    <div>
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 paginate">
            {{ $orders->links() }}
        </div>
    </div>

    <script>
        jQuery(document).ready(function ($) {
            // $('.pop-ap .close').click(function () {
            //     $('.pop-ap').hide();
            // });

            function setAllSumm() {
                var all_summ = 0;
                $('.statistic input:checked').each(function () {
                    var summ_item = $(this).val() / 1;
                    console.log(`summ_item - ${summ_item} + all_summ ${all_summ}`);
                    all_summ += summ_item;
                    console.log(`= ${all_summ}`);
                });
                $('.statistic .summ_out').text(all_summ.toFixed(2));
            }
            $('.statistic input').change(function () {

                setAllSumm();
            })

            setAllSumm();
        });
    </script>
@stop


