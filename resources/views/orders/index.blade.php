@extends('layouts.master')

@section('title', 'orders')

@section('sidebar')
    @parent
@stop

@section('content')
    <div class="max-w-10xl mx-auto sm:px-10 lg:px-10">
        <p>
            общая сумма текущий месяц - оплачено на ( {{ $priceMonth }} )
            ожидает оплаты на {{ $priceMonthAwaiting }} <br>
        </p>

        <p>
            общая сумма за весь год - оплачено на ( {{ $priceYear }} )
            ожидает оплаты на {{ $priceYearAwaiting }}
        </p>

        <form method="get" action="{{ route('orders') }}" >
            start date: <input type="date" name="date-from" value="{{ $date_from->format('Y-m-d') }}">
            end date: <input type="date" name="date-to" value="{{ $date_to->format('Y-m-d') }}">
            <input type="submit" name="date_filter">
        </form>
        <p>
            отчет за период с {{ $date_from->format('Y-m-d') }} по {{ $date_to->format('Y-m-d') }} <br>
            @foreach($paydPeriodInfo as $key => $summ)
                &nbsp; &nbsp; &nbsp; {{ $key }} - {{ $summ }} <br>
            @endforeach
        </p>


        <table>
           <tr>
               <th>Order date</th>
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
                       <td>
                           {{ $item->created_at }} <br>
                           <a class="hide button" href="{{ route('delete_order', ['id' => $item->ecwidId]) }}" >delete</a>
                       </td>
                       <td>
                           <a class="border-bottom" href="https://my.ecwid.com/store/48198100#order:id={{ $item->ecwidId }}&return=orders" target="_blank">
                               {{ $item->ecwidId }}
                           </a>

                       </td>
                       <td>{{ $item->ecwidStatus }}</td>
                       <td>
                           @if(empty($item->amoId))
                               <a class="button" href="{{ route('amo.create.lead', ['id' => $item->ecwidId]) }}" >
                                   create amo lead
                               </a>
                           @endif
                           <a class="border-bottom" href="{{ 'https://takebreak.amocrm.ru/leads/detail/'.$item->amoId }}" target="_blank">
                               {{ $item->amoId }}
                           </a>
                           <br>
                               <a class="hide button" href="{{ route('amo.create.lead', ['id' => $item->ecwidId]) }}" >
                                   new amo lead
                               </a>
                       </td>
                       <td>{{ $item->amoStatus }}</td>
                       <td>{{ $paymentMethod[$item->paymentMethod] }}</td>
                       <td>
                           {{ $paymentStatus[$item->paymentStatus] }} <br>
                           {{ $item->paymentDate }}
                       </td>
                       <td>{{ $item->orderPrice }}</td>
                       <td>
                           {{ $invoiceStatus[$item->invoiceStatus] }} <br>

                           @if (!empty($item->invoiceData))
                               @php
                               ($invoicedata = json_decode($item->invoiceData))
                               @endphp

                               <a class="button" href="{{ $invoicedata->url->en }}" >invoice</a>
                           @endif

                       </td>
                       <td>
                           {{ $item->name }} <br>
                           {{ $item->email }}
                       </td>
                   </tr>
               @endforeach
           @endif
       </table>
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 paginate">
            {{ $orders->links() }}
        </div>
    </div>

@stop

