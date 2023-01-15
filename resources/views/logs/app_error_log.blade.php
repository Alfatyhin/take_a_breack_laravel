@extends('layouts.master')

@section('title', 'app error log')

@section('head')

    <style>
        .log {
            width: 98%;
            max-width: 84vw;
            overflow-wrap: break-word;
        }
    </style>
@stop

@section('sidebar')
    @parent
@stop

@section('content')

    <p>
        @if (!empty($log))
            <a class="button" href="{{ route('app_log', ['clear' => 1]) }}">clear log</a>
        @endif
    </p>



    @if (!empty($log))
        <div class="log">
            {!! $log !!}
        </div>
    @endif

@stop

