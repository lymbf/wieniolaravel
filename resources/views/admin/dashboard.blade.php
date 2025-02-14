@extends('layouts.app')

@section('content')
    <h1>Panel Administracyjny</h1>
    
    <nav>
        <ul>
            <li><a href="{{ route('admin.users.index') }}">Zarządzanie Użytkownikami</a></li>
            <li><a href="{{ route('admin.projects.index') }}">Zarządzanie Projektami</a></li>
        </ul>
    </nav>
    
    <div style="margin-top:20px;">
        <a href="{{ route('projects.index') }}">Powrót do Twoich Projektów</a>
    </div>
@endsection
