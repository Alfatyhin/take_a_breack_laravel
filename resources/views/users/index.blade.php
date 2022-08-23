@extends('layouts.master')

@section('title', 'users')

@section('sidebar')
    @parent
@stop

@section('content')
    <div class="max-w-10xl mx-auto sm:px-10 lg:px-10">

        <p>
            <a class="button" href="{{ route('register') }}">create account</a>
        </p>
        <hr>
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
                        <td>
                            <form action="{{ route('users', ['user_id' => $user->id]) }}" method="POST">
                                @csrf
                                <select name="user_role">
                                    @foreach($user_roles as $role)
                                        @if ($user->user_role == $role)
                                            <option value="{{ $role }}" selected>
                                                {{ $role }}
                                            </option>
                                        @else
                                            <option value="{{ $role }}" >
                                                {{ $role }}
                                            </option>
                                        @endif
                                    @endforeach
                                    <option value="delete" >
                                        delete
                                    </option>
                                </select>
                                <br>
                                <input type="submit" name="user_change" value="изменить">
                            </form>
                        </td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            @endif
        </table>
    </div>
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 paginate">
        {{ $users->links() }}
    </div>
@stop

