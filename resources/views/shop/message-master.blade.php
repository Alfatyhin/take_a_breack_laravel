<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="icon" href="/img/common/favicon.png" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common_adaptation.css') }}">
    <link rel="stylesheet" href="{{ asset('css/payOrder.css') }}">
    <link rel="stylesheet" href="{{ asset('css/payOrder_adaptatione.css') }}">

    <script src="{{ asset('js/jquery-3.6.0.min.js') }}" defer></script>

    @section('head')

    @show

    @include('shop.layouts.seo.head-scripts')
</head>
<body>
<div class="payOrder">
    <div class="payOrder__bgShadow"></div>
    <div class="container">

        @section('content')

        @show

    </div>
</div>

@section('scripts')

@show
</body>
</html>
