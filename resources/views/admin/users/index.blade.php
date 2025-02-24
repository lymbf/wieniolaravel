@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="text-center admin-title">Panel Administracyjny - Użytkownicy</h1>

    <div class="text-center mb-4">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Powrót do Panelu Administracyjnego</a>
    </div>

    @if(session('status'))
        <div class="alert alert-success text-center">
            {{ session('status') }}
        </div>
    @endif

    <div class="card modern-card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nazwa</th>
                        <th>Email</th>
                        <th>Projekty</th>
                        <th>Dostęp</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td class="text-white">{{ $user->id }}</td>
                            <td class="text-white">{{ $user->name }}</td>
                            <td class="text-white">{{ $user->email }}</td>
                            <td class="text-white">
                                @if($user->projects->isNotEmpty())
                                    <ul class="list-unstyled mb-0 pl-3">
                                        @foreach($user->projects as $project)
                                            <li class="text-white">{{ $project->name }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-white">Brak</span>
                                @endif
                            </td>
                            <td class="text-white">{{ $user->is_admin ? 'Administrator' : 'Użytkownik' }}</td>
                            <td>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-custom">Edytuj</a>
                                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć tego użytkownika?')">Usuń</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .admin-title {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 1.5rem;
        color: #fff;
    }
    .modern-card {
        background: linear-gradient(135deg, #333, #000);
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .modern-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        border: 2px solid #F1C40F;
    }
    .btn-custom {
        background-color: #F1C40F;
        color: #1c1c1c;
        border: none;
        border-radius: 25px;
        padding: 0.375rem 0.75rem;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    .btn-custom:hover {
        opacity: 0.9;
    }
    /* Wymuś biały kolor tekstu w tabeli wewnątrz karty */
    .modern-card table tbody td,
    .modern-card table thead th,
    .modern-card table ul li {
        color: #fff !important;
    }
    /* Opcjonalnie zmień tło nagłówka tabeli */
    .table-dark th {
        background-color: #222;
    }
</style>
@endpush
