@extends('layouts.master')

@section('title', 'баннер')


@section('head')
{{--    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>--}}
{{--    <script>tinymce.init({ selector:'textarea.redact' });</script>--}}
@stop

@section('sidebar')
    @parent
@stop

@section('content')


   <form action="{{ route('banner') }}" method="POST">
       @csrf
       <div>
           <b>banner </b>
           <label>
               <input type="checkbox" name="banner[banner]" value="1" @isset($banner['banner']) checked @endisset > вкл
           </label>
           <br>
           <b>режим попап: </b>
           <label>
               <input type="checkbox" name="banner[popapp]" value="1" @isset($banner['popapp']) checked @endisset > вкл
           </label>
       </div>
       <hr>
       <div>
           <b>Язык: en </b>
           Текст: <br>
           <textarea class="redact" name="banner[en]">{{ $banner['en'] }}</textarea>
       </div>
       <hr>
       <div>
           <b>Язык: ru </b>

           Текст: <br>
           <textarea class="redact" name="banner[ru]">{{ $banner['ru'] }}</textarea>
       </div>
       <hr>
       <div>
           <b>Язык: he </b>

           Текст: <br>
           <textarea class="redact" name="banner[he]">{{ $banner['he'] }}</textarea>
       </div>
       <input type="submit" >
   </form>

@stop


