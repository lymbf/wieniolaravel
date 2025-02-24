@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="text-center project-title">Twoje Projekty</h1>
    
    @if($projects->isEmpty())
        <p class="text-center">Nie masz jeszcze żadnych projektów.</p>
    @else
        <div class="row">
            @foreach($projects as $project)
                <div class="col-md-3 mb-4">
                    <div class="card modern-card text-center">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <h5 class="card-title">{{ $project->name }}</h5>
                        </div>
                        <a href="{{ route('projects.show', $project->id) }}" class="stretched-link"></a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @can('access-admin-panel')
        <div class="text-center mt-4">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                Panel administracyjny
            </a>
        </div>
    @endcan
</div>
@endsection

@push('styles')
<style>
    .project-title {
        font-size: 2.5rem;
        font-weight: bold;
        color: #000;
        margin-bottom: 2rem;
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
    .modern-card .card-body {
        padding: 2rem;
    }
    .modern-card .card-title {
        font-size: 1.5rem;
        margin: 0;
    }
</style>
@endpush
