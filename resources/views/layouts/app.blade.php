<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Mantle | @yield('tab-title')</title>

        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        @yield('styles')

    </head>

    <body>
        <main id="main">
            @yield('main')
        </main>
    </body>

    <script type="text/javascript" src="{{ mix('js/app.js') }}"></script>

    @yield('scripts')

</html>
