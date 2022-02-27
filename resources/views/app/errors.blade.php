@extends('layouts.master')

@section('title', 'App-Errors')

@section('sidebar')
    @parent
@stop

@section('content')
    <div class="max-w-10xl mx-auto sm:px-10 lg:px-10">


        <table>
            <tr>
                <th>Id</th>
                <th>Date</th>
                <th>error</th>
            </tr>
            @if(isset($appErrors))
                @foreach($appErrors as $item)
                    <tr>
                        <td>
                            <a class="border-bottom" href="https://my.ecwid.com/store/48198100#order:id={{ $item->ecwidId }}&return=orders" target="_blank">
                                {{ $item->id }}
                            </a>

                        </td>
                        <td>
                            {{ $item->created_at }}
                        </td>
                        <td>
                            {{ $item->error }}
                        </td>


                    </tr>
                @endforeach
            @endif
        </table>
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 paginate">
            {{ $appErrors->links() }}
        </div>
    </div>

@stop

