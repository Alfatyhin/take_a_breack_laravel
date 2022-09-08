@extends('layouts.master')

@section('title', 'Категории')

@section('head')


    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script>tinymce.init({ selector:'textarea.redact' });</script>

    <script>
        var categories = @json($categories);
        var products = @json($products);
    </script>

    <style>
        .page_box li a.fa {
            width: auto;
        }
    </style>

@stop

@section('sidebar')
    @parent
@stop

@section('content')

    <div class="box_inline box_list box_border">
        <ul class="sortable list categories_list">
            @foreach($categories as $category)
                <li class="ui-state-default category" data_id="{{ $category->id }}"  data_name="category_{{ $category->slag }}" >
                    <a data_id="{{ $category->id }}">
                        {{ $category->name }}
                    </a>
                </li>
            @endforeach
        </ul>
        <form class="save_sortable" action="{{ route('save_sortable') }}" method="GET" data_name="categories_list">
            @csrf
            <input type="hidden" name="name" value="categories" />
            <div class="form_append"></div>
            <input type="submit" name="event_sortable" value="сохранить порядок" />
        </form>
    </div>
    <div class="box_inline content_list">
        <p>
            <a class="button" href="{{ route('shop_settings_category_create') }}">создать категорию</a>
            <a class="button" href="{{ route('shop_settings_category_delete', ['category' => $category]) }}">удалить категорию</a>
        </p>
        <br>

    @foreach($categories as $category)
            <div class="content_item box_inline category_{{ $category->slag }}" data_id="{{ $category->id }}" >
                <div class="content_list_menu">
                    <div>
                        <span class="box_data active" data_name="box_list_1">Основное</span>
                        <span class="box_data" data_name="box_list_2">Товары в категории</span>
                        <span class="box_data" data_name="box_list_3">Дополнительно</span>
                        <span class="box_data" data_name="box_list_4">SEO</span>
                    </div>
                </div>
                <h3>Категория : <b>{{ $category->name }}</b> </h3>
                <div class="content_list">
                    <div class="box_inline content_item box_list box_list_1 active">
                        <div class="box_inline image">
                            @php($image = json_decode($category->image, true))
                            <div class="image_box">
                                @if(!empty($image))
                                    <img src="{{ $image['image400pxUrl'] }}" />
                                @else
                                @endif
                                <div class="fa_box">
                                    <span class="fa fa-plus-square"></span>
                                </div>
                            </div>

                            <br>
                            <form class="image_download hidden" action="{{ route('image_download', ['image_to' => 'category', 'id' => $category->id]) }}"
                                  method="POST" enctype="multipart/form-data">
                                <span class="close"></span>
                                @csrf
                                <input type="file" name="image">
                                <input type="submit" value="загрузить">
                            </form>

                        </div>
                        <form class="box_inline" action="{{ route('category_category_save') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $category->id }}" />

                            <p>
                                Статус:
                                @if ($category->enabled == 1)
                                    <input type="checkbox" name="enabled" value="1" checked>
                                @else
                                    <input type="checkbox" name="enabled" value="1" >
                                @endif

                                <span class="hidden">
                                     родительская категория:
                                <select name="parent_id">
                                    <option value="0">нет родительской категории</option>
                                    @foreach($categories as $category_parent)
                                        @if($category_parent->id != $category->id)
                                            @if($category->parent_id != $category_parent->id)
                                                <option value="{{ $category_parent->id }}">{{ $category_parent->name }}</option>
                                            @else
                                                <option value="{{ $category_parent->id }}" selected>{{ $category_parent->name }}</option>
                                            @endif

                                        @endif
                                    @endforeach
                                </select>
                                </span>


                                <input type="submit" value="сохранить" />
                            </p>
                            <hr>
                            <p>
                                Ярлык: <input type="text" name="name" value="{{ $category->name }}"> <br>
                                Url: <input type="text" class="@error('slag') is-invalid @enderror" name="slag" value="{{ $category->slag }}">
                            </p>
                            <hr>
                            @php($translite = json_decode($category->translate, true))
                            <div>
                                <b>Язык: en </b>
                                <br>
                                <p>
                                    Название:
                                    @isset($translite['nameTranslated']['en'])
                                        <input type="text" name="translate[nameTranslated][en]" value="{{ $translite['nameTranslated']['en'] }}">
                                    @else
                                        <input type="text" name="translate[nameTranslated][en]" value="">
                                    @endif
                                </p>

                                Описание: <br>
                                @isset($translite['descriptionTranslated']['en'])
                                    <textarea class="redact" name="translate[descriptionTranslated][en]">{{ $translite['descriptionTranslated']['en'] }}</textarea>
                                @else
                                    <textarea class="redact" name="translate[descriptionTranslated][en]"></textarea>
                                @endif
                            </div>
                            <hr>
                            <div>
                                <b>Язык: ru </b>
                                <br>
                                <p>
                                    Название:
                                    @isset($translite['nameTranslated']['ru'])
                                        <input type="text" name="translate[nameTranslated][ru]" value="{{ $translite['nameTranslated']['ru'] }}">
                                    @else
                                        <input type="text" name="translate[nameTranslated][ru]" value="">
                                    @endif
                                </p>

                                Описание: <br>
                                @isset($translite['descriptionTranslated']['ru'])
                                    <textarea class="redact" name="translate[descriptionTranslated][ru]">{{ $translite['descriptionTranslated']['ru'] }}</textarea>
                                @else
                                    <textarea class="redact" name="translate[descriptionTranslated][ru]"></textarea>
                                @endif
                            </div>
                            <hr>
                            <div>
                                <b>Язык: he </b>
                                <br>
                                <p>
                                    Название:
                                    @isset($translite['nameTranslated']['he'])
                                        <input type="text" name="translate[nameTranslated][he]" value="{{ $translite['nameTranslated']['he'] }}">
                                    @else
                                        <input type="text" name="translate[nameTranslated][he]" value="">
                                    @endif
                                </p>

                                Описание: <br>
                                @isset($translite['descriptionTranslated']['he'])
                                    <textarea class="redact" name="translate[descriptionTranslated][he]">{{ $translite['descriptionTranslated']['he'] }}</textarea>
                                @else
                                    <textarea class="redact" name="translate[descriptionTranslated][he]"></textarea>
                                @endif
                            </div>
                            <hr>

                            <input type="submit" value="сохранить" />
                        </form>
                    </div>
                    <div class="box_inline content_item box_list box_list_2">
                        <form action="{{ route('category_products_save') }}" method="POST">
                            @csrf
                            <h3>товары в категории</h3>
                            @if(!empty($category->products))
                                @php($prod_ids = json_decode($category->products))

                                <input type="hidden" name="id" value="{{ $category->id }}" />
                                <ul class="category_products_list sortable">
                                    @foreach($prod_ids as $prod_id)
                                        @isset($products[$prod_id])
                                            <li class="{{ $prod_id }}">
                                                <input type="hidden" name="prod_ids[]" value="{{ $prod_id }}" checked>
                                                <div>
                                                    {{ $products[$prod_id]->sku }}
                                                </div>
                                                <div>
                                                    {{ $products[$prod_id]->name }}
                                                </div>
                                                <div>
                                                    <span class="fa fa-trash-o delete_category_rpoduct" data_id="{{ $prod_id }}"></span>

                                                    <a class="fa fa-pencil" href="{{ route('product_redact', ['product' => $prod_id]) }}"> </a>

                                                </div>
                                            </li>
                                            @else
                                            <li class="{{ $prod_id }}">
                                                <div>
                                                    product id - {{ $prod_id }}
                                                </div>
                                                <div>
                                                    not found
                                                </div>
                                                <div>

                                                </div>
                                            </li>
                                        @endisset
                                    @endforeach
                                </ul>


                            @endif
                            <input type="submit" value="сохранить" />
                        </form>
                    </div>
                    <div class="box_inline content_item box_list box_list_3">
                        @php($category_data = json_decode($category->data, true))
                        <form action="{{ route('category_save_data', ['category' => $category]) }}" method="POST">
                            @csrf
                            <div class="box_border">
                                Инфо блок:
                                @isset($category_data['attributes']['desc_icons'])
                                    <input type="checkbox" name="data[attributes][desc_icons]" value="1" checked> откл
                                @else
                                    <input type="checkbox" name="data[attributes][desc_icons]" value="1" > вкл
                                @endisset
                                <input type="submit" value="сохранить">
                                <br>
                                @include("shop.ru.desc_slider")
                            </div>
                            <div class="box_border">
                                Хранение <br>
                                язык ru: <br>
                                @if (isset($category_data['attributes']['keeping']['ru']))
                                    <textarea class="redact" name="data[attributes][keeping][ru]">{{ $category_data['attributes']['keeping']['ru'] }}</textarea>
                                @else
                                    <textarea class="redact" name="data[attributes][keeping][ru]"></textarea>
                                @endif
                                <hr>
                                язык en: <br>
                                @if (isset($category_data['attributes']['keeping']['en']))
                                    <textarea class="redact" name="data[attributes][keeping][en]">{{ $category_data['attributes']['keeping']['en'] }}</textarea>
                                @else
                                    <textarea class="redact" name="data[attributes][keeping][en]"></textarea>
                                @endif
                                <hr>
                                язык he: <br>
                                @if (isset($category_data['attributes']['keeping']['he']))
                                    <textarea class="redact" name="data[attributes][keeping][he]">{{ $category_data['attributes']['keeping']['he'] }}</textarea>
                                @else
                                    <textarea class="redact" name="data[attributes][keeping][he]"></textarea>
                                @endif
                                <hr>
                            </div>

                            <p>
                                <input type="submit" value="сохранить">
                            </p>
                        </form>
                    </div>
                    <div class="box_inline content_item box_list box_list_4">
                        <form action="{{ route('category_save_data', ['category' => $category]) }}" method="POST">
                            @csrf
                            <div class="content_list_menu">
                                <div>
                                    <span class="box_data active" data_name="box_list_title">Title</span>
                                    <span class="box_data" data_name="box_list_description">Description</span>
                                    <span class="box_data" data_name="box_list_keywords">Keywords</span>
                                </div>
                            </div>
                            <div class="content_list">
                                <div class="box_inline content_item box_list box_list_title active">
                                    <div class="box_border">
                                        <h3>Title</h3>
                                        язык ru: <br>
                                        @if (isset($category_data['seo']['title']['ru']))
                                            <input type="text" size="70"  name="data[seo][title][ru]" value="{{ $category_data['seo']['title']['ru'] }}">
                                        @else
                                            <input type="text" size="70" class="" name="data[seo][title][ru]">
                                        @endif
                                        <hr>
                                        язык en: <br>
                                        @if (isset($category_data['seo']['title']['en']))
                                            <input type="text" size="70"  name="data[seo][title][en]" value="{{ $category_data['seo']['title']['en'] }}">
                                        @else
                                            <input type="text" size="70"  name="data[seo][title][en]">
                                        @endif
                                        <hr>
                                        язык he: <br>
                                        @if (isset($category_data['seo']['title']['he']))
                                            <input type="text" size="70"  name="data[seo][title][he]" value="{{ $category_data['seo']['title']['he'] }}">
                                        @else
                                            <input type="text" size="70"  name="data[seo][title][he]">
                                        @endif
                                    </div>
                                </div>

                                <div class="box_inline content_item box_list box_list_description">
                                    <div class="box_border">
                                        <h3>Description</h3>
                                        язык ru: <br>
                                        @if (isset($category_data['seo']['description']['ru']))
                                            <textarea class="" name="data[seo][description][ru]">{{ $category_data['seo']['description']['ru'] }}</textarea>
                                        @else
                                            <textarea class="" name="data[seo][description][ru]"></textarea>
                                        @endif
                                        <hr>
                                        язык en: <br>
                                        @if (isset($category_data['seo']['description']['en']))
                                            <textarea class="" name="data[seo][description][en]">{{ $category_data['seo']['description']['en'] }}</textarea>
                                        @else
                                            <textarea class="" name="data[seo][description][en]"></textarea>
                                        @endif
                                        <hr>
                                        язык he: <br>
                                        @if (isset($category_data['seo']['description']['he']))
                                            <textarea class="" name="data[seo][description][he]">{{ $category_data['seo']['description']['he'] }}</textarea>
                                        @else
                                            <textarea class="" name="data[seo][description][he]"></textarea>
                                        @endif
                                    </div>
                                </div>

                                <div class="box_inline content_item box_list box_list_keywords">
                                    <div class="box_border">
                                        <h3>Keywords</h3>
                                        язык ru: <br>
                                        @if (isset($category_data['seo']['keywords']['ru']))
                                            <textarea class="" name="data[seo][keywords][ru]">{{ $category_data['seo']['keywords']['ru'] }}</textarea>
                                        @else
                                            <textarea class="" name="data[seo][keywords][ru]"></textarea>
                                        @endif
                                        <hr>
                                        язык en: <br>
                                        @if (isset($category_data['seo']['keywords']['en']))
                                            <textarea class="" name="data[seo][keywords][en]">{{ $category_data['seo']['keywords']['en'] }}</textarea>
                                        @else
                                            <textarea class="" name="data[seo][keywords][en]"></textarea>
                                        @endif
                                        <hr>
                                        язык he: <br>
                                        @if (isset($category_data['seo']['keywords']['he']))
                                            <textarea class="" name="data[seo][keywords][he]">{{ $category_data['seo']['keywords']['he'] }}</textarea>
                                        @else
                                            <textarea class="" name="data[seo][keywords][he]"></textarea>
                                        @endif
                                    </div>
                                </div>
                                <p>
                                    <input type="submit" value="сохранить">
                                </p>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        @endforeach

    </div>
@stop

