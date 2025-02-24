<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Ładowanie Bootstrapa z CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Ładowanie głównego pliku CSS kompilowanego przez Vite -->
    @vite(['resources/css/app.css'])
    @stack('styles')
    <style>
        /* Nowoczesny styl dla sekcji dashboardu */
        #meetings, #questions, #attachments {
            background-color: #2d2d2d; /* ciemny szary */
            color: #F1C40F; /* miodowy żółty */
            border: 2px solid #000; /* czarna ramka */
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        /* Opcjonalnie zmień kolor nagłówków sekcji */
        #meetings h2, #questions h2, #attachments h2 {
            color: #FFC107; /* alternatywny miodowy żółty */
        }
    </style>
</head>
<body>
    <!-- Nagłówek z logo firmy -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo firmy" style="height: 150px;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                        aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('projects.index') }}">Projekty</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('profile.edit') }}">Profil</a>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link nav-link" style="display:inline; padding:0;">Wyloguj się</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Główna treść -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Stopka z danymi kontaktowymi -->
    <footer class="bg-dark text-white mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-6">
                    <h5>NO PROBLEM LUBLIN</h5>
                    <p>Adres: ul. Nałęczowska 30, lok. 201, 20-337 Lublin</p>
                    <p>Telefon: +48 790 778 117</p>
                    <p>Email: kontakt@noproblemlublin.pl</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; {{ date('Y') }} NO PROBLEM LUBLIN. Wszelkie prawa zastrzeżone.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Skrypty Bootstrapa -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
