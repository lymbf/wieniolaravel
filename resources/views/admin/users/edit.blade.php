@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="text-center admin-title">Edycja Użytkownika</h1>

    <div class="mb-4 text-center">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Powrót do Listy Użytkowników</a>
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
            <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label for="name" class="form-label">Nazwa:</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="form-control">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="form-control">
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_admin" id="is_admin" value="1" class="form-check-input" {{ $user->is_admin ? 'checked' : '' }}>
                    <label for="is_admin" class="form-check-label">Administrator</label>
                </div>

                <div class="mb-3">
                    <h3 class="h5">Przypisz projekty</h3>
                    @foreach($allProjects as $project)
                        <div class="form-check">
                            <input type="checkbox" name="projects[]" value="{{ $project->id }}" class="form-check-input" {{ in_array($project->id, $userProjects) ? 'checked' : '' }}>
                            <label class="form-check-label">{{ $project->name }}</label>
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-custom">Zapisz zmiany</button>
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
        color: #000;
        margin-bottom: 1.5rem;
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
    .form-control {
        border-radius: 5px;
        padding: 0.75rem;
        border: 1px solid #ccc;
    }
    /* Dodatkowe style dla formularza checkbox (Bootstrap domyślnie) */
    .form-check-input {
        margin-top: 0.3rem;
    }
</style>
@endpush
