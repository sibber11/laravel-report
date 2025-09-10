<table class="w-full {{$table_class}}">
    <thead>
    @if($title)
        <tr>
            <th colspan="100" class="{{$title_class}}">
                {{$title}}
            </th>
        </tr>
    @endif

    @if(!is_array(\Illuminate\Support\Arr::first($headers['data'])))
        <tr>
            @if($has_serial)
                <th class="{{$header_class}}">{{__('SL')}}</th>
            @endif
            @foreach($headers['data'] as $key => $header)
                <th class="{{$header_class}} {{$headers['classes'][$key] ?? ''}}">{{$header}}</th>
            @endforeach
        </tr>
    @else
        @foreach($headers['data'] as $key => $header)
            <tr>
                @if($has_serial && $loop->first)
                    <th class="{{$header_class}}" rowspan="{{count($headers['data'])}}">SL</th>
                @endif
                {{--                @dd($headers)--}}
                @foreach($header as $key2 => $value)
                    @if(is_array($value))
                        <th class="{{$header_class}} "
                            @if(!empty($value['row_span']))rowspan="{{$value['row_span']}}" @endif
                            @if(!empty($value['col_span']))colspan="{{$value['col_span']}}" @endif
                        >{{$value['value'] ?? $value}}</th>
                    @else
                        <th class="{{$header_class}} {{$headers['classes'][$key] ?? ''}}">{{$value}}</th>
                    @endif
                @endforeach
            </tr>
        @endforeach
    @endif
    </thead>
    {{--  Start of body section  --}}
    <tbody>
    @foreach($rows as $key => $row)
        <tr class="{{$row_class}}">
            @if($has_serial)
                <td>{{$key + 1}}</td>
            @endif
            @if(!is_array(\Illuminate\Support\Arr::first($headers['data'])))
                @foreach(array_keys($headers['data']) as $key)
                    <td class="
                    {{$row['class']}}
                    {{$row['classes'][$key] ?? ''}}
                     @if(str_contains($key,'date')) whitespace-nowrap @endif
                     ">
                        {!! $row['data'][$key] ?? '' !!}
                    </td>
                @endforeach
            @else
                @foreach($row as $key => $row_data)
                    @if($key == 'data')
                        @foreach($row_data as $key2 => $value)
                            @if(is_array($value))
                                @foreach($value as $key3 => $value2)
                                    <td class="
                                            {{$row['class']}}
                                            {{data_get($row['classes'], "$key2.$key3")}}
                                             @if(str_contains($key3,'date')) whitespace-nowrap @endif
                                             ">
                                        {!! $value2 !!}
                                    </td>
                                @endforeach
                                @continue
                            @endif
                            <td class="
                                    {{$row['class']}}
                                    {{$row['classes'][$key2] ?? ''}}
                                     @if(str_contains($key2,'date')) whitespace-nowrap @endif
                                     ">
                                {!! $value !!}
                            </td>
                        @endforeach
                    @endif
                @endforeach
            @endif
        </tr>
    @endforeach
    {{--  Start of total section  --}}
    @if(!empty($totals))
        <tr><th colspan="100"></th></tr>
        <tr class="total-row">
            @if($has_serial)
                <td></td>
            @endif
            @if(is_array(\Illuminate\Support\Arr::first($headers['data'])))

                @foreach(\Illuminate\Support\Arr::dot($row['data']) as $key => $header)
                        <th>
                        {{$totals[$key] ?? ''}}
                    </th>
                @endforeach
            @else
                @foreach(array_keys($headers['data']) as $key)
                        <th>
                        {{$totals[$key] ?? ''}}
                    </th>
                @endforeach
            @endif
        </tr>
    @endif
    </tbody>
</table>
