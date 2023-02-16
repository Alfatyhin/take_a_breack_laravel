@extends('layouts.master')

@section('title', 'Скидки по группам')

@section('head')


@stop

@section('sidebar')
    @parent
@stop

@section('content')


    <table>
        <tr>
            <th>id</th>
            <th>название</th>
            <th>купоны</th>
            <th>скидка</th>
            <th>действует для</th>
            <th>генерация купонов</th>
            <th></th>
        </tr>
        @if (!empty($coupons))
            @foreach($coupons as $item)
                @php($discount = json_decode($item->discount, true))
                @php($data = json_decode($item->data, true))
                <tr>
                    <td>
                        {{ $item->id }}
                    </td>
                    <td>
                        {{ $item->name }} -
                        <a class="button" href="{{ route('coupons_discount', ['name' => $item->name]) }}">
                            view
                        </a>
                        <a class="button" href="{{ route('coupons_groups_list', ['coupon_group' => $item]) }}">
                            list
                        </a>
                    </td>
                    <td>
                        @isset($groups_data[$item->id])
                            {{ $groups_data[$item->id]['count'] }}

                        @else
                            0
                        @endisset
                    </td>
                    <td>
                        {{ $discount['value'] }}
                        @if ($discount['mod'] == "ABS")
                            $
                        @else
                            %
                        @endif
                        <br> limit {{ $data['count_limit'] }}
                    </td>
                    <td>

                        {{ $discount['type_mod'] }}

                        @if ($discount['type_mod'] == 'PRODUCT' && isset($discount['prod_id']) && isset($products[$discount['prod_id']]))
                            @php($product = $products[$discount['prod_id']])
                            -
                            <b>
                                {{ $product->name }}
                            </b>
                        @endif
                    </td>

                    <td>
                        <form action="{{ route('coupons_groups_generate', ['coupon_group' => $item]) }}" method="POST">
                            @csrf
                            coupons add:
                            <input type="text" name="count" value="500">
                            <br>
                            <input type="submit" name="add" value="add">
                        </form>
                    </td>
                    <td>
                        {{--                        <a class="fa fa-trash" href="{{ route('coupons_discount', ['delete' => $item->id ]) }}"></a>--}}
                        {{--                        <br>--}}
                        <span class="fa fa-pencil add_option" data_id="{{ $item->id }}"></span>
                        <div class="new_option hidden pop-ap">
                            <div class="body">
                                <span class="close"></span>
                                <form  action="{{ route('coupons_groups_change', ['coupon_group' => $item]) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="discount[type_mod]" value="{{ $discount['type_mod'] }}" checked>
                                    <p>
                                        название: <br>
                                        <input type="text" name="name" size="100" value="{{ $item->name }}">
                                    </p>
                                    @if ($discount['type_mod'] == 'PRODUCT' && isset($discount['prod_id']) && isset($products[$discount['prod_id']]))
                                        <p>
                                            <input type="hidden" name="discount[old_prod_id]" value="{{ $discount['prod_id'] }}">

                                            @php($product = $products[$discount['prod_id']])
                                            <b>
                                                {{ $product->name }}
                                            </b>
                                        </p>
                                    @endif
                                    <div>
                                        @if ($discount['type_mod'] == 'PRODUCT')
                                            <p>

                                                Товары:
                                                <span class="prodicts_list">
                                                        <select class="categories_list" name="product_id">
                                                            @foreach($products as $item)
                                                                <option value="{{ $item['id'] }}">{{ $item['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                </span>

                                            </p>
                                        @endif
                                    </div>
                                    <p>
                                        скидка
                                        <input type="number" name="discount[value]" value="{{ $discount['value'] }}">
                                        тип скидки {{ $discount['type_mod'] }}:
                                        @if ($discount['mod'] == "ABS")
                                            <input type="radio" name="discount[mod]" value="ABS" checked>$
                                            <input type="radio" name="discount[mod]" value="PERSENT" >%
                                        @else
                                            <input type="radio" name="discount[mod]" value="ABS" >$
                                            <input type="radio" name="discount[mod]" value="PERSENT" checked>%
                                        @endif

                                        @isset($data['count_limit'])
                                            <input type="number" name="data[count_limit]" value="{{ $data['count_limit'] }}">
                                        @else
                                            <input type="number" name="data[count_limit]" value="1">
                                        @endisset
                                    </p>

                                    <input type="submit" name="save" value="изменить">
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        @endif

        <tr>
            <th colspan="7">
                добавить группу
            </th>
        </tr>
        <tr>
            <form action="{{ route('coupons_groups') }}" method="POST">
                @csrf
                <td>
                </td>
                <td>

                    <input type="text" name="name">
                </td>
                <td>
                </td>
                <td>
                    скидка
                    <input type="number" name="discount[value]" value="10"> <br>
                    тип скидки: <br>
                    <input type="radio" name="discount[mod]" value="ABS" checked>$
                    <input type="radio" name="discount[mod]" value="PERSENT" >%
                    <br>
                    лимит
                    <input type="number" name="data[count_limit]" value="1" min="0">
                </td>
                <td>
                    скидка на: <br>
                    <label>
                        <input type="radio" name="discount[type_mod]" value="CART" checked>Cart
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="discount[type_mod]" value="PRODUCT" >Product
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="discount[type_mod]" value="DELIVERY" disabled >Delivery
                    </label>
                </td>
                <td>

                </td>
                <td><input type="submit" name="add" value="+"></td>
            </form>
        </tr>
    </table>
@stop

