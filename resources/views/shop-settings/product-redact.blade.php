@extends('layouts.master')

@section('title', 'Изменить товар')

@section('head')

    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script>tinymce.init({ selector:'textarea.redact' });</script>

    <script>
        var categories = @json($categories);
    </script>

@stop
@section('sidebar')
    @parent
@stop

@section('left_sidebar')
    @parent

    Товары в этой категории: <br>
    <ul class=" products_list">
        @foreach($products as $cat_product)
            <li class="products @if(!$cat_product->enabled) disabled @endif @if($cat_product->name == $product->name) active @endif" >
                <a href="{{ route('product_redact', ['product' => $cat_product]) }}">
                    {{ $cat_product->name }}
                </a>
            </li>
        @endforeach

    </ul>
@stop
@section('content')

    <div class="box_inline">
        <div class="content_list_menu">
            <div>
                <span class="box_data active" data_name="box_list_1">Основное</span>
                <span class="box_data" data_name="box_list_2" >Параметры</span>
                <span class="box_data" data_name="box_list_3" >Вариации</span>
                <span class="box_data" data_name="box_list_4" >Атрибуты</span>
            </div>
        </div>
        <h3>Наименование : <b>{{ $product->name }}</b></h3>
        <div class="content_list">

            <div class="box_inline content_item box_list_1 active">
                <div class="box_block_border product_images">
                    <div class="box_inline image">
                        <ul class="sortable list product_images box_inline" >

                            @php($galery = json_decode($product->galery, true))

                            @if(!empty($galery))
                                @foreach($galery as $img_key => $image)
                                    <li class="ui-state-default image_box box_inline" data_id="{{ $img_key }}" >
                                        <img src="{{ $image['image400pxUrl'] }}" />

                                        <div class="fa_box">
                                            <form class="image_delete" action="{{ route('image_delete', ['image_to' => 'product', 'id' => $product->id, 'img_key' => $img_key]) }}"
                                                  method="POST" >
                                                @csrf
                                                <span class="fa fa-trash">
                                                <input  type="submit" name="delete_image" value="">
                                            </span>

                                            </form>
                                        </div>
                                    </li>
                                @endforeach
                            @endif

                        </ul>

                        <div class="image_box box_inline">

                            <div class="fa_box">
                                <span class="fa fa-plus-square"></span>
                            </div>
                        </div>
                        <form class="image_download hidden" action="{{ route('image_download', ['image_to' => 'product', 'id' => $product->id]) }}"
                              method="POST" enctype="multipart/form-data">
                            <span class="close"></span>
                            @csrf
                            <input type="file" name="image">
                            <input type="submit" value="загрузить">
                        </form>
                        <form class="save_sortable" action="{{ route('save_sortable') }}" method="GET" data_name="product_images">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}" />
                            <input type="hidden" name="name" value="product_galery" />
                            <div class="form_append"></div>
                            <input type="submit" name="event_sortable" value="сохранить сортировку" />
                        </form>
                    </div>
                </div>
                <div class="box_block_border">
                    <form action="{{ route('product_save', ['product' => $product, 'mode' => 'general']) }}" method="POST">
                        @csrf
                        <p>
                            Ярлык: <input class="name" type="text" name="name" value="{{ $product->name }}">
                            sku: <input type="text" name="sku" value="{{ $product->sku }}"> <br>
                            стоимость: <input type="number" name="price" value="{{ $product->price }}">
                            сравнить с ценой: <input type="number" name="compareToPrice" value="{{ $product->compareToPrice }}">
                            <br>
                            Url: <input class="name @error('slag') is-invalid @enderror" type="text" name="slag" value="{{ $product->slag }}">
                            <br><input type="submit" value="сохранить">
                        </p>
                        <p>
                            <span class="box_inline border">
                                Статус:
                                 @if ($product->enabled == 1)
                                    <input type="checkbox" name="enabled" value="1" checked> вкл
                                @else
                                    <input type="checkbox" name="enabled" value="1" > откл
                                @endif
                            </span>
                            <span class="box_inline border">
                                Наличие на складе: <br>

                                @if ($product->unlimited == 1)
                                    <input type="radio" name="unlimited" value="1" checked> не ограничено
                                    <br>
                                    <input type="radio" name="unlimited" value="0" > вкл
                                @else
                                    <input type="radio" name="unlimited" value="1" > не ограничено
                                    <br>
                                    <input type="radio" name="unlimited" value="0" checked> вкл
                                @endif

                                    <input type="number" name="count" value="{{ $product->count }}"> шт
                            </span>

                            <span class="box_inline border">
                                главная категория:
                                <select name="category_id">
                                    <option value="0">нет категории</option>
                                    @foreach($categories as $category)
                                        @if($category->id != $product->category_id)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @else
                                            <option value="{{ $category->id }}" selected>{{ $category->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                категории:
                                <select name="categories[]" multiple >
                                    @foreach($categories as $category)
                                        @if($category->id != $product->category_id)
                                            @if (!isset($prod_categories_map[$category->id]))
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @else
                                                <option value="{{ $category->id }}" selected>{{ $category->name }}</option>
                                            @endif
                                        @endif
                                    @endforeach
                                </select>
                            </span>

                        </p>
                        <hr>
                        @php($translite = json_decode($product->translate, true))
                        <div>
                            <b>Язык: en </b>
                            <br>
                            <p>
                                Название:
                                @isset($translite['nameTranslated']['en'])
                                    <input type="text" class="name" name="translate[nameTranslated][en]" value="{{ $translite['nameTranslated']['en'] }}">
                                @else
                                    <input type="text" class="name" name="translate[nameTranslated][en]" value="">
                                @endif
                            </p>

                            Описание: <br>
                            @isset($translite['descriptionTranslated']['en'])
                                <textarea name="translate[descriptionTranslated][en]">{{ $translite['descriptionTranslated']['en'] }}</textarea>
                            @else
                                <textarea name="translate[descriptionTranslated][en]"></textarea>
                            @endif
                        </div>
                        <hr>
                        <div>
                            <b>Язык: ru </b>
                            <br>
                            <p>
                                Название:
                                @isset($translite['nameTranslated']['ru'])
                                    <input type="text" class="name" name="translate[nameTranslated][ru]" value="{{ $translite['nameTranslated']['ru'] }}">
                                @else
                                    <input type="text" class="name" name="translate[nameTranslated][ru]" value="">
                                @endif
                            </p>

                            Описание: <br>
                            @isset($translite['descriptionTranslated']['ru'])
                                <textarea name="translate[descriptionTranslated][ru]">{{ $translite['descriptionTranslated']['ru'] }}</textarea>
                            @else
                                <textarea name="translate[descriptionTranslated][ru]"></textarea>
                            @endif
                        </div>
                        <hr>
                        <div>
                            <b>Язык: he </b>
                            <br>
                            <p>
                                Название:
                                @isset($translite['nameTranslated']['he'])
                                    <input type="text" class="name" name="translate[nameTranslated][he]" value="{{ $translite['nameTranslated']['he'] }}">
                                @else
                                    <input type="text" class="name" name="translate[nameTranslated][he]" value="">
                                @endif
                            </p>

                            Описание: <br>
                            @isset($translite['descriptionTranslated']['he'])
                                <textarea name="translate[descriptionTranslated][he]">{{ $translite['descriptionTranslated']['he'] }}</textarea>
                            @else
                                <textarea name="translate[descriptionTranslated][he]"></textarea>
                            @endif
                        </div>
                        <hr>

                        <input type="submit" value="сохранить">
                    </form>
                </div>


            </div>

            <div class="box_inline content_item box_list_2">
                <div class="options_header">
                    <span class="button add_option">
                        добавить параметр
                    </span>
                    <div class="new_option hidden pop-ap">
                        <div class="body">
                            <span class="close"></span>
                            <div class="box_inline box_border">
                                options:
                                <ul class="list products_list">
                                    @foreach($products_options as $option)
                                        <li class="products" data_id="{{ $option['id'] }}" data_name="category_{{ $option['id'] }}" >
                                            <a>
                                                {{ $option['name'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>

                            </div>
                            <div class="box_inline content_list">
                                @foreach($products_options as $option)
                                    @php($nameTranslate = json_decode($option['nameTranslate']))

                                    <div class="content_item category_{{ $option['id'] }}" data_name="{{ $option['id'] }}" >
                                        <form action="{{ route('product_save', ['product' => $product, 'mode' => 'option_add']) }}" method="POST">
                                            @csrf
                                            <input hidden name="new_option_id" value="{{ $option['id'] }}" />
                                            {{ $option['name'] }} - type: {{ $option['type'] }}

                                            <p>
                                                <input class="button" type="submit" value="добавить параметр">
                                            </p>
                                        </form>
                                    </div>
                                @endforeach
                            </div>


                        </div>
                    </div>

                </div>
                @php($options = json_decode($product->options, true))

                @if (!empty($options))

                    <div class="box_inline box_list">
                        <ul class="list options_list">
                            @foreach($options as $k => $option)
                                @php($option_id = $option['options_id'])
                                @php($option_data = $products_options[$option_id])
                                <li data_name="option_{{ $option_data['id'] }}" data_name="{{ $option_data['id'] }}">
                                    <a >{{ $option_data['name'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="box_inline content_list">
                        <form class="" action="{{ route('product_save', ['product' => $product, 'mode' => 'options']) }}" method="POST">
                            @csrf
                            @foreach($options as $k => $option)
                                @php($option_id = $option['options_id'])
                                @php($option_data = $products_options[$option_id])
                                @php($nameTranslate = json_decode($option_data['nameTranslate'], true))
                                <div class="content_item option_{{ $option_data['id'] }}">
                                    <div class="box_inline box_border">
                                        <input type="hidden" name="options[{{ $k }}][options_id]" value="{{ $option_id }}">
                                        наименование:
                                        {{ $option_data['name'] }}


                                        @if($option_data['type'] == 'TEXT')
                                            <br>
                                            макс кол-во символов
                                            @isset($option['max_size'])
                                                <input type="number" name="options[{{ $k }}][max_size]" value="{{ $option['max_size'] }}">
                                            @else

                                                <input type="number" name="options[{{ $k }}][max_size]" value="20">
                                            @endisset
                                        @endif
                                        <hr>
                                        <p class="opening_box plus">Переводы</p>
                                        <div class="open_box closed">
                                            @foreach($shop_langs as $kl => $lang_data)
                                                @php($name = $nameTranslate[$kl])
                                                <div class="">
                                                    {{ $lang_data['name'] }} -
                                                    {{ $name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="box_inline box_border">
                                        тип опции: {{ $option_data['type'] }}
                                    </div>
                                    <tr class="box_block">
                                        <p>
                                            @if(!empty($option['required']))
                                                <input type="checkbox" name="options[{{ $k }}]['required']" value="true" checked>
                                            @else
                                                <input type="checkbox" name="options[{{ $k }}]['required']" value="true">
                                            @endif
                                            обязательный параметр
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
                                                @if($option_data['type'] != 'TEXT')
                                                    <th>
                                                        метрики
                                                    </th>
                                                @endif
                                                <th>
                                                    модификатор цены
                                                </th>
                                                <th>
                                                    по умолчанию
                                                </th>
                                                <th>

                                                </th>
                                            </tr>
                                            @php($data_option_variants = json_decode($option_data['options'], true))
                                            @if (isset($option['choices']) && !empty($option['choices']))
                                                @php($choices_isset = [])
                                                @foreach($option['choices'] as $kc => $choice)
                                                    @php($key_opt_var = $choice['var_option_id'])
                                                    @php($choice_data = $data_option_variants[$key_opt_var])

                                                    @isset($choice_data['textTranslated'])
                                                        @php($textTranslated = $choice_data['textTranslated'])
                                                    @endisset

                                                    @php($choices_isset[$key_opt_var] = 1)
                                                    <tr>
                                                        <td>
                                                            @isset($choice['variant_number'])
                                                                <input type="hidden" name="options[{{ $k }}][choices][{{ $kc }}][variant_number]" value="{{ $choice['variant_number'] }}">
                                                                <b>
                                                                    variant-{{ $choice['variant_number'] + 1 }}
                                                                </b><br>
                                                            @endisset
                                                            <input hidden name="options[{{ $k }}][choices][{{ $kc }}][var_option_id]" value="{{ $key_opt_var }}">
                                                            значение:
                                                            @isset($choice_data['text'])
                                                                {{ $choice_data['text'] }}
                                                            @endisset
                                                            <hr>
                                                            <p class="opening_box plus">Переводы</p>
                                                            <div class="open_box closed">
                                                                <table>
                                                                    @foreach($shop_langs as $kl => $lang_data)
                                                                        <tr>
                                                                            <td>
                                                                                {{ $lang_data['name'] }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $textTranslated[$kl] }}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </table>
                                                            </div>

                                                        </td>
                                                        <td>
                                                            @isset($choice_data['description'])
                                                                <table>
                                                                    @foreach($shop_langs as $kl => $lang_data)
                                                                        <tr>
                                                                            <td>
                                                                                {{ $lang_data['name'] }}
                                                                            </td>
                                                                            <td>
                                                                                @isset($choice_data['description'][$kl])
                                                                                    {{ $choice_data['description'][$kl] }}
                                                                                @endisset
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </table>
                                                            @endisset
                                                        </td>

                                                        @if($option_data['type'] != 'TEXT')
                                                            <td>
                                                                @isset($choice_data['metrics'])
                                                                    <table>
                                                                        @foreach($choice_data['metrics'] as $metric)
                                                                            @foreach($shop_langs as $kl => $lang_data)
                                                                                <tr>
                                                                                    <td>
                                                                                        {{ $lang_data['name'] }}
                                                                                    </td>
                                                                                    <td>
                                                                                        @isset($metric[$kl])
                                                                                            {{ $metric[$kl] }}
                                                                                        @endisset
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        @endforeach
                                                                    </table>
                                                                @endisset

                                                            </td>
                                                        @endif
                                                        <td>
                                                            <div class="box_inline">


                                                                @isset($choice['priceModifier'])
                                                                    <input type="number" name="options[{{ $k }}][choices][{{ $kc }}][priceModifier]" value="{{ $choice['priceModifier'] }}">
                                                                @else
                                                                    <input type="number" name="options[{{ $k }}][choices][{{ $kc }}][priceModifier]" value="0">
                                                                @endisset
                                                            </div>
                                                            <div class="box_inline">
                                                                @if (isset($choice['priceModifierType']) && $choice['priceModifierType'] == 'PERCENT')
                                                                    <input type="radio" name="options[{{ $k }}][choices][{{ $kc }}][priceModifierType]" value="ABSOLUTE" > $
                                                                    <br>
                                                                    <input type="radio" name="options[{{ $k }}][choices][{{ $kc }}][priceModifierType]" value="PERCENT" checked > %
                                                                @else
                                                                    <input type="radio" name="options[{{ $k }}][choices][{{ $kc }}][priceModifierType]" value="ABSOLUTE" checked > $
                                                                    <br>
                                                                    <input type="radio" name="options[{{ $k }}][choices][{{ $kc }}][priceModifierType]" value="PERCENT" > %
                                                                @endif
                                                            </div>
                                                        </td>

                                                        <td>
                                                            @if(isset($option['defaultChoice']) && $option['defaultChoice'] == $kc)
                                                                <input type="radio" name="options[{{ $k }}][defaultChoice]" value="{{ $kc }}" checked>
                                                            @else
                                                                <input type="radio" name="options[{{ $k }}][defaultChoice]" value="{{ $kc }}" >
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="fa fa-trash button"></span>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                @foreach($data_option_variants as $key_opt_var => $choice_data)

                                                    @isset($choices_isset[$key_opt_var])

                                                        @else
                                                        <tr>
                                                            <td colspan="6">
                                                                <label>
                                                                    <input type="checkbox" name="new_options[{{ $k }}][choices][{{ ++$kc }}][var_option_id]" value="{{ $key_opt_var }}">
                                                                    значение:
                                                                    {{ $choice_data['text'] }}
                                                                </label>

                                                            </td>
                                                        </tr>
                                                    @endisset
                                                @endforeach

                                            @else
                                                @if($option_data['type'] == 'TEXT')
                                                    @php($choice_data = $data_option_variants[0])
                                                    @php($textTranslated = $choice_data['textTranslated'])

                                                    <tr>
                                                        <td>

                                                            <input hidden name="options[{{ $k }}][choices][0][var_option_id]" value="0">
                                                            значение:
                                                            @isset($choice_data['text'])
                                                                {{ $choice_data['text'] }}
                                                            @endisset
                                                            <hr>
                                                            <p class="opening_box plus">Переводы</p>
                                                            <div class="open_box closed">
                                                                <table>
                                                                    @foreach($shop_langs as $kl => $lang_data)
                                                                        <tr>
                                                                            <td>
                                                                                {{ $lang_data['name'] }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $textTranslated[$kl] }}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </table>
                                                            </div>

                                                        </td>
                                                        <td>
                                                            @isset($choice_data['description'])
                                                                <table>
                                                                    @foreach($shop_langs as $kl => $lang_data)
                                                                        <tr>
                                                                            <td>
                                                                                {{ $lang_data['name'] }}
                                                                            </td>
                                                                            <td>
                                                                                @isset($choice_data['description'][$kl])
                                                                                    {{ $choice_data['description'][$kl] }}
                                                                                @endisset
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </table>
                                                            @endisset
                                                        </td>


                                                        <td>
                                                            <div class="box_inline">
                                                                <input type="number" name="options[{{ $k }}][choices][0][priceModifier]" value="0">
                                                            </div>
                                                            <div class="box_inline">
                                                                <input type="radio" name="options[{{ $k }}][choices][0][priceModifierType]" value="ABSOLUTE" checked > $
                                                                <br>
                                                                <input type="radio" name="options[{{ $k }}][choices][0][priceModifierType]" value="PERCENT" > %
                                                            </div>
                                                        </td>

                                                        <td>

                                                        </td>
                                                        <td>

                                                        </td>
                                                    </tr>
                                                @else
                                                    @php($kc = 0)
                                                    @foreach($data_option_variants as $key_opt_var => $choice_data)

                                                        <tr>
                                                            <td colspan="6">
                                                                <label>
                                                                    <input type="checkbox" name="new_options[{{ $k }}][choices][{{ $kc }}][var_option_id]" value="{{ $key_opt_var }}">
                                                                    значение:
                                                                    {{ $choice_data['text'] }}
                                                                </label>

                                                            </td>
                                                        </tr>

                                                        @php($kc++)
                                                    @endforeach
                                                    <tr>
                                                        <td colspan="6">
                                                            <input type="submit" value="добавить">
                                                        </td>
                                                    </tr>
                                                @endif

                                            @endif
                                        </table>


                                </div>

                            @endforeach
                            <p>
                                <input type="submit" value="сохранить">
                            </p>
                        </form>

                    </div>


                @endif
            </div>

            <div class="box_inline content_item box_list_3">
                @php($variables = json_decode($product->variables, true))

                <div class="options_header">
                    <span class="button add_option">
                        добавить вариацию
                    </span>
                    <div class="new_option hidden pop-ap">
                        <div class="body">
                            <span class="close"></span>
                            <p>
                                add variant
                            </p>
                            <form action="{{ route('product_save', ['product' => $product, 'mode' => 'add-variable']) }}" method="POST">
                                @csrf
                                @if (!empty($variables))
                                    @php($kv = array_key_last($variables) + 1)
                                    @php($sk = $kv)
                                @else
                                    @php($kv = 0)
                                    @php($sk = 1)
                                @endif
                                <p>
                                    variant-{{ $kv }}
                                    <input type="hidden" name="kv" value="{{ $kv }}">
                                </p>
                                <p>
                                    sku<b>*</b>:
                                    <input required type="text" name="variables[sku]" value="{{ $product->sku }}-{{ $sk }}">
                                </p>
                                <div>
                                    @if (!empty($options))
                                        @foreach($options as $k => $option)
                                            @php($option_id = $option['options_id'])
                                            <input type="hidden" name="variables[options][{{ $k }}][options_id]" value="{{ $option_id }}" >
                                            @php($option_data = $products_options[$option_id])
                                            @php($nameTranslate = json_decode($option_data['nameTranslate'], true))
                                            @php($data_option_variants = json_decode($option_data['options'], true))
                                            @if ($option_data['type'] != 'TEXT')
                                                <p>
                                                    {{ $option_data['name'] }}
                                                    <select name="variables[options][{{ $k }}][var_option_id]" >
                                                        <option value=""> выбрать параметр </option>
                                                        @foreach($data_option_variants as $ki => $option_item)
                                                            <option value="{{ $ki }}"> {{ $option_item['text'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </p>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                                <input class="button" type="submit">
                            </form>
                        </div>
                    </div>
                </div>
                <form action="{{ route('product_save', ['product' => $product, 'mode' => 'variables']) }}" method="POST">
                    @csrf
                    @if (!empty($variables))
                        <table>
                            <tr>
                                <th>

                                </th>
                                <th>

                                </th>
                                <th>
                                    параметры
                                </th>
                                <th>
                                    артикул
                                </th>
                                <th>
                                    склад
                                </th>
                                <th>
                                    цена
                                </th>
                                <th>

                                </th>
                            </tr>
                            @foreach($variables as $kv => $variant)
                                <tr>
                                    <td>
                                        #{{ $kv + 1 }} <br>
                                        id: {{ $variant['id'] }}
                                        <input type="hidden" name="variables[{{ $kv }}][id]" value="{{ $variant['id'] }}">

                                    </td>
                                    <td>
                                        @php($image = json_decode($product->image, true))

                                        @if(!empty($image))
                                            <img src="{{ $image['image160pxUrl'] }}" />
                                        @endif
                                    </td>
                                    <td>
                                        @isset($variant['options'])
                                            @foreach($variant['options'] as $ko => $option)
                                                @isset($option['options_id'])
                                                    @php($option_id = $option['options_id'])
                                                    @php($option_data = $products_options[$option_id])
                                                    @php($option_data_values = json_decode($option_data['options'], true))
                                                    <input hidden name="variables[{{ $kv }}][options][{{ $ko }}][options_id]" value="{{ $option_id }}">
                                                    {{ $option_data['name'] }}
                                                @endisset
                                                @isset($option['var_option_id'])
                                                    @php($var_option_id = $option['var_option_id'])
                                                    @php($option_value = $option_data_values[$var_option_id])
                                                    <input hidden name="variables[{{ $kv }}][options][{{ $ko }}][var_option_id]" value="{{ $var_option_id }}">
                                                    {{ $option_value['text'] }} test
                                                @else
                                                    <select name="variables[{{ $kv }}][options][{{ $ko }}][var_option_id]">
                                                        <option value="">not value</option>
                                                        @isset($option_data_values)
                                                            @foreach($option_data_values as $kc => $data_value)
                                                                <option value="{{ $kc }}">{{ $data_value['text'] }}</option>
                                                            @endforeach
                                                        @endisset
                                                    </select>
                                                @endisset
                                                <br>

                                            @endforeach
                                        @endisset
                                    </td>
                                    <td>
                                        @isset($variant['sku'])
                                            <input type="text" name="variables[{{ $kv }}][sku]" value="{{ $variant['sku'] }}">
                                        @else
                                            <input required type="text" name="variables[{{ $kv }}][sku]" value="">
                                        @endisset
                                    </td>
                                    <td>

                                        @if ($variant['unlimited'] == 1)
                                            <input type="radio" name="variables[{{ $kv }}][unlimited]" value="1" checked> не ограничено
                                            <br>
                                            <input type="radio" name="variables[{{ $kv }}][unlimited]" value="0" > вкл
                                        @else
                                            <input type="radio" name="variables[{{ $kv }}][unlimited]" value="1" > не ограничено
                                            <br>
                                            <input type="radio" name="variables[{{ $kv }}][unlimited]" value="0" checked> вкл
                                        @endif
                                        @if (!empty($variant['quantity']) && $variant['quantity'] > 0 )
                                            <input type="number" name="variables[{{ $kv }}][quantity]" value="{{ $variant['quantity'] }}"> шт
                                        @else
                                            <input type="number" name="variables[{{ $kv }}][quantity]" value="0"> шт
                                        @endif

                                    </td>
                                    <td>
                                        <input type="text" name="variables[{{ $kv }}][defaultDisplayedPrice]" value="{{ $variant['defaultDisplayedPrice'] }}">
                                        <br>
                                        сравнить с ценой: <br>
                                        @if (isset($variant['compareToPrice']))
                                            <input type="text" name="variables[{{ $kv }}][compareToPrice]" value="{{ $variant['compareToPrice'] }}">
                                        @else
                                            <input type="text" name="variables[{{ $kv }}][compareToPrice]" value="">
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fa fa-trash"></span>
                                    </td>


                                </tr>

                            @endforeach
                        </table>

                        <p>
                            <input type="submit" value="сохранить">
                        </p>
                    @endif

                </form>
            </div>

            <div class="box_inline content_item box_list_4">
                <div class="box_inline box_list">
                    <ul class="list options_list">
                        <li data_name="composition">
                            <a>Состав</a>
                        </li>
                        <li data_name="calories">
                            <a>Калорийность</a>
                        </li>
                    </ul>
                </div>

                <div class="box_inline content_list">
                    <form action="{{ route('product_save', ['product' => $product, 'mode' => 'data']) }}" method="POST">
                        @csrf
                        <div class="content_item composition" >
                            Состав ru: <br>
                            @if (isset($product_data['attributes']['composition']['ru']))
                                <textarea name="data[attributes][composition][ru]">{{ $product_data['attributes']['composition']['ru'] }}</textarea>
                            @else
                                <textarea name="data[attributes][composition][ru]"></textarea>
                            @endif
                            <hr>
                            Состав en: <br>
                            @if (isset($product_data['attributes']['composition']['en']))
                                <textarea name="data[attributes][composition][en]">{{ $product_data['attributes']['composition']['en'] }}</textarea>
                            @else
                                <textarea name="data[attributes][composition][en]"></textarea>
                            @endif
                            <hr>
                            Состав he: <br>
                            @if (isset($product_data['attributes']['composition']['he']))
                                <textarea name="data[attributes][composition][he]">{{ $product_data['attributes']['composition']['he'] }}</textarea>
                            @else
                                <textarea name="data[attributes][composition][he]"></textarea>
                            @endif

                        </div>
                        <div class="content_item calories">
                            Калорийность:
                            @if (isset($product_data['attributes']['calories']))
                                калории <br>
                                <input type="text" name="data[attributes][calories][calories]"
                                       value="{{ $product_data['attributes']['calories']['calories'] }}"> ккал <br>
                                белки <br>
                                <input type="text" name="data[attributes][calories][protein]"
                                       value="{{ $product_data['attributes']['calories']['protein'] }}"> г <br>
                                жиры <br>
                                <input type="text" name="data[attributes][calories][fat]"
                                       value="{{ $product_data['attributes']['calories']['fat'] }}"> г <br>
                                углеводы <br>
                                <input type="text" name="data[attributes][calories][carbohydrate]"
                                       value="{{ $product_data['attributes']['calories']['carbohydrate'] }}"> г <br>
                            @else
                                калории <br>
                                <input type="text" name="data[attributes][calories][calories]"> ккал <br>
                                белки <br>
                                <input type="text" name="data[attributes][calories][protein]"> г <br>
                                жиры <br>
                                <input type="text" name="data[attributes][calories][fat]"> г <br>
                                углеводы <br>
                                <input type="text" name="data[attributes][calories][carbohydrate]"> г <br>
                            @endif
                        </div>

                        <p>
                            <input type="submit" value="сохранить">
                        </p>
                    </form>
                </div>
            </div>

        </div>
    </div>

@stop

