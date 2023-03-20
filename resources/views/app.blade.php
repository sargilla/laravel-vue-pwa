<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'L10') }}</title>
    {{-- @routes --}}
    @vite(['resources/app.js'])
</head>

<body>
    <div id="app"></div>
</body>

</html>
