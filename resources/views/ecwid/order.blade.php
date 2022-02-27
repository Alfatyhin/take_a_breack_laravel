
@extends('layouts.master')

@section('title', 'Ecwid Order')

@section('sidebar')
    @parent

@stop

@section('content')
    <div class="max-w-6xl mx-auto sm:px-6">
        @if(!empty($ecwidOrder))
            <h2>
                Ecwid Order
                @if ($message)
                    {{ $message }}
                @else
                    {{ $orderId }}
                @endif
            </h2>

            <p>
                Discount: {{ $discount }}
            </p>

            <hr>

            <div class="pre code short" >
                @php
                (var_dump( $ecwidOrder ))
                @endphp

                <span class="action_long">show</span>
            </div>

            <hr>
            <h2>Test Order {{ $orderId }}</h2>

            @if(!empty($order))
                <p> order {{ $orderId }} to server isset </p>
            @else
                @if(!empty($ecwidOrder) && !empty($orderId))
                    <p>
                        order to server not found -
                        <a class="button" href="{{ route('order.create_by_ecwid_id', ['orderId' => $ecwidOrder['id']]) }}" >
                            create order {{ $orderId }} to server
                        </a>
                    </p>
                @else
                    <p> Order {{ $orderId }} not created </p>
                @endif
            @endif
        @else
            <p> Input ecwid order id </p>

            <form action="{{ route('ecwid.order') }}" method="get">
                @csrf
                <input type="text" name="orderId">

                <input class="button" type="submit" name="send" value="view order">
            </form>
        @endif


    </div>
@stop

