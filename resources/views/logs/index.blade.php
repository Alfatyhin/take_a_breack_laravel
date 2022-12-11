@extends('layouts.master')

@section('title', 'orders logs')

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
        @if ($date_pre)
            <a class="button" href="{{ route($route, ['date' => $date_pre->format('Y-m-d')]) }}">{{  $date_pre->format('Y-m-d') }}</a>
        @endif
    </p>



    @if (!empty($log))
        <div class="log">
            {!! $log !!}
        </div>
    @endif

@stop

