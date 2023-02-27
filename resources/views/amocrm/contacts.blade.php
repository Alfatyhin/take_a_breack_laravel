@extends('layouts.master')

@section('title', 'clients doubles')

@section('sidebar')
    @parent
@stop

@section('content')
    <style>
        th, td {
            max-width: 400px;
        }
    </style>
    <div class="">

        <table>
            <tr>
                <th>данные контактов</th>
                <th></th>
            </tr>
            @if(!empty($doubles) )
                @foreach($contacts_data as $contact)
                    <tr>
                        <td>
                            @if (!empty($doubles) && isset($doubles[$contact['id']]))

                               <b>
                                   <a href="https://takebreak.amocrm.ru/contacts/detail/{{ $contact['id'] }}" target="_blank">
                                       id - {{ $contact['id'] }},
                                       name - {{ $contact['name'] }}
                                   </a>
                               </b>
                                @if ($contact['custom_fields_values'])
                                    @foreach($contact['custom_fields_values'] as $item)
                                        @foreach($item['values'] as $value)
                                            <p>
                                                {{ $item['field_name'] }} - {{ $value['value'] }}
                                            </p>
                                        @endforeach
                                    @endforeach
                                @endif
                                <hr>
                                @if (!empty($doubles) && isset($doubles[$contact['id']]['phones']))
                                    @foreach($doubles[$contact['id']]['phones'] as $double)
                                        <b>
                                            <a href="https://takebreak.amocrm.ru/contacts/detail/{{ $double['id'] }}" target="_blank">
                                                id - {{ $double['id'] }},
                                                name - {{ $double['name'] }}
                                            </a>
                                        </b>
                                        @foreach($double['custom_fields_values'] as $item_double)
                                            @foreach($item_double['values'] as $value)
                                                <p>
                                                    {{ $item_double['field_name'] }} - {{ $value['value'] }}
                                                </p>
                                            @endforeach
                                        @endforeach

                                        <hr>
                                    @endforeach
                                @endif

                                @if (!empty($doubles) && isset($doubles[$contact['id']]['emails']))
                                    @foreach($doubles[$contact['id']]['emails'] as $double)
                                        <a href="https://takebreak.amocrm.ru/contacts/detail/{{ $double['id'] }}" target="_blank">
                                            id - {{ $double['id'] }},
                                            name - {{ $double['name'] }}
                                        </a>
                                        @foreach($double['custom_fields_values'] as $item_double)
                                            @foreach($item_double['values'] as $value)
                                                <p>
                                                    {{ $item_double['field_name'] }} - {{ $value['value'] }}
                                                </p>
                                            @endforeach
                                        @endforeach

                                        <hr>
                                    @endforeach
                                @endif

                            @endif
                        </td>
                        <td>
                            <form method="post">
                                @csrf
                                <table>
                                    <tr>
                                        <th>выбрать основной контакт</th>
                                        <th>выбрать контакты для слияния</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            @if (!empty($doubles) && isset($doubles[$contact['id']]))
                                                <label>
                                                    <input type="radio" name="contact" value="{{ $contact['id'] }}" >
                                                    id - {{ $contact['id'] }}
                                                </label>

                                                <hr>
                                                @if (!empty($doubles) && isset($doubles[$contact['id']]['phones']))
                                                    @foreach($doubles[$contact['id']]['phones'] as $double)
                                                        <label>
                                                            <input type="radio" name="contact" value="{{ $double['id'] }}" >
                                                            id - {{ $double['id'] }}
                                                        </label>
                                                        <hr>
                                                    @endforeach
                                                @endif

                                                @if (!empty($doubles) && isset($doubles[$contact['id']]['emails']))
                                                    @foreach($doubles[$contact['id']]['emails'] as $double)
                                                        <label>
                                                            <input type="radio" name="contact" value="{{ $double['id'] }}" >
                                                            id - {{ $double['id'] }}
                                                        </label>
                                                        <hr>
                                                    @endforeach
                                                @endif

                                            @endif
                                        </td>
                                        <td>
                                            @if (!empty($doubles) && isset($doubles[$contact['id']]))

                                                <label>
                                                    <input type="checkbox" name="merge[{{ $contact['id'] }}]" value="{{ $contact['id'] }}" >
                                                    id - {{ $contact['id'] }}
                                                </label>

                                                <hr>
                                                @if (!empty($doubles) && isset($doubles[$contact['id']]['phones']))
                                                    @foreach($doubles[$contact['id']]['phones'] as $double)
                                                        <label>
                                                            <input type="checkbox" name="merge[{{ $double['id'] }}]" value="{{ $double['id'] }}" >
                                                            id - {{ $double['id'] }}
                                                        </label>
                                                        <hr>
                                                    @endforeach
                                                @endif

                                                @if (!empty($doubles) && isset($doubles[$contact['id']]['emails']))
                                                    @foreach($doubles[$contact['id']]['emails'] as $double)
                                                        <label>
                                                            <input type="checkbox" name="merge[{{ $double['id'] }}]" value="{{ $double['id'] }}" >
                                                            id - {{ $double['id'] }}
                                                        </label>
                                                        <hr>
                                                    @endforeach
                                                @endif

                                            @endif
                                        </td>
                                    </tr>
{{--                                    <tr>--}}
{{--                                        <td colspan="2">--}}
{{--                                            <input type="submit" name="mergh" value="выполнить слияние">--}}
{{--                                        </td>--}}
{{--                                    </tr>--}}
                                </table>
                            </form>
                        </td>
                    </tr>

                @endforeach
            @endif
        </table>

        <p>
            page - {{ $page }} проверено {{ $page * 50 }} контактов
            @if($next_page)
                <a class="button" href="{{ route('amocrm_contacts', ['page' => $page++]) }}"> > </a>
            @endif
        </p>

    </div>
@stop

