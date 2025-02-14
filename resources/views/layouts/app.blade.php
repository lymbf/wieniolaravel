<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Dodaj swoje style i skrypty -->
</head>
<body>
    <header>
        <!-- Spersonalizowany nagłówek -->
    </header>

    <main>
        @yield('content')
    </main>

    <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Wyloguj się</button>
</form>


    <footer>
        <!-- Spersonalizowana stopka -->
    </footer>
</body>
</html>
