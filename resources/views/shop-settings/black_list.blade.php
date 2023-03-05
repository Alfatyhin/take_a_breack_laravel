@extends('layouts.master')

@section('title', 'black list ip')


@section('head')

@stop

@section('sidebar')
    @parent
@stop

@section('content')


    <div class="box_inline">
        <form method="POST">
            @csrf
            <table>
                <tr>
                    <td>
                        add to list <br>
                        <input type="text" name="ips[]" value="">
                    </td>
                    <td>

                    </td>
                </tr>

                @if($black_list)

                    @foreach($black_list as $ip => $item)
                        <tr>
                            <td>
                                <input type="hidden" name="ips[]" value="{{ $ip }}">
                                {{ $ip }}
                            </td>
                            <td>
                                <span class="fa fa-trash"></span>
                            </td>
                        </tr>

                    @endforeach
                @endif
            </table>

            <input type="submit" name="save" value="сохранить">
        </form>

    </div>
@stop


