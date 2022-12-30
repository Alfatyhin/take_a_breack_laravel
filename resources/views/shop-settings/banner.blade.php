@extends('layouts.master')

@section('title', 'баннер')


@section('head')

@stop

@section('sidebar')
    @parent
@stop

@section('content')


   <form action="{{ route('banner') }}" method="POST">
       @csrf
       <div>
           <b>popap </b>
           режим попап: <br>
           <label>
               <input type="checkbox" name="banner[popapp]" value="1" @isset($banner['popapp']) checked @endisset > вкл
           </label>
       </div>
       <hr>
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


