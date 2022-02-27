
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <style>
        .body_content {
            width: max-content;
            margin: 5em auto;
            margin-top: 5em;
            font-size: 110%;
            border: 1px groove;
            border-radius: 1em;
            overflow: hidden;
        }
        header {
            background: #00c091;
            padding: 5px 40px;
        }
        h1 {
            margin: 0;
            text-align: center;
        }
        .content {
            padding: 20px 40px;
        }
        .right {
            text-align: right;
        }
        .pre {
            white-space: pre;
        }
        .lang_he {
            direction: rtl;
        }
        @media screen and (max-width: 560px) {
            .body_content {
                width: 100%;
            }
            .content {
                padding: 20px 10px;
            }
            .pre {
                white-space: pre-line;
            }
        }
    </style>
</head>
<body class="antialiased min-h-screen">
<div class="body_content">
@section('sidebar')

    <!-- Page Heading -->
        <header class="bg-white shadow">
            <div class="">
                <h1>@yield('title')</h1>
            </div>
        </header>

    @show

    <div class="content">
        @section('content')

        @show
    </div>

</div>

</body>
</html>

