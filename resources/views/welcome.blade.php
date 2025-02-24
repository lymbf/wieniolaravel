<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Logowanie - Moja Aplikacja</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: #f8f9fa;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .login-container {
            background: linear-gradient(135deg, #444, #000);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
            color: #fff;
        }
        .login-header {
            background: #F1C40F;
            padding: 1rem;
            text-align: center;
        }
        .login-header img {
            max-height: 80px;
        }
        .login-body {
            padding: 2rem;
        }
        .login-body h2 {
            margin-top: 0;
            margin-bottom: 1.5rem;
            text-align: center;
            color: #fff;
        }
        .login-body form {
            display: flex;
            flex-direction: column;
        }
        .login-body input[type="email"],
        .login-body input[type="password"] {
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
        }
        .login-body button {
            padding: 0.75rem;
            background: #F1C40F;
            color: #1c1c1c;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .login-body button:hover {
            background: #FFC107;
        }
        .login-footer {
            text-align: center;
            padding: 1rem;
            background: #222;
        }
        .login-footer a {
            color: #F1C40F;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <!-- Upewnij się, że ścieżka do logo jest poprawna -->
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </div>
        <div class="login-body">
            <h2>Logowanie</h2>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="email" name="email" placeholder="Adres e-mail" required autofocus>
                <input type="password" name="password" placeholder="Hasło" required>
                <button type="submit">Zaloguj się</button>
            </form>
        </div>
        <div class="login-footer">
            <p><a href="{{ route('password.request') }}">Zapomniałeś hasła?</a></p>
        </div>
    </div>
</body>
</html>
