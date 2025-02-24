@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="text-center admin-title">Dodaj nowy projekt</h1>

    <div class="text-center mb-4">
        <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary">Powrót do Listy Projektów</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card modern-card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.projects.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Nazwa projektu:</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="form-control">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Opis projektu:</label>
                    <textarea name="description" id="description" rows="4" class="form-control">{{ old('description') }}</textarea>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-custom">Utwórz projekt</button>
                </div>
            </form>
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
        color: #fff;
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
    .form-label {
        font-weight: 500;
        color: #fff;
    }
    .form-control {
        border-radius: 5px;
        border: 1px solid #ccc;
        padding: 0.75rem;
    }
    .btn-custom {
        background-color: #F1C40F;
        color: #1c1c1c;
        border: none;
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
        font-weight: bold;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    .btn-custom:hover {
        opacity: 0.9;
    }
</style>
@endpush
