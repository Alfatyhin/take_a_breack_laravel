@extends('layouts.master')

@section('title', 'iCredit')

@section('sidebar')
    @parent
@stop

@section('content')
    <div class="max-w-10xl mx-auto sm:px-10 lg:px-10">

        <p> Input ecwid order id </p>

        <form action="{{ route('icredit_index') }}" method="get">
            @csrf
            <input type="text" name="orderId">

            <input class="button" type="submit" name="send" value="search">
        </form>

        <table>
            <tr>
                <th>ecwid Id</th>
                <th>payment Status</th>
                <th>payment data</th>
            </tr>
            @if(isset($icredit))
                @foreach($icredit as $item)
                    <tr>
                        <td>
                            <a class="border-bottom" href="https://my.ecwid.com/store/48198100#order:id={{ $item->ecwidId }}&return=orders" target="_blank">
                                {{ $item->orderId }}
                            </a>

                        </td>
                        <td>{{ $item->paymentStatus }}</td>

                        <td>
                            @php
                                $data = json_decode($item->data, true);
                            @endphp

{{--                            @foreach($data as $key => $itemData)--}}
{{--                                ( {{ $key }} = {{ $itemData }} ) &nbsp; &nbsp; &nbsp;--}}
{{--                            @endforeach--}}
                        </td>


                    </tr>
                @endforeach
            @endif
        </table>
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 paginate">
            {{ $icredit->links() }}
        </div>
    </div>

@stop

