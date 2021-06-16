
@extends('layouts.master')

@section('title', 'Ecwid Order')

@section('sidebar')
    @parent

@stop

@section('content')
    <div class="max-w-6xl mx-auto sm:px-6">
        @if(!empty($ecwidOrder))
            <h2>Ecwid Order</h2>
            <pre>
                @php
                (var_dump($ecwidOrder))
                @endphp

            </pre>
        @else
            <p> order Ecwid not found </p>
        @endif

        <hr>
        <h2>Test Order</h2>
        @if(!empty($order))
                <p> order to server isset </p>
            @else
                @if(!empty($ecwidOrder))
                    <p>
                        order to server not found -
                        <a class="button" href="{{ route('order.create_by_ecwid_id', ['orderId' => $ecwidOrder['id']]) }}" >
                            create order to server
                        </a>
                    </p>
                @else
                    <p> Order not created </p>
                @endif
            @endif
    </div>
@stop

