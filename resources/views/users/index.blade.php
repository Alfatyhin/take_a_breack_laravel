@extends('layouts.master')

@section('title', 'users')

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
                <th>role</th>
                <th>date reg</th>
            </tr>
            @if(isset($users))
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->user_role }}</td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            @endif
        </table>
    </div>
@stop

