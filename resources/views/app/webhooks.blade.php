@extends('layouts.master')

@section('title', 'webhoocks')

@section('sidebar')
    @parent
@stop

@section('content')
    <div class="max-w-10xl mx-auto sm:px-10 lg:px-10">

        <table>
            <tr>
                <th> id </th>
                <th> name </th>
                <th> data </th>
                <th> date </th>
            </tr>
            @if(isset($webhoocks))
                @foreach($webhoocks as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>
                            {{ $item->data }} <br>
                            <form action="{{ route('ecwid.webhook') }}" method="post">
                                <input type="hidden" name="data-test" value="{{$item->data}}">
                                <input type="submit" name="test data">
                            </form>
                        </td>
                        <td>{{ $item->created_at->format('Y-m-d H-i-s') }}</td>
                    </tr>
                @endforeach
            @endif
        </table>
    </div>
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 paginate">
        {{ $webhoocks->links() }}
    </div>

@stop

