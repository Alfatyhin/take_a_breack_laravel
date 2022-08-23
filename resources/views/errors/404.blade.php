@extends('shop.404')

@section('title', '404')

@section('head')

    <link rel="stylesheet" href="/css/404.css">
    <link rel="stylesheet" href="/css/404_adaptation.css">

@stop

@section('content')


    <section class="page404">
        <div class="container">
            <div class="page404__body">
                <div class="page404__title">404</div>
                @if($lang == 'ru')
                    <div class="page404__subtitle">
                        <p>Такая страница не найдена, возможно, она была перемещена или удалена</p>
                    </div>
                    <div class="page404__text">
                        <p>Вы легко можете найти всю необходимую информацию на сайте</p>
                    </div><a class="page404__btn" href="{{ route("index_$lang") }}">на главную страницу</a>
                @else
                    <div class="page404__subtitle">
                        <p>This page was not found, it may have been moved or deleted</p>
                    </div>
                    <div class="page404__text">
                        <p>You can easily find all the information you need on the website</p>
                    </div><a class="page404__btn" href="{{ route("index_$lang") }}">back to main page</a>
                @endif
            </div>
        </div>
        </div>
    </section>

@stop



@section('scripts')
@stop

