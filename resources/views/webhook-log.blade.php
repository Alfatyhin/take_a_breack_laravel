@extends('layouts.master')

@section('title', 'orders')

@section('sidebar')
    @parent
@stop

@section('content')
    <div class="max-w-10xl mx-auto sm:px-10 lg:px-10">
        <p>
            общая сумма текущий месяц - оплачено на ( {{ $priceMonth }} )
            ожидает оплаты на {{ $priceMonthAwaiting }}
        </p>
        <p>
            общая сумма за весь год - оплачено на ( {{ $priceYear }} )
            ожидает оплаты на {{ $priceYearAwaiting }}
        </p>
        <table>
            <tr>
                <th>id</th>
                <th>ecwid Id</th>
                <th>ecwid Status</th>
                <th>amoId</th>
                <th>amo Status</th>
                <th>payment Method</th>
                <th>payment Status</th>
                <th>order Price</th>
                <th>invoice Status</th>
                <th>client info</th>
            </tr>
            @if(isset($orders))
                @foreach($orders as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->ecwidId }}</td>
                        <td>{{ $item->ecwidStatus }}</td>
                        <td>
                            @if(empty($item->amoId))
                                <a href="{{ route('amo.create.lead', ['id' => $item->ecwidId]) }}" >
                                    create amo lead
                                </a>
                            @endif
                            {{ $item->amoId }}
                        </td>
                        <td>{{ $item->amoStatus }}</td>
                        <td>{{ $paymentMethod[$item->paymentMethod] }}</td>
                        <td>{{ $paymentStatus[$item->paymentStatus] }}</td>
                        <td>{{ $item->orderPrice }}</td>
                        <td>{{ $invoiceStatus[$item->invoiceStatus] }}</td>
                        <td>
                            {{ $item->name }} <br>
                            {{ $item->email }}
                        </td>
                    </tr>
                @endforeach
            @endif
        </table>
    </div>
@stop

