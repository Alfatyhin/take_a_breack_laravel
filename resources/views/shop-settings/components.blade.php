@extends('layouts.master')

@section('title', 'Products Options')


@section('head')

    <style>
        textarea {
            width: 800px;
            height: 300px;
        }
        .pre {
            max-width: 300px;
        }
    </style>
@stop

@section('sidebar')
    @parent
@stop

@section('content')


    <div class="box_inline">
        <div class="content_list_menu">
            <div>
                <span class="box_data  active " data_name="box_list_1">sendpulse</span>
            </div>
        </div>


        <div class="content_list">


                <div class="content_item box_list content_item box_list_1 active " data_name="box_list_1" >
                    <h2>sendpulse</h2>

                    <div class="box_inline box_list">
                        <ul class="list options_list">

                            @foreach($files as $k => $item)
                                <li data_name="option_{{ $k }}" data_name="{{ $k }}" >
                                    <a>{{ $item['name'] }}</a>
                                </li>
                            @endforeach

                        </ul>
                    </div>

                    <div class="box_inline content_list">

                        @foreach($files as $k => $item)
                            <div class="content_item option_{{ $k }} ">
                                <form class="" action="" method="POST">
                                    @csrf
                                    <input hidden name="file_path" value="{{ $item['path'] }}">
                                    <p>
                                        {{ $item['name'] }}
                                    </p>
                                    <p>
                                        <input type="submit" value="сохранить">
                                    </p>
                                    <p>
                                        <textarea name="content">{!! $item['content'] !!}</textarea>
                                    </p>
                                    <p>
                                        <input type="submit" value="сохранить">
                                    </p>
                                </form>
                            </div>
                        @endforeach
                    </div>


                </div>

        </div>
    </div>
@stop


