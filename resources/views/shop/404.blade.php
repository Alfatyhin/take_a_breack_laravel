<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>

    <meta charset="UTF-8">
    <title>
        @section('title')

        @show
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="icon" href="/img/common/favicon.png" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/common-0.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/common_adaptation.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/slick.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/slick-theme.css') }}?{{ $v }}">


    <link rel="stylesheet" href="/css/404.css"?{{ $v }}">
    <link rel="stylesheet" href="/css/404_adaptation.css"?{{ $v }}">

    <meta name="robots" content="noindex, follow" />

    @section('head')

    @show

</head>
<body>

<div class="wrapper">
    @include("shop.new.layouts.header")

    <main class="main lang_{{ $lang }}">

        @section('content')

        @show

    </main>

    @include("shop.new.layouts.footer")
</div>
<script src="{{ asset('js/jquery-3.6.0.min.js') }}" defer></script>
@section('scripts')

@show

</body>
</html>

