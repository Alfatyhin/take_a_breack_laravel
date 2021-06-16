
@extends('layouts.master')

@section('title')
    {{ $title }}
@stop
@section('sidebar')
    @parent

@stop

@section('content')
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
       @foreach( $messages as $type => $message )
           <p class="{{ $type }}"> {{ $message }} </p>
       @endforeach
    </div>
@stop
