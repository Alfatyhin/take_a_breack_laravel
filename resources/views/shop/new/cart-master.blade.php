

@extends('shop.new.shop_master')

@section('title', 'cart')

@section('head')


@stop
@section('content')

    @if($errors->any())

    @endif


@stop


@section('scripts')

    <script src="{{ asset('/assets/libs/mask-lib.js') }}?{{ $v }}" defer></script>
    <script src="{{ asset('js/calendar.js') }}?{{ $v }}" defer></script>
    <script>
        let cart = JSON.parse(localStorage.getItem("cart") || "[]");
        console.log(cart)
        //localStorage.setItem("cart", []);   //при подтвержденном заказе
    </script>
@stop

