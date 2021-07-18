@extends('layouts.master')

@section('title', 'clients')

@section('sidebar')
    @parent
@stop

@section('content')
    <div class="max-w-10xl mx-auto sm:px-10 lg:px-10">

        <table>
            <tr>
                <th>id</th>
                <th>name</th>
                <th>email</th>
                <th>tel</th>
                <th>amoId</th>
            </tr>
            @if(isset($clients))
                @foreach($clients as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>{{ $user->amoId }}</td>
                    </tr>
                @endforeach
            @endif
        </table>

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 paginate">
            {{ $clients->links() }}
        </div>
    </div>
@stop

