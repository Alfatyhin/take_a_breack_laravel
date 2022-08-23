
<link rel="stylesheet" href="{{ asset('css/areasAndPrices.css') }}?{{ $v }}">
<link rel="stylesheet" href="{{ asset('css/areasAndPrices_adaptation.css') }}?{{ $v }}">
<link rel="stylesheet" href="{{ asset('css/cart.css') }}?{{ $v }}">
<link rel="stylesheet" href="{{ asset('css/cart-2.css') }}?{{ $v }}">
<link rel="stylesheet" href="{{ asset('css/cart_adaptation.css') }}?{{ $v }}">
<link rel="stylesheet" href="{{ asset('css/cart_adaptation-2.css') }}?{{ $v }}">
<link rel="stylesheet" href="{{ asset('css/calendar.css') }}?{{ $v }}">

<script>
    var get_promo_code_url = "{{ route('check_promo_code') }}";
    var shop_setting = {!! $shop_setting !!};
    var lang = '{{ $lang }}';
    var cityes = @json($cityes);
</script>

