<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="icon" href="/img/common/favicon.png" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/common_adaptation.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/payOrder.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('css/payOrder_adaptation.css') }}?{{ $v }}">

    <script>
        var general_url = "{{ route('index_'.$lang) }}";
    </script>



@section('head')

    @show

    @include('shop.layouts.seo_delete.head-scripts')
</head>

<body>
{{--<script>--}}
{{--    fbq('track', 'Purchase', {--}}
{{--        value: {{ $order->orderPrice }},--}}
{{--        currency: 'ILS'--}}
{{--    });--}}
{{--</script>--}}

<div class="payOrder">
    <div class="payOrder__bgShadow"></div>
    <div class="container">

        @section('content')

        @show

    </div>
</div>
<script src="{{ asset('js/jquery-3.6.0.min.js') }}" defer></script>
<script src="{{ asset('js/common.js') }}?{{ $v }}" defer></script>
<script src="{{ asset('js/payOrder.js') }}?{{ $v }}" defer></script>
<script>
    var client = @json($client);

</script>
@section('scripts')
@show
</body>
</html>
