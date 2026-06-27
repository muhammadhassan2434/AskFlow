<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">

    @routes

    @viteReactRefresh
    @vite('resources/js/app.jsx')

    @inertiaHead
</head>
<body>
    @inertia
</body>
</html>