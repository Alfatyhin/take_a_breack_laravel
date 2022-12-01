
@extends('layouts.master')

@section('title')
    {{ $title }}
@stop
@section('sidebar')
    @parent

@stop

@section('content')
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
       @foreach( $messages as $type => $item )
           <div > {!! $item !!}  </div>
       @endforeach
    </div>
@stop
