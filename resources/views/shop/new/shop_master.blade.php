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
{{--    <meta name="viewport" content="width=device-width, initial-scale=1.0">--}}
    <meta name="viewport" content="width=device-width, user-scalable=no">
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
        console.log('v-{{ $v }}');
        var lang = '{{ $lang }}';
    </script>
    @section('head')

    @show

    @include('shop.new.layouts.scripts.head-scripts')
</head>
<body>
@include('shop.new.layouts.scripts.body_top')

@if(isset($banner['banner']) && !empty($banner[$lang]))
    <div class="banner">
        {!! $banner[$lang] !!}
    </div>
@endif

<div class="wrapper">
    <div class="content">
        <div class="modal"></div>

        @include('shop.new.layouts.header')

        <main class="main">
            <div class="container">
                <div class="main__wrap @section('page_class')@show">

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
                    <span>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.4099 12L17.7099 7.71C17.8982 7.5217 18.004 7.2663 18.004 7C18.004 6.7337 17.8982 6.47831 17.7099 6.29C17.5216 6.1017 17.2662 5.99591 16.9999 5.99591C16.7336 5.99591 16.4782 6.1017 16.2899 6.29L11.9999 10.59L7.70994 6.29C7.52164 6.1017 7.26624 5.99591 6.99994 5.99591C6.73364 5.99591 6.47824 6.1017 6.28994 6.29C6.10164 6.47831 5.99585 6.7337 5.99585 7C5.99585 7.2663 6.10164 7.5217 6.28994 7.71L10.5899 12L6.28994 16.29C6.19621 16.383 6.12182 16.4936 6.07105 16.6154C6.02028 16.7373 5.99414 16.868 5.99414 17C5.99414 17.132 6.02028 17.2627 6.07105 17.3846C6.12182 17.5064 6.19621 17.617 6.28994 17.71C6.3829 17.8037 6.4935 17.8781 6.61536 17.9289C6.73722 17.9797 6.86793 18.0058 6.99994 18.0058C7.13195 18.0058 7.26266 17.9797 7.38452 17.9289C7.50638 17.8781 7.61698 17.8037 7.70994 17.71L11.9999 13.41L16.2899 17.71C16.3829 17.8037 16.4935 17.8781 16.6154 17.9289C16.7372 17.9797 16.8679 18.0058 16.9999 18.0058C17.132 18.0058 17.2627 17.9797 17.3845 17.9289C17.5064 17.8781 17.617 17.8037 17.7099 17.71C17.8037 17.617 17.8781 17.5064 17.9288 17.3846C17.9796 17.2627 18.0057 17.132 18.0057 17C18.0057 16.868 17.9796 16.7373 17.9288 16.6154C17.8781 16.4936 17.8037 16.383 17.7099 16.29L13.4099 12Z" fill="#222222" style=""></path>
                        </svg>
                    </span>

                </div>
                @section('popup')
                @show
            </div>
        </section>


        @if(isset($banner['popapp']) && !empty($banner[$lang]))
            <section class="popup_site_message" style="display: block" >

                <div class="popup__content">
                    <div class="close">
                       <span>
                           <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                               <path d="M13.4099 12L17.7099 7.71C17.8982 7.5217 18.004 7.2663 18.004 7C18.004 6.7337 17.8982 6.47831 17.7099 6.29C17.5216 6.1017 17.2662 5.99591 16.9999 5.99591C16.7336 5.99591 16.4782 6.1017 16.2899 6.29L11.9999 10.59L7.70994 6.29C7.52164 6.1017 7.26624 5.99591 6.99994 5.99591C6.73364 5.99591 6.47824 6.1017 6.28994 6.29C6.10164 6.47831 5.99585 6.7337 5.99585 7C5.99585 7.2663 6.10164 7.5217 6.28994 7.71L10.5899 12L6.28994 16.29C6.19621 16.383 6.12182 16.4936 6.07105 16.6154C6.02028 16.7373 5.99414 16.868 5.99414 17C5.99414 17.132 6.02028 17.2627 6.07105 17.3846C6.12182 17.5064 6.19621 17.617 6.28994 17.71C6.3829 17.8037 6.4935 17.8781 6.61536 17.9289C6.73722 17.9797 6.86793 18.0058 6.99994 18.0058C7.13195 18.0058 7.26266 17.9797 7.38452 17.9289C7.50638 17.8781 7.61698 17.8037 7.70994 17.71L11.9999 13.41L16.2899 17.71C16.3829 17.8037 16.4935 17.8781 16.6154 17.9289C16.7372 17.9797 16.8679 18.0058 16.9999 18.0058C17.132 18.0058 17.2627 17.9797 17.3845 17.9289C17.5064 17.8781 17.617 17.8037 17.7099 17.71C17.8037 17.617 17.8781 17.5064 17.9288 17.3846C17.9796 17.2627 18.0057 17.132 18.0057 17C18.0057 16.868 17.9796 16.7373 17.9288 16.6154C17.8781 16.4936 17.8037 16.383 17.7099 16.29L13.4099 12Z" fill="#222222" style=""></path>
                           </svg>
                       </span>

                    </div>
                    {!! $banner[$lang] !!}
                </div>
            </section>
        @endif


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
            {{--            <div>--}}
            {{--                <a href="#" class="mark-link">--}}
            {{--                    <img src="/assets/images/icons/user.svg" alt="">--}}
            {{--                </a>--}}
            {{--            </div>--}}
            <div>
                <ul class="lang_select mark_lang">
                    @include('shop.new.layouts.components.lang-select')
                </ul>
            </div>
            <div>
                <a class="social-link" href="https://www.facebook.com/TABdesserts/">Facebook</a>
            </div>
            <div>
                <a class="social-link" href="https://www.instagram.com/takeabreak_desserts/">Instagram</a>
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
<script src="{{ asset('/scripts/app-2.js') }}?{{ $v }}" defer></script>
<script src="{{ asset('/scripts/app.js') }}?{{ $v }}" defer></script>

@include('shop.new.layouts.scripts.footer-scripts')
@include("shop.new.layouts.send_pulse.$lang.popap_sendpulse")
</body>
</html>
