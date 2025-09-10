@props([
    /** @var mixed */
    'columns',
    /** @var mixed */
    'reports',
    /** @var mixed */
    'totals'
])

<table {{ $attributes->class(['w-full']) }}>
    <thead>
    <tr>
        {{--        <th>SL</th>--}}
        @foreach($columns as $column)
            @if(is_array($column))
                <th class="{{$column['class']}}">{{__($column['value'])}}</th>
            @else
                <th>{{__($column)}}</th>
            @endif
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($reports as $index => $report)
        <tr>
            {{--            <td>{{$index + 1}}</td>--}}
            @foreach($columns as $key => $column)
                <td class="@if(is_array($column)){{$column['class']}} @endif @if(\Illuminate\Support\Str::contains($key,'date')) whitespace-nowrap @endif">
                    {{$report[$key]}}
                </td>
            @endforeach
        </tr>
    @endforeach
    @foreach($totals as $total => $value)
        <tr>
            @if(count($columns) > 3)
                <th colspan="{{count($columns) - 3}}"></th>
            @endif
            <th colspan="2">{{$total}}</th>
            <th>
                {{$value}}
            </th>
        </tr>
    @endforeach
    </tbody>
</table>
