@php use App\Settings\GeneralSettings;use Carbon\Carbon; @endphp
    <!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/x-icon" href="{{app(GeneralSettings::class)->favicon}}">
    <title>@yield('title')</title>
{{--    <link rel="stylesheet" href="{{asset('build/css/report.css')}}">--}}
    @vite('resources/css/report.css')
</head>
<body>
<div class="m-8 p-8 print:m-0 print:p-0 print:rounded-none print:shadow-none">
    {{--  company header here  --}}
    <main class="mt-4 space-y-4 lg:mx-auto text-[10px] md:text-[14px] lg:text-base">
        <div class="flex flex-col justify-center items-center text-center w-32 mx-auto">
            <img src="{{app(App\Settings\GeneralSettings::class)->logo}}" alt="company logo">
        </div>
        <div class="print:hidden text-center">
            <button onclick="window.print()" class="px-4 py-2 border bg-gray-300 rounded">
                {{__('Print')}}
            </button>
        </div>
        <h2 class="text-lg text-center">
            @yield('title')
            @if(isset($from) && isset($to))
                <span class="">
                    <span>({{Carbon::create($from)->format('d-M-Y')}}</span>
                    <span>{{__('To:')}}</span>
                    <span>{{Carbon::create($to)->format('d-M-Y')}})</span>
                </span>
            @endif
        </h2>

        <div class="overflow-auto space-y-4">
            @yield('content')
        </div>
    </main>
    <footer class="mt-16">
        <div class="text-xs mt-4 flex justify-between">
            <span>
            Report generated at: {{now()->format('d-m-Y h:i:s A')}}
        </span>
            <span>
            <span>Powered by: </span>
            <a href="#" class="font-bold">Pigeon Soft</a>
        </span>
        </div>
    </footer>
</div>
</body>
</html>
