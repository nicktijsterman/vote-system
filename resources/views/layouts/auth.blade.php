<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8"/>
    <title>{{ config('app.title') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-custom-styling />
</head>
<body class="background--login">
<div id="app" class="container mx-auto min-h-screen flex flex-col items-center justify-center">
    @section('content')@show
</div>
</body>
</html>
