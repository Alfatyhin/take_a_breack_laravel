@extends('layouts.master')

@section('title', 'Products Options')


@section('head')

    <style>
        textarea {
            width: 400px;
            height: auto;
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

    @include('shop-settings.layouts.popapp_message')


    <div class="box_inline">
        <div class="content_list_menu">
            <div>
                @foreach($shop_langs as $klang => $item)
                    <span class="box_data @if($loop->first) active @endif" data_name="box_list_{{ $klang }}">{{ $item['name_ru'] }}</span>
                @endforeach
            </div>
        </div>


        <div class="content_list">

            @foreach($shop_langs as $klang => $item)

                <div class="content_item box_list content_item box_list_{{ $klang }} @if($loop->first) active @endif" data_name="box_list_{{ $klang }}" >
                    <h2>{{ $item['name_ru'] }}</h2>

                    <div class="box_inline box_list">
                        <ul class="list options_list">

                            @foreach($files[$klang]['names'] as $k => $file_path)
                                <li data_name="option_{{ $k }}" data_name="{{ $k }}" >
                                    <a>{{ $file_path }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="box_inline content_list">
                        @foreach($files[$klang]['names'] as $k => $file_path)
                            <div class="content_item option_{{ $k }} ">
                                <form class="" action="" method="POST">
                                    @csrf
                                    <input hidden name="file_path" value="{{ $file_path }}">
                                    <p>
                                        {{ $file_path }}
                                    </p>
                                    <table>
                                        <tr>
                                            <td>
                                                ключ строки <br>
                                                <small>(*может быть коротким)</small>
                                            </td>
                                            <td>
                                                значение перевода
                                            </td>
                                        </tr>
                                        @foreach($files[$klang]['contents'][$file_path] as $kstr => $str)
                                            @if(!empty($kstr))
                                                <tr>
                                                    <td>
                                                        <div class="pre">{{ $kstr }}</div>
                                                    </td>
                                                    <td>
                                                        <textarea name="translite[{{ $kstr }}]">{{ $str }}</textarea>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach

                                        @if(Auth::user()->user_role == 'admin' )
                                            <tr>
                                                <th colspan="2">
                                                    заполнить для добавления *
                                                </th>

                                            </tr>
                                            <tr>
                                                <td>
                                                    <textarea name="translite_add[key]"></textarea>
                                                </td>
                                                <td>
                                                    <textarea name="translite_add[value]"></textarea>
                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                    <p>
                                        <input type="submit" value="сохранить">
                                    </p>
                                </form>
                            </div>
                        @endforeach

                    </div>


                </div>

            @endforeach
        </div>
    </div>
@stop


