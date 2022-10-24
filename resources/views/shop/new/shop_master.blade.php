<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>

    <meta charset="UTF-8">
    <title>
        @section('title')

        @show
    </title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="{{ asset('/css/style.css') }}?{{ $v }}">

    @if($noindex)
        <meta name="robots" content="noindex, follow" />
    @endif

    <script>
        var general_url = "{{ route('index_'.$lang) }}";
        console.log('cart v-{{ $v }}');
    </script>
    @section('head')

    @show

    @include('shop.layouts.seo.head-scripts')
</head>
<body>
@include('shop.layouts.seo.body_top')

<div class="wrapper">
    <div class="content">
        <div class="modal"></div>

        @include('shop.new.ru.header')

        <main class="main">
            <div class="container">
                <div class="main__wrap">

                    @section('content')

                    @show

                </div>
                @section('content_2')

                @show

            </div>
        </main>
        @include("shop.new.$lang.footer")
    </div>
    <div class="mark">
        <div>
            <a href="{{ route("cart_$lang") }}" class="mark-link">
                <img src="/assets/images/icons/bag.svg" alt="">
            </a>
            <a href="#" class="mark-link">
                <img src="/assets/images/icons/user.svg" alt="">
            </a>
            <a class="social-link" href="#">
                Facebook
            </a>
            <a class="social-link" href="#">
                Instagram
            </a>
        </div>
    </div>
</div>
@section('scripts')

@show
<script src="{{ asset('/scripts/app.js') }}?{{ $v }}" defer></script>

@include('shop.layouts.seo.footer-scripts')
@include("shop.$lang.popap_sendpulse")
</body>
</html>
