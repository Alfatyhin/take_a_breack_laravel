@extends('layouts.master')

@section('title', 'Заказы')


@section('head')



@stop

@section('sidebar')
    @parent
@stop

@section('content')

    <form action="{{ route('image_download') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="image">
        <input type="submit" value="загрузить">
    </form>

@stop


