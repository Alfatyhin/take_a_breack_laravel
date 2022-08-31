<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} @yield('title')</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css?v1.01.09') }}">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
    <script src="{{ asset('js/main.js?v1.0.0') }}" defer></script>


    <link rel="stylesheet" href="{{ asset('css/shop-settings.css') }}?v1.04">
    <script src="{{ asset('js/shop-settings.js') }}?v1.04" defer></script>

    @section('head')

    @show

</head>
<body class="antialiased min-h-screen">
@section('sidebar')
    <div class="hidden top-0 right-0 px-3 py-1 sm:block">
        @auth
            @include('layouts.navigation')
        @else
            <div class="float-right">
                <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">Log in</a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 underline">Register</a>
                @endif
            </div>
        @endauth
    </div>


    <!-- Page Heading -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h1>@yield('title')</h1>
        </div>
    </header>

@show

@include('shop-settings.layouts.popapp_message')

<div class="content relative items-top justify-center bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">

    @auth
        <div class="left_sidebar">
            @section('left_sidebar')
                @include('layouts.left_sidebar')
            @show
        </div>

        <div class="page_box">
            @section('content')

            @show
        </div>
    @endauth
</div>
</body>


<footer>

</footer>
</html>

