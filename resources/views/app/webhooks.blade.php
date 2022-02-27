@extends('layouts.master')

@section('title', 'webhoocks')

@section('sidebar')
    @parent
@stop

@section('content')
    <style>
        form {
            display: inline-block;
            margin: 5px 10px;
        }
    </style>
    <div class="max-w-10xl mx-auto sm:px-10 lg:px-10">

        <table>
            <tr>
                <th> id </th>
                <th> name </th>
                <th> data </th>
            </tr>
            @if(isset($webhoocks))
                @foreach($webhoocks as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>
                            {{ $item->name }}
                            <br>
                            {{ $item->created_at->format('Y-m-d H-i-s') }}
                        </td>
                        <td>
                            {{ $item->data }} <br>
                            <form action="{{ route('ecwid.webhook') }}" method="post">
                                <input type="hidden" name="data-test" value="{{$item->data}}">
                                <input class="button" type="submit" name="test data" value="test ecwid">
                            </form>
                            <form class="hidden" action="{{ route('tilda_new_order') }}" method="post">
                                <input type="hidden" name="data-test" value="{{$item->data}}">
                                <input class="button" type="submit" name="test data" value="test tilda">
                            </form>
                            <form class="hidden" action="{{ route('tilda_payment') }}" method="post">
                                <input type="hidden" name="data-test" value="{{$item->data}}">
                                <input class="button" type="submit" name="test data" value="test tilda payment">
                            </form>
                            <form action="{{ route('amo_webhook') }}" method="post">
                                <input type="hidden" name="data-test" value="{{$item->data}}">
                                <input class="button" type="submit" name="test data" value="test amo">
                            </form>
                            <form action="{{ route('order_thanks') }}" method="post">
                                <input type="hidden" name="data-test" value="{{$item->data}}">
                                <input class="button" type="submit" name="test data" value="test iCredit thanks">
                            </form>
                            <form action="{{ route('icredit_hebhook') }}" method="post">
                                <input type="hidden" name="data-test" value="{{$item->data}}">
                                <input class="button" type="submit" name="test data" value="test iCredit hebhook">
                            </form>
                            <form action="{{ route('add_new_order') }}" method="post">
                                <input type="hidden" name="data-test" value="{{$item->data}}">
                                <input class="button" type="submit" name="test data" value="test new order">
                            </form>
                            <form action="{{ route('get_new_order_id') }}" method="post">
                                <input type="hidden" name="data-test" value="{{$item->data}}">
                                <input class="button" type="submit" name="test data" value="get_new_order_id">
                            </form>
                            <form action="{{ route('paypal_capture') }}" method="post">
                                <input type="hidden" name="data-test" value="{{$item->data}}">
                                <input class="button" type="submit" name="test data" value="paypal_capture">
                            </form>
                            <form action="{{ route('api_ginvoice') }}" method="post">
                                <input type="hidden" name="data-test" value="{{$item->data}}">
                                <input class="button" type="submit" name="test data" value="api_ginvoice">
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endif
        </table>
    </div>
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 paginate">
        {{ $webhoocks->links() }}
    </div>

@stop

