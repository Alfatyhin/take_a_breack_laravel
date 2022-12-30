@extends('layouts.master')

@section('title', 'Заказы')


@section('head')



@stop

@section('sidebar')
    @parent
@stop
@section('left_sidebar')

    @parent
@stop

@section('content')

    <div>
        @if(!empty($tb_data))
            <table>
                @foreach($tb_data as $item)
                    @if($loop->first)
                        <tr>
                            @foreach($item as $k => $v)
                                <th>
                                    {{ $k }}
                                </th>
                            @endforeach
                        </tr>
                    @endif

                    <tr>
                        @foreach($item as $k => $v)
                            <th>
                                {{ $v }}
                            </th>
                        @endforeach
                    </tr>
                @endforeach
            </table>
        @endif
    </div>

    <div>
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 paginate">
            {{ $tb_data->links() }}
        </div>
    </div>

@stop


