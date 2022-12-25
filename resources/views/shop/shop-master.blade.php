<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>

    <meta charset="UTF-8">
    <title>
        @section('title')

        @show
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="icon" href="/img/common/logo.png" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/common-0.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/common_adaptation.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/slick.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/slick-theme.css') }}?{{ $v }}">

    @if($noindex)
        <meta name="robots" content="noindex, follow" />
    @endif

    <script>
        console.log('cart v-{{ $v }}');
    </script>
    @section('head')

    @show

</head>
<body>

@include('shop.layouts.seo_delete.body_top')
<div class="wrapper">

    <main class="main lang_{{ $lang }}">

        @section('content')

        @show

    </main>

    @include("shop.$lang.header")
{{--    @include("shop.$lang.footer")--}}
</div>
<script src="{{ asset('js/imask.min.js') }}" defer></script>
<script src="{{ asset('js/jquery-3.6.0.min.js') }}" defer></script>
<script src="{{ asset('js/slick-1.8.1/slick/slick.js') }}" defer></script>
<script src="{{ asset('js/common.js') }}?{{ $v }}" defer></script>
@section('scripts')

@show

@include('shop.layouts.seo_delete.footer-scripts')
@include("shop.$lang.popap_sendpulse")
</body>
</html>
