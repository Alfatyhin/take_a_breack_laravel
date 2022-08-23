@extends('layouts.master')

@section('title', 'invoice-setting')


@section('head')


@stop

@section('sidebar')
    @parent
@stop

@section('content')


    <h1> Invoice Setting </h1>

    <form method="get" >
        @csrf

        <p>
            Выбрать Greeninvoice аккаунт для PayPal
        </p>

        <label>
            <input type="radio" name="invoice_mode_paypal" value="1"
                   @if ($settingData['invoice_mode_paypal'] == 1)
                   checked
                @endif
            /> Евгений
        </label>
        <br>

        <label>
            <input type="radio" name="invoice_mode_paypal" value="2"
                   @if ($settingData['invoice_mode_paypal'] == 2)
                   checked
                @endif
            /> Элизабет Рейчел
        </label>
        <br>




        <hr>
        <p>
            Выбрать Greeninvoice аккаунт для Cache
        </p>

        <label>
            <input type="radio" name="invoice_mode_cache" value="1"
                   @isset($settingData['invoice_mode_cache'])
                   @if ($settingData['invoice_mode_cache'] == 1)
                   checked
                @endif
                @endisset
            /> Евгений
        </label>
        <br>

        <label>
            <input type="radio" name="invoice_mode_cache" value="2"
                   @isset($settingData['invoice_mode_cache'])
                   @if ($settingData['invoice_mode_cache'] == 2)
                   checked
                @endif
                @endisset
            /> Элизабет Рейчел
        </label>
        <br>



        <hr>
        <p>
            Выбрать Greeninvoice аккаунт для Bit
        </p>

        <label>
            <input type="radio" name="invoice_mode_bit" value="1"
                   @isset($settingData['invoice_mode_bit'])
                   @if ($settingData['invoice_mode_bit'] == 1)
                   checked
                @endif
                @endisset
            /> Евгений
        </label>
        <br>

        <label>
            <input type="radio" name="invoice_mode_bit" value="2"
                   @isset($settingData['invoice_mode_bit'])
                   @if ($settingData['invoice_mode_bit'] == 2)
                   checked
                @endif
                @endisset
            /> Элизабет Рейчел
        </label>
        <br>

        <input type="submit" value="save">

    </form>

@stop


