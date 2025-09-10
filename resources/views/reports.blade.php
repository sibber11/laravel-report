@extends('report::layout')
@section('title', $name)
@section('content')
    @if(isset($tables))
        @foreach($tables as $table)
            {{$table}}
        @endforeach
    @else
        <x-report::report-table :columns="$columns" :reports="$data" :totals="$totals" :serial="$serial ?? false"/>
    @endif

@endsection
