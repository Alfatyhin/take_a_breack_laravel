@extends('layouts.master')

@section('title', 'Продукты')

@section('head')

    <script>
        var categories = @json($categories);
        var products = @json($products);
    </script>

@stop

@section('sidebar')
    @parent
@stop

@section('content')


    <div class="box_inline box_list box_border">
        Товары в категории: <br>
        <ul class="list products_list">
            @foreach($categories as $category)
                <li class="products" data_id="{{ $category->id }}" data_name="category_{{ $category->slag }}" >
                    <a>
                        {{ $category->name }}
                    </a>
                </li>
            @endforeach
            @if(!empty($empty_categories))
                <li class="products" data_id="empty_categories" data_name="empty_categories" >
                    <a >
                        без категории
                    </a>
                </li>
            @endif

        </ul>

    </div>
    <div class="box_inline content_list">
        <p>
            <a class="button" href="{{ route('shop_settings_product_create') }}">создать товар</a>
        </p>

        @foreach($categories as $category)
            <div class="content_item category_{{ $category->slag }}" data_name="{{ $category->slag }}" >
                @php($products_category = json_decode($category->products))

                @if (!empty($products_category))
                    @foreach($products_category as $product_id)
                        @isset($products[$product_id])
                            @php($product = $products[$product_id])

                            <div class="product_view ">
                                <div class="box_inline image product">
                                    @php($image = json_decode($product->image, true))

                                    @if(!empty($image))
                                        <img src="{{ $image['image400pxUrl'] }}" />
                                    @endif
                                    <br>

                                </div>
                                <div class="box_inline status_{{ $product->enabled }}">
                                    <h3>Наименование : <b>{{ $product->name }}</b> sku {{ $product->sku }}</h3>
                                    <p>url: /{{ $product->slag }}</p>
                                    <form class="ajax" action="{{ route('product_enabled') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $product_id }}">
                                        Статус:
                                        @if ($product->enabled == 1)
                                            <input class="submit" type="checkbox" name="enabled" value="1" checked> вкл
                                        @else
                                            <input class="submit" type="checkbox" name="enabled" value="1" > откл
                                        @endif

                                        @if ($product->unlimited == 1)
                                            unlimited
                                        @else
                                            в наличии: {{ $product->count }} шт
                                        @endif
                                    </form>

                                </div>
                                <div class="box_inline">
                                    стоимость: <b>{{ $product->price }} </b> <br>
                                    <a class="button" href="{{ route('product_redact', ['product' => $product]) }}">
                                    <span class="fa fa-pencil">

                                    </span>
                                    </a>
                                    <a class="button" href="{{ route('product_delete', ['product' => $product]) }}">
                                    <span class="fa fa-trash">

                                    </span>
                                    </a>
                                </div>
                            </div>
                        @endisset
                    @endforeach
                @endif
            </div>
        @endforeach
        @if(!empty($empty_categories))
            <div class="content_item empty_categories" data_name="empty_categories" >

                @foreach($empty_categories as $product_id)
                    @php($product = $products[$product_id])

                    <div class="product_view status_{{ $product->enabled }}">
                        <div class="box_inline image product">
                            <img src="{{ $product->image }}" />
                            <br>

                        </div>
                        <div class="box_inline">
                            <h3>Наименование : <b>{{ $product->name }}</b> sku {{ $product->sku }}</h3>

                            <form class="ajax"  method="GET">
                                @csrf
                                <input type="hidden" name="id" value="{{ $product_id }}">
                                Статус:
                                @if ($product->enabled == 1)
                                    <input class="submit" type="checkbox" name="enabled" value="{{ $product->enabled }}" checked> вкл
                                @else
                                    <input class="submit" type="checkbox" name="enabled" value="{{ $product->enabled }}" > откл
                                @endif


                                @if ($category->unlimited == 1)
                                    unlimited
                                @else
                                    в наличии: {{ $product->count }} шт
                                @endif
                            </form>

                        </div>
                        <div class="box_inline">
                            стоимость: <b>{{ $product->price }} </b> <br>
                            <a class="button" href="{{ route('product_redact', ['product' => $product]) }}">
                                    <span class="fa fa-pencil">

                                    </span>
                            </a>
                            <a class="button" href="{{ route('product_delete', ['product' => $product]) }}">
                                    <span class="fa fa-trash">

                                    </span>
                            </a>
                        </div>
                    </div>

                @endforeach
            </div>
        @endif

    </div>

@stop

