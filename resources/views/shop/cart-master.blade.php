

@extends('shop.shop-master')

@section('title', 'cart')

@section('head')

    @include('shop.layouts.cart.header')

    <script>
            @if (old('methodPay'))
        var methodPay = true;
            @else
        var methodPay = false;
        @endif
    </script>

@stop
@section('content')

    @include("shop.$lang.master_popap")

    @if($errors->any())
        <div class="popupInfo showBlock">
            <div class="container">
                <div class="popupAreasInfo__body">  </div>
                <button class="popupAreasInfo__closeBtn popupCloseBtn" data_close="popupInfo"></button>
                <div class="popupAreasInfo__note">
                    <div class="popupAreasInfo__noteItem popupAreasInfo__noteItem--location">
                        <div class="popupAreasInfo__noteItemText">
                            @foreach ($errors->all() as $message)
                                <p style="color: brown;">{{ $message }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@stop


@section('scripts')
    <script src="{{ asset('js/cart.js') }}?{{ $v }}" defer></script>
    <script src="{{ asset('js/calendar.js') }}?{{ $v }}" defer></script>
@stop

