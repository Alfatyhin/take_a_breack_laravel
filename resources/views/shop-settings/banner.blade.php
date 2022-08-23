@extends('layouts.master')

@section('title', 'Заказы')


@section('head')

@stop

@section('sidebar')
    @parent
@stop

@section('content')

   @include('shop-settings.layouts.popapp_message')

   <form action="{{ route('banner') }}" method="POST">
       @csrf
       <div>
           <b>Язык: en </b>
           Текст: <br>
           <textarea name="banner[en]">{{ $banner['en'] }}</textarea>
       </div>
       <hr>
       <div>
           <b>Язык: ru </b>

           Текст: <br>
           <textarea name="banner[ru]">{{ $banner['ru'] }}</textarea>
       </div>
       <hr>
       <div>
           <b>Язык: he </b>

           Текст: <br>
           <textarea name="banner[he]">{{ $banner['he'] }}</textarea>
       </div>
       <input type="submit" >
   </form>

@stop


