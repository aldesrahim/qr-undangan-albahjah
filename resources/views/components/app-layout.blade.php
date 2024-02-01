<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite('resources/css/app.css')

    <title>
        {{ $title ??  config('app.name')}}
    </title>

    {{ $heading ?? '' }}
</head>
<body class="bg-gray-50">
{{ $slot }}
</body>
</html>
