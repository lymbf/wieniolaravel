@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="text-center admin-title">Panel Administracyjny</h1>
    
    <div class="row justify-content-center mt-5">
        <!-- Zarządzanie Użytkownikami -->
        <div class="col-md-6">
            <div class="card modern-card admin-card">
                <div class="card-body">
                    <h2 class="card-title">Zarządzanie Użytkownikami</h2>
                    <p class="card-text">Przeglądaj, edytuj i usuwaj użytkowników.</p>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-custom">Przejdź</a>
                </div>
            </div>
        </div>
        <!-- Zarządzanie Projektami -->
        <div class="col-md-6">
            <div class="card modern-card admin-card">
                <div class="card-body">
                    <h2 class="card-title">Zarządzanie Projektami</h2>
                    <p class="card-text">Przeglądaj, dodawaj i edytuj projekty.</p>
                    <a href="{{ route('admin.projects.index') }}" class="btn btn-custom">Przejdź</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-4">
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">Powrót do Twoich Projektów</a>
    </div>
</div>
@endsection

@push('styles')
<style>
    .admin-title {
        font-size: 2.5rem;
        font-weight: bold;
        color: #000;
        margin-bottom: 2rem;
    }
    .admin-card {
        text-align: center;
        margin-bottom: 1rem;
    }
    .admin-card .card-title {
        font-size: 1.75rem;
        font-weight: bold;
        margin-bottom: 0.75rem;
    }
    .admin-card .card-text {
        font-size: 1rem;
        margin-bottom: 1.25rem;
    }
    .btn-custom {
        background-color: #F1C40F;
        color: #1c1c1c;
        border: none;
        border-radius: 25px;
        padding: 0.75rem 1.25rem;
        font-size: 1rem;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    .btn-custom:hover {
        opacity: 0.9;
    }
    .modern-card {
        /* Gradient od ciemnego szarego do czarnego */
        background: linear-gradient(135deg, #333, #000);
        color: #fff;
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
    }
    .modern-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        border: 2px solid #F1C40F;
    }
</style>
@endpush
