@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="text-center admin-title">Panel Administracyjny - Projekty</h1>

    <div class="text-center mb-4">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Powrót do Panelu Administracyjnego</a>
    </div>

    <div class="text-center mb-4">
        <a href="{{ route('admin.projects.create') }}" class="btn btn-custom">Dodaj nowy projekt</a>
    </div>

    <div class="card modern-card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nazwa</th>
                        <th>Opis</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
                        <tr>
                            <td class="text-white">{{ $project->id }}</td>
                            <td class="text-white">{{ $project->name }}</td>
                            <td class="text-white">{{ $project->description }}</td>
                            <td>
                                <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-sm btn-custom">Edytuj</a>
                                <form method="POST" action="{{ route('admin.projects.destroy', $project->id) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć ten projekt?')">Usuń</button>
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
        color: #fff;
        margin-bottom: 1.5rem;
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
    /* Wymuszenie białego koloru tekstu w tabeli wewnątrz karty */
    .modern-card table tbody td,
    .modern-card table thead th {
        color: #fff !important;
    }
    /* Nagłówek tabeli */
    .table-dark th {
        background-color: #222;
    }
</style>
@endpush
