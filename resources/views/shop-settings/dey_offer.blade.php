@extends('layouts.master')

@section('title', 'Предложение дня')

@section('head')


    <script src="{{ asset('js/dey_offer.js') }}" defer></script>
    <script>
        var categories = @json($categories);
        var products = @json($products);
    </script>

@stop

@section('sidebar')
    @parent
@stop

@section('content')



    <form action="" method="POST" >
        @csrf
        <h3>
            Предложение дня:
            @if (!empty($dey_offer_data))
                @php($offer_id = $dey_offer_data['id'])
                @php($dey_offer = $products[$offer_id])
                <b>
                    {{ $dey_offer->name }}
                </b>
            @endif
        </h3>
        <p> Слоган для блока: <br>
            ru
            @if (!empty($dey_offer_data['title']['ru']))
                <input type="text" name="title[ru]" value="{{ $dey_offer_data['title']['ru'] }}">
            @else
                <input type="text" name="title[ru]" value="">
            @endif
            <br>
            en
            @if (!empty($dey_offer_data['title']['en']))
                <input type="text" name="title[en]" value="{{ $dey_offer_data['title']['en'] }}">
            @else
                <input type="text" name="title[en]" value="">
            @endif
            <br>
            he
            @if (!empty($dey_offer_data['title']['he']))
                <input type="text" name="title[he]" value="{{ $dey_offer_data['title']['he'] }}">
            @else
                <input type="text" name="title[he]" value="">
            @endif
            <br>
        </p>
        <p> Категории:

            <select class="categories_list" >
                @foreach($categories as $item)
                    <option value="{{ $item['id'] }}">{{ $item['name'] }}</option>
                @endforeach
            </select>

            Товары:
            <span class="prodicts_list"></span>
        </p>

        <input class="button" type="submit" value="сохранить">
    </form>

@stop

