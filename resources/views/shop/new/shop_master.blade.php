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
    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/style-2.css') }}?{{ $v }}">
    <link rel="stylesheet" href="{{ asset('/css/style.css') }}?{{ $v }}">

    @auth()
        @if(Auth::user()->user_role == 'admin' )
            <style>
                a.admin {
                    position: absolute;
                    right: 9%;
                    display: block;
                    width: 30px;
                    height: 20px;
                }
            </style>
        @endif
    @endauth

    @if($noindex)
        <meta name="robots" content="noindex, follow" />
    @endif

    <script>
        console.log('cart v-{{ $v }}');
        var lang = '{{ $lang }}';
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

        @include('shop.new.layouts.header')

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
        <section class="popup">
            <div class="popup__content">
                <div class="close">
                    <span></span>
                    <span></span>
                </div>
                @section('popup')

                @show
            </div>
        </section>


        @include("shop.new.layouts.footer")
    </div>

    <div class="mark">
        <div class="right_user_login_block">
            <div>
                <a href="{{ route("cart", ['lang' => $lang]) }}" class="mark-link cart">
                    <img src="/assets/images/icons/bag.svg" alt="">
                    @include('shop.new.layouts.components.bag-badge')
                </a>
            </div>
            <div>
                <a href="#" class="mark-link">
                    <img src="/assets/images/icons/user.svg" alt="">
                </a>
            </div>
            <div>
                <ul class="lang_select mark_lang">
                    @include('shop.new.layouts.components.lang-select')
                </ul>
            </div>
            <div>
                <a class="social-link" href="#">Facebook</a>
            </div>
            <div>
                <a class="social-link" href="#">Instagram</a>
            </div>
            @auth()
                @if(Auth::user()->user_role == 'admin' )
                    <div>
                        <a class="admin fa fa-adn" title="CRM" href="{{ route('crm_index') }}" target="_blank">
                        </a>
                    </div>
                @endif
            @endauth
        </div>
    </div>
</div>
@section('scripts')

@show
<script src="{{ asset('/scripts/app.js') }}?{{ $v }}" defer></script>

@include('shop.layouts.seo.footer-scripts')
@include("shop.new.layouts.send_pulse.$lang.popap_sendpulse")
</body>
</html>
