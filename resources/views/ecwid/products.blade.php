
@extends('layouts.orders')

@section('title', 'Ecwid Products')

@section('sidebar')
    @parent

@stop

@section('content')
    <div class="max-w-6xl mx-auto sm:px-6">
        <p> всего - {{ $products['count'] }} товаров </p>
        @if(!empty($products['items']))
            @foreach($products['items'] as $k => $product)
                <div class="product">
                    ({{ $k + 1 }}) <br>


                    @if (!empty($product['thumbnailUrl']))
                    <img class="d-inline" src="{{ $product['thumbnailUrl'] }}"
                          />
                    @endif


                    name - {{ $product['name'] }}
                    <a href="{{ route('ecwid.product', ['id' => $product['id'] ]) }}" >
                        id - {{ $product['id'] }}
                    </a>
                    sku - {{ $product['sku'] }}

                    <pre>


                    </pre>

                    @if(!empty($product['galleryImages']))

                        <p>
                            Gallery
                        </p>

                        @foreach($product['galleryImages'] as $image)
                            <img class="d-inline" src="{{ $image['thumbnailUrl'] }}" style="height:150px;" />
                        @endforeach
                    @endif
                    <hr />
                </div>
            @endforeach
            <script>
                var product = @json($product);
                console.log(product);
            </script>
        @endif

    </div>
@stop

