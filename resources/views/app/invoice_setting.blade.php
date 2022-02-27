@extends('layouts.master')

@section('title', 'InvoiceSetting')

@section('sidebar')
@parent
@stop

@section('content')
<div class="max-w-10xl mx-auto sm:px-10 lg:px-10">

    <h1> Invoice Setting </h1>

    <form method="get" >
        @csrf

        <p>
            Выбрать Greeninvoice аккаунт для PayPal
        </p>

        <input type="radio" name="invoice_mode_paypal" value="1"
               @if ($settingData['invoice_mode_paypal'] == 1)
               checked
            @endif
            /> Евгений <br>

        <input type="radio" name="invoice_mode_paypal" value="2"
               @if ($settingData['invoice_mode_paypal'] == 2)
               checked
            @endif
        /> Элизабет Рейчел <br>

        <hr>
        <p>
            Выбрать Greeninvoice аккаунт для Cache
        </p>

        <input type="radio" name="invoice_mode_cache" value="1"
               @isset($settingData['invoice_mode_cache'])
               @if ($settingData['invoice_mode_cache'] == 1)
               checked
            @endif
            @endisset
            /> Евгений <br>

        <input type="radio" name="invoice_mode_cache" value="2"
               @isset($settingData['invoice_mode_cache'])
               @if ($settingData['invoice_mode_cache'] == 2)
               checked
            @endif
            @endisset
        /> Элизабет Рейчел <br>

        <input type="submit" value="save">

    </form>

</div>

@stop


