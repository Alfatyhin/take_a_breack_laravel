
@extends('layouts.master')

@section('title', 'home')

@section('sidebar')
    @parent

@stop

@section('content')
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
    @auth
        <div>
            <p>Вы успешно вошли</p>
        </div>


    @endauth
    </div>

@stop

