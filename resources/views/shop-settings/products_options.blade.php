@extends('layouts.master')

@section('title', 'Products Options')


@section('head')

@stop

@section('sidebar')
    @parent
@stop

@section('content')

    @include('shop-settings.layouts.popapp_message')


    <div class="box_inline box_list box_border">
        Опции товаров: <br>
        <ul class="list products_list">
            @foreach($products_options as $option)
                <li class="products" data_id="{{ $option->id }}" data_name="category_{{ $option->id }}" >
                    <a>
                        {{ $option->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="box_inline content_list">
        <div class="options_header">
                    <span class="button add_option">
                        добавить параметр
                    </span>
            <div class="new_option hidden pop-ap">
                <div class="body">
                    <span class="close"></span>
                    <form action="{{ route('shop_settings_products_options_add') }}" method="POST">
                        @csrf
                        <div class="box_inline box_border">
                            наименование:
                            <input type="text" name="name" value="">
                        </div>
                        <div class="box_inline box_border">
                            тип:
                            <select name="type">
                                @foreach($options_select as $ks => $item)
                                    <option value="{{ $ks }}" >{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="box_block">

                            <p>Переводы</p>
                            @foreach($shop_langs as $kl => $lang_data)
                                <div class="box_block box_border">
                                    <p>Язык: {{ $lang_data['name'] }}</p>
                                    <div class="box_inline">
                                        наименование: <br>
                                        <input type="text" name="nameTranslate[{{ $kl }}]" value="">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p>
                            <input class="button" type="submit" value="добавить параметр">
                        </p>
                    </form>

                </div>
            </div>

        </div>
        @foreach($products_options as $option)
            @php($nameTranslate = json_decode($option->nameTranslate))
            <div class="content_item category_{{ $option->id }}" data_name="{{ $option->id }}" >
                <h2>{{ $option->name }}</h2>
                <form class="" action="{{ route('shop_settings_products_option_save', ['option' => $option]) }}" method="POST">
                    @csrf
                    <div class="">
                        <div class="box_inline box_border">
                            наименование:
                            <input type="text" name="name" value="{{ $option->name }}" >{{ $option->name }}

                            <hr>
                            <p class="opening_box plus">Переводы</p>
                            <div class="open_box closed">
                                @foreach($shop_langs as $kl => $lang_data)
                                    @php($nameTranslate = json_decode($option->nameTranslate, true))
                                    @php($name = $nameTranslate[$kl])
                                    <div class="">
                                        {{ $lang_data['name'] }} <br>
                                        <input type="text" name="nameTranslate[{{ $kl }}]" value="{{ $name }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="box_inline box_border">
                            тип: {{ $option->type }}
                            <select name="type">
                                @foreach($options_select as $ks => $item)
                                    @if($option->type == $ks)
                                        <option value="{{ $ks }}" selected>{{ $item }}</option>
                                    @else

                                        <option value="{{ $ks }}" >{{ $item }}</option>
                                    @endif

                                @endforeach
                            </select>
                        </div>
                        <div class="box_block">
                            <p>
                                <input type="submit" value="сохранить">
                            </p>
                            <table>
                                <tr>
                                    <th>
                                        название
                                    </th>
                                    <th>
                                        описание
                                    </th>
                                    @if($option->type != 'TEXT')
                                        <th>
                                            метрики
                                        </th>
                                    @endif
                                    <th>

                                    </th>
                                </tr>


                                @php($option_values = json_decode($option->options, true))
                                @if(!empty($option_values))
                                    @foreach($option_values as $kc => $choice)
                                        <tr>

                                            <td>
                                                значение:
                                                @isset($choice['text'])
                                                    <input type="text" name="options[{{ $kc }}][text]" value="{{ $choice['text'] }}">
                                                @else
                                                    <input type="text" name="options[{{ $kc }}][text]" >
                                                @endisset
                                                <hr>
                                                <p class="opening_box plus">Переводы</p>
                                                <div class="open_box closed">
                                                    @foreach($shop_langs as $kl => $lang_data)
                                                        <div class="text-right">
                                                            <p>{{ $lang_data['name'] }}
                                                                @isset($choice['textTranslated'][$kl])
                                                                    <input name="options[{{ $kc }}][textTranslated][{{ $kl }}]"
                                                                           value="{{ $choice['textTranslated'][$kl] }}"
                                                                           placeholder="{{ $choice['text'] }}">
                                                                @else
                                                                    <input name="options[{{ $kc }}][textTranslated][{{ $kl }}]"
                                                                           value=""
                                                                           placeholder="{{ $choice['text'] }}">
                                                                @endisset
                                                            </p>
                                                        </div>
                                                    @endforeach
                                                </div>

                                            </td>
                                            <td>
                                                @foreach($shop_langs as $kl => $lang_data)
                                                    <div class="text-right">
                                                        <p>{{ $lang_data['name'] }}
                                                            @isset($choice['description'][$kl])
                                                                <input name="options[{{ $kc }}][description][{{ $kl }}]"
                                                                       value="{{ $choice['description'][$kl] }}"
                                                                       placeholder="description">
                                                            @else
                                                                <input name="options[{{ $kc }}][description][{{ $kl }}]"
                                                                       value=""
                                                                       placeholder="description">
                                                            @endisset
                                                        </p>
                                                    </div>
                                                @endforeach
                                            </td>

                                            @if($option->type != 'TEXT')
                                                <td>
                                                    @isset($choice['metrics'])

                                                        @foreach($choice['metrics'] as $mk => $metric)
                                                            @foreach($shop_langs as $kl => $lang_data)
                                                                <div class="text-right">
                                                                    <p>{{ $lang_data['name'] }}
                                                                        @isset($metric[$kl])
                                                                            <input name="options[{{ $kc }}][metrics][{{ $mk }}][{{ $kl }}]"
                                                                                   value="{{ $metric[$kl] }}"
                                                                                   placeholder="metrics">
                                                                        @else
                                                                            <input name="options[{{ $kc }}][metrics][{{ $mk }}][{{ $kl }}]"
                                                                                   value=""
                                                                                   placeholder="metrics">
                                                                        @endisset
                                                                    </p>
                                                                </div>
                                                            @endforeach
                                                        @endforeach
                                                    @else
                                                        <p>add metrics</p>
                                                        @foreach($shop_langs as $kl => $lang_data)
                                                            <div class="text-right">
                                                                <p>{{ $lang_data['name'] }}
                                                                    <input name="options[{{ $kc }}][metrics][0][{{$kl}}]"
                                                                           value=""
                                                                           placeholder="metrics">
                                                                </p>
                                                            </div>
                                                        @endforeach
                                                    @endisset

                                                </td>
                                            @endif

                                            <td>
                                                <span class="fa fa-trash button"></span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    @if($option->type == 'TEXT')
                                        <tr>
                                            <th colspan="5">

                                            </th>
                                        </tr>
                                        <tr>
                                            <td>
                                                значение:
                                                <input type="text" name="options[0][text]" value="test_text">
                                            </td>


                                            <td>


                                            </td>
                                        </tr>
                                    @endif
                                @endif


                                @if($option->type != 'TEXT')
                                    <tr>
                                        <th colspan="5">
                                            для добавления значения параметра заполнить поля ниже и нажать
                                            <span class="fa fa-plus"></span>
                                        </th>
                                    </tr>
                                    <tr>
                                        @php($kc++)
                                        <td>
                                            значение:
                                            <input type="text" name="options[{{ $kc }}][text]" >
                                        </td>


                                        <td>

                                        </td>

                                        <td>

                                        </td>
                                        <td>

                                            <button type="submit">
                                                <span class="fa fa-plus button"></span>
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            </table>


                        </div>
                    </div>
                    <p>
                        <input type="submit" value="сохранить">
                    </p>
                </form>

            </div>

        @endforeach
    </div>
@stop


